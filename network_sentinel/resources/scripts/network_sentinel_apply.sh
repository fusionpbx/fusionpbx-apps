#!/bin/bash
# /usr/local/bin/network_sentinel_apply.sh
# Reads /etc/network_sentinel/rules.conf and /etc/network_sentinel/ports.conf (written by the
# FusionPBX network_sentinel module) and rebuilds ipsets + iptables chain.
# Runs as root, invoked by www-data via a restricted sudoers entry.
set -euo pipefail

ETC_DIR="/etc/network_sentinel"
RULES_FILE="${ETC_DIR}/rules.conf"
PORTS_FILE="${ETC_DIR}/ports.conf"
CHAIN="SENTINEL_FILTER"
ZONE_CACHE="${ETC_DIR}/zones"
ZONE_MAX_AGE_DAYS=30

mkdir -p "$ZONE_CACHE"

if [ ! -f "$RULES_FILE" ]; then
    echo "No rules file at $RULES_FILE - nothing to do."
    exit 0
fi

# --- load port settings ---
SIP_PORTS="5060,5061"
RTP_RANGE="16384:32768"
WEB_PORTS=""
SSH_PORT=""
ADMIN_WHITELIST_IPS=""
FILTER_MODE="ports"   # "ports" = only sip/rtp/web/ssh below, "all_ports" = every port/protocol
DEFAULT_POLICY="allow"  # "allow" = block only explicit deny rules, "deny" = allow only explicit allow rules

if [ -f "$PORTS_FILE" ]; then
    # shellcheck disable=SC1090
    source <(sed -E 's/^([A-Z_]+)=(.*)$/\1="\2"/' "$PORTS_FILE")
fi

# --- helper: download a country zone file if missing/stale, return path ---
fetch_zone() {
    local cc_lower
    cc_lower=$(echo "$1" | tr 'A-Z' 'a-z')
    local zfile="${ZONE_CACHE}/${cc_lower}.zone"
    if [ ! -f "$zfile" ] || [ "$(find "$zfile" -mtime +${ZONE_MAX_AGE_DAYS} 2>/dev/null)" ]; then
        curl -s -f --connect-timeout 5 --max-time 15 "https://www.ipdeny.com/ipblocks/data/countries/${cc_lower}.zone" -o "$zfile" || {
            echo "  [!] failed to fetch zone for $1" >&2
            rm -f "$zfile"
            return 1
        }
    fi
    echo "$zfile"
}
export -f fetch_zone
export ZONE_CACHE ZONE_MAX_AGE_DAYS

# --- fetch ALL country zones in ONE bulk download instead of one request per country ---
# (ipdeny rate-limits many concurrent per-country requests; the bundled tarball avoids that entirely)
ALL_ZONES_MARKER="${ZONE_CACHE}/.all-zones-fetched"
if [ ! -f "$ALL_ZONES_MARKER" ] || [ "$(find "$ALL_ZONES_MARKER" -mtime +${ZONE_MAX_AGE_DAYS} 2>/dev/null)" ]; then
    echo "Fetching bulk country zone bundle (one-time / monthly refresh)..."
    if curl -s -f --connect-timeout 10 --max-time 90 \
        "https://www.ipdeny.com/ipblocks/data/countries/all-zones.tar.gz" \
        -o "${ZONE_CACHE}/all-zones.tar.gz"; then
        tar -xzf "${ZONE_CACHE}/all-zones.tar.gz" -C "$ZONE_CACHE" 2>/dev/null
        touch "$ALL_ZONES_MARKER"
        echo "Bulk zone bundle extracted."
    else
        echo "  [!] bulk zone download failed - will fall back to per-country fetches" >&2
    fi
fi

# --- pre-fetch (in parallel) only whatever the bulk bundle didn't already provide ---
COUNTRY_CODES=$(awk -F'|' '$1=="country" {print $2}' "$RULES_FILE" | sort -u)
MISSING_CODES=""
for c in $COUNTRY_CODES; do
    cl=$(echo "$c" | tr 'A-Z' 'a-z')
    [ -f "${ZONE_CACHE}/${cl}.zone" ] || MISSING_CODES="$MISSING_CODES $c"
done
if [ -n "$MISSING_CODES" ]; then
    echo "$MISSING_CODES" | tr ' ' '\n' | xargs -P 10 -I{} bash -c 'fetch_zone "$@" >/dev/null' _ {}
fi

# --- build ipsets: sentinel_allow_new / sentinel_deny_new ---
ipset create sentinel_allow_new hash:net -exist maxelem 500000
ipset create sentinel_deny_new hash:net -exist maxelem 500000
ipset flush sentinel_allow_new
ipset flush sentinel_deny_new

# port-group specific sets so different port groups can have different lists
declare -A PG_ALLOW_SETS PG_DENY_SETS
for pg in sip rtp web ssh all; do
    ipset create "sentinel_allow_${pg}_new" hash:net -exist maxelem 500000
    ipset create "sentinel_deny_${pg}_new" hash:net -exist maxelem 500000
    ipset flush "sentinel_allow_${pg}_new"
    ipset flush "sentinel_deny_${pg}_new"
done

while IFS='|' read -r rule_type rule_value action port_group; do
    [ -z "$rule_type" ] && continue
    setname="sentinel_${action}_${port_group}_new"

    if [ "$rule_type" = "cidr" ]; then
        ipset add "$setname" "$rule_value" -exist
    elif [ "$rule_type" = "country" ]; then
        zfile=$(fetch_zone "$rule_value") || continue
        while read -r net; do
            [ -n "$net" ] && ipset add "$setname" "$net" -exist
        done < "$zfile"
    fi
done < "$RULES_FILE"

# atomically swap all new sets into place
for pg in sip rtp web ssh all; do
    ipset create "sentinel_allow_${pg}" hash:net -exist maxelem 500000
    ipset create "sentinel_deny_${pg}" hash:net -exist maxelem 500000
    ipset swap "sentinel_allow_${pg}_new" "sentinel_allow_${pg}"
    ipset swap "sentinel_deny_${pg}_new" "sentinel_deny_${pg}"
    ipset destroy "sentinel_allow_${pg}_new"
    ipset destroy "sentinel_deny_${pg}_new"
done

# --- rebuild iptables chain ---
iptables -N "$CHAIN" 2>/dev/null || true
iptables -F "$CHAIN"

# NEVER filter loopback traffic - internal services (nginx->php-fpm, php->postgres
# over 127.0.0.1, etc.) must always work regardless of geo rules, or the whole
# app stack can silently hang waiting on a blocked internal connection.
iptables -A "$CHAIN" -i lo -j RETURN
iptables -A "$CHAIN" -s 127.0.0.0/8 -j RETURN

# always allow the admin whitelist first, regardless of country rules
IFS=',' read -ra ADMIN_IPS <<< "$ADMIN_WHITELIST_IPS"
for ip in "${ADMIN_IPS[@]}"; do
    ip=$(echo "$ip" | xargs)
    if [ -n "$ip" ]; then
        iptables -A "$CHAIN" -s "$ip" -j RETURN
    fi
done

# let already-established/related connections continue regardless of policy -
# this matters most in "deny" default mode so ongoing sessions aren't cut off.
iptables -A "$CHAIN" -m state --state ESTABLISHED,RELATED -j RETURN

apply_port_group() {
    local pg=$1
    iptables -A "$CHAIN" -m set --match-set "sentinel_allow_${pg}" src -j RETURN
    iptables -A "$CHAIN" -m set --match-set "sentinel_deny_${pg}" src -m state --state NEW -j DROP
}
for pg in sip rtp web ssh all; do
    apply_port_group "$pg"
done

if [ "$DEFAULT_POLICY" = "deny" ]; then
    # whitelist-only mode: anything not explicitly allowed above gets dropped
    iptables -A "$CHAIN" -m state --state NEW -j DROP
    iptables -A "$CHAIN" -j RETURN
else
    # current default: only explicit deny rules block traffic, everything else passes
    iptables -A "$CHAIN" -j RETURN
fi

# --- bind chain to ports, or to everything if FILTER_MODE=all_ports ---
# remove any previous SENTINEL_FILTER bindings first (from earlier applies / mode switches)
# so we always end up with a single, top-of-chain set of jump rules.
while iptables -C INPUT -j "$CHAIN" 2>/dev/null; do
    iptables -D INPUT -j "$CHAIN"
done
iptables-save | (grep -- "-A INPUT .*-j ${CHAIN}\$" || true) | sed 's/^-A INPUT //' | while read -r rule; do
    # shellcheck disable=SC2086
    iptables -D INPUT $rule 2>/dev/null || true
done
bind() {
    local proto=$1 port=$2
    [ -z "$port" ] && return 0
    iptables -C INPUT -p "$proto" --dport "$port" -j "$CHAIN" 2>/dev/null || \
    iptables -I INPUT 1 -p "$proto" --dport "$port" -j "$CHAIN"
}

if [ "$FILTER_MODE" = "all_ports" ]; then
    # insert once at the top of INPUT, no protocol/port restriction - covers everything
    iptables -C INPUT -j "$CHAIN" 2>/dev/null || iptables -I INPUT 1 -j "$CHAIN"
else
    IFS=',' read -ra SIP_PORT_LIST <<< "$SIP_PORTS"
    for p in "${SIP_PORT_LIST[@]}"; do
        bind udp "$p"
        bind tcp "$p"
    done
    if [ -n "$RTP_RANGE" ]; then bind udp "$RTP_RANGE"; fi
    if [ -n "$WEB_PORTS" ]; then
        IFS=',' read -ra WEB_PORT_LIST <<< "$WEB_PORTS"
        for p in "${WEB_PORT_LIST[@]}"; do bind tcp "$p"; done
    fi
    if [ -n "$SSH_PORT" ]; then bind tcp "$SSH_PORT"; fi
fi

echo "Sentinel rules applied at $(date)."
echo "allow sets: $(for pg in sip rtp web ssh all; do echo -n "$pg=$(ipset list sentinel_allow_${pg} | grep -c '^[0-9]') "; done)"
echo "deny sets:  $(for pg in sip rtp web ssh all; do echo -n "$pg=$(ipset list sentinel_deny_${pg} | grep -c '^[0-9]') "; done)"
