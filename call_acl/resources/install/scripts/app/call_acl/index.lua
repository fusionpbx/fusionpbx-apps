-- Check both clid and destination number against ACL

-- connect to the database
require "resources.functions.database_handle"

local dbh = database_handle('system')

local function log(message)
    freeswitch.consoleLog("NOTICE", "[call_acl] "..message.."\n");
end

local function convert_pattern(pattern)
    
    -- Cleanup pattern-related magical characters
    converted_pattern = pattern:gsub("%(", "%%(")
    converted_pattern = converted_pattern:gsub("%)", "%%)")
    converted_pattern = converted_pattern:gsub("%%", "%%%%")
    converted_pattern = converted_pattern:gsub("%.", "%%.")
    converted_pattern = converted_pattern:gsub("%[", "%%[")
    converted_pattern = converted_pattern:gsub("%]", "%%]")
    converted_pattern = converted_pattern:gsub("%+", "%%+")
    converted_pattern = converted_pattern:gsub("%-", "%%-")
    converted_pattern = converted_pattern:gsub("%?", "%%?")

    -- Internal convention x - any digit, * - any number of digits
    converted_pattern = converted_pattern:gsub("x", "%%d")
    converted_pattern = converted_pattern:gsub("%*", ".*")

    return converted_pattern

end

if (session:ready()) then

    local sql = ""

    local source = session:getVariable("caller_id_number")
    local destination = session:getVariable("destination_number")

    if (source == nil or destination == nil) then
        log("Cannot get callerid or destination number")
        return
    end


    if (domain_id == nil) then
        domain_id = session:getVariable("domain_name") or session:getVariable("sip_invite_domain")
    else
        sql = "SELECT call_acl_name, "
        sql = sql .. "call_acl_source, "
        sql = sql .. "call_acl_destination, "
        sql = sql .. "call_acl_action "
        sql = sql .. "FROM v_call_acl "
        sql = sql .. "WHERE domain_uuid = '" .. domain_id .. "' "
        sql = sql .. "AND call_acl_enabled = 'true' "
        sql = sql .. "ORDER BY call_acl_order"
    end

    if (domain_id ~= nil) then
        if (sql == "") then
            sql = "SELECT call_acl_name, "
            sql = sql .. "call_acl_source, "
            sql = sql .. "call_acl_destination, "
            sql = sql .. "call_acl_action "
            sql = sql .. "FROM v_call_acl "
            sql = sql .. "WHERE domain_uuid ="
            sql = sql .. " (SELECT domain_uuid FROM v_domains"
            sql = sql .. " WHERE domain_name = '" .. domain_id .. "'"
            sql = sql .. " AND domain_enabled = 'true') "
            sql = sql .. "AND call_acl_enabled = 'true' "
            sql = sql .. "ORDER BY call_acl_order"
        end

        -- Get all patterns in list. Mainly cause we can't get 
        local patterns = {}
        local pattern_index = 1
        dbh:query(sql, function(row)
            patterns[pattern_index] = row
            pattern_index = pattern_index + 1
        end);

        dbh:release()

        -- Adjust pattern_index as it's 1 more than actual data
        pattern_index = pattern_index - 1

        for i = 1, pattern_index do
            call_acl_name = patterns[i]['call_acl_name']
            call_acl_source = patterns[i]['call_acl_source']
            call_acl_destination = patterns[i]['call_acl_destination']
            call_acl_action = patterns[i]['call_acl_action']

            call_acl_source = convert_pattern(call_acl_source:lower())
            call_acl_destination = convert_pattern(call_acl_destination:lower())

            if (source:find(call_acl_source) and destination:find(call_acl_destination)) then
                log("[" ..source.. "/" .. call_acl_source.. "][" ..destination.. "/" .. call_acl_destination.. "] ACL " .. call_acl_name .. " matched")
                if call_acl_action == 'reject' then
                    log("ACL is reject. Stop process call")
                    session:execute('hangup', "BEARERCAPABILITY_NOTAUTH")
                else 
                    -- We found pattern match and this is allow (means not reject)
                    log("ACL is allow. Stop process ACLs")
                end
                return
            end
        end
    end

    log("ACL processing end. Contunue call")
end