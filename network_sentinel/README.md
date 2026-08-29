# Network Sentinel

A FusionPBX app that provides a UI for managing country/IP-based
allow/deny firewall rules on SIP, RTP, web, and SSH ports, built on
`ipset` + `iptables` since `xt_geoip` (used by some older distros) is
not reliably available on modern kernels (Debian 12/13, recent
Ubuntu, etc).

## Features

- Add/edit/delete/enable-disable rules by country (ISO 3166-1 alpha-2
  code) or custom CIDR, each scoped to a chosen action (allow/deny)
  and port group (sip/rtp/web/ssh/all)
- Bulk-add rules by continent, so you don't have to add ~190
  countries one at a time
- Configurable admin IP whitelist, always allowed regardless of any
  other rule, so admins don't lock themselves out
- Two operating modes, set from Sentinel Settings:
  - `allow` (default) - only explicit Deny rules block traffic,
    everything else passes
  - `deny` - whitelist-only mode: only explicit Allow rules let
    traffic through, everything else (new connections) is dropped
- Loopback traffic (127.0.0.0/8) and already-established/related
  connections are always passed through untouched, so internal
  service calls (nginx -> php-fpm, php -> PostgreSQL over
  `127.0.0.1`, etc.) are never affected
- Rules are applied via a root-owned script invoked through a
  narrowly-scoped sudoers rule - the web-facing PHP process
  (`www-data`) can run only that one script, nothing else, as root
- Country IP ranges are sourced from ipdeny.com's bulk zone bundle
  (single download, cached locally, refreshed monthly via cron)
  rather than one HTTP request per country

## Installation

```bash
cp -r network_sentinel /var/www/fusionpbx/app/
chown -R www-data:www-data /var/www/fusionpbx/app/network_sentinel
```

Register the app's schema, defaults, menu entries, and permissions:

```bash
cd /var/www/fusionpbx
php core/upgrade/upgrade.php -s
php core/upgrade/upgrade.php -d
php core/upgrade/upgrade.php -m
php core/upgrade/upgrade.php -p
php core/upgrade/upgrade.php -g
```

Log out/in to see **Network Sentinel** under the Advanced menu.

Install the root-level enforcement script and restrict it to
`www-data` via `sudo`:

```bash
mkdir -p /etc/network_sentinel
chown www-data:www-data /etc/network_sentinel

cp resources/scripts/network_sentinel_apply.sh /usr/local/bin/network_sentinel_apply.sh
chmod 750 /usr/local/bin/network_sentinel_apply.sh
chown root:root /usr/local/bin/network_sentinel_apply.sh

cat > /etc/sudoers.d/network_sentinel <<'EOF'
www-data ALL=(root) NOPASSWD: /usr/local/bin/network_sentinel_apply.sh
EOF
chmod 440 /etc/sudoers.d/network_sentinel

apt install -y ipset curl iptables-persistent
```

## First use

1. Open **Sentinel Settings** and set `admin_whitelist_ips` to your
   own management IP(s) - do this **before** adding any Deny rules or
   switching to whitelist (`deny`) mode, or you can lock yourself
   out.
2. Review `sip_ports`, `rtp_range`, `web_ports`, and `ssh_port` to
   match your actual FreeSWITCH/web server configuration.
3. Add rules from **Sentinel Rules** (or **Sentinel Bulk Add** for
   whole continents at once).
4. Click **Apply Rules Now**.

## Persistence across reboots

The ipset must be rebuilt before iptables tries to reference it on
boot:

```bash
cat > /etc/systemd/system/network-sentinel-restore.service <<'EOF'
[Unit]
Description=Restore Network Sentinel ipset/iptables before netfilter-persistent
Before=netfilter-persistent.service
DefaultDependencies=no

[Service]
Type=oneshot
ExecStart=/usr/local/bin/network_sentinel_apply.sh
RemainAfterExit=yes

[Install]
WantedBy=multi-user.target
EOF

systemctl daemon-reload
systemctl enable network-sentinel-restore.service
netfilter-persistent save
```

## Monthly IP range refresh

```bash
echo "0 4 1 * * root /usr/local/bin/network_sentinel_apply.sh >> /var/log/network_sentinel_apply.log 2>&1" \
  > /etc/cron.d/network_sentinel_apply
```

## Emergency reset

If a rule change locks out legitimate traffic, run as root:

```bash
for i in 1 2 3; do iptables -D INPUT -j SENTINEL_FILTER 2>/dev/null; done
iptables -F SENTINEL_FILTER
```

This removes the enforcement chain immediately without touching any
saved rules in the database - fix the rule from the UI, then
**Apply Rules Now** again.

## Troubleshooting: "Apply" reports success but nothing changes

If **Apply Rules Now** returns something like `No rules file...
nothing to do` even though you have enabled rules, the most likely
cause is that your `php-fpm` systemd unit is sandboxed
(`ProtectSystem=strict` or `full`), which makes `/etc` read-only for
that specific service regardless of normal file permissions. Check:

```bash
systemctl cat php*-fpm | grep -iE "ProtectSystem"
```

If it shows `strict` or `full`, add an explicit exception:

```bash
mkdir -p /etc/systemd/system/php8.2-fpm.service.d   # adjust the PHP version
cat > /etc/systemd/system/php8.2-fpm.service.d/network-sentinel.conf <<'EOF'
[Service]
ReadWritePaths=/etc/network_sentinel
EOF
systemctl daemon-reload
systemctl restart php8.2-fpm
```

## Notes

- The app registers itself under the standard "Advanced" FusionPBX
  menu category.
- Only PostgreSQL is targeted currently (`type => uuid` /
  `timestamptz` column types); MySQL/SQLite support would need the
  per-database type breakdown extended in `app_config.php`.
