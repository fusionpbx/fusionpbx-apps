<?php
require_once dirname(__DIR__, 2) . '/resources/require.php';
require_once 'resources/check_auth.php';
require_once 'resources/functions/sentinel_functions.php';

if (!permission_exists('sentinel_rule_add')) {
    echo "access denied";
    exit;
}

//add multi-lingual support
$language = new text;
$text = $language->get();

//get the domain values
$domain_uuid = $_SESSION['domain_uuid'] ?? '';
$domain_name = $_SESSION['domain_name'] ?? '';

//initialize the settings object (required by header.php)
$settings = new settings(['database' => $database, 'domain_uuid' => $domain_uuid]);

$continents = sentinel_continents();
$countries = sentinel_country_list();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected = $_POST['countries'] ?? [];
    $action = $_POST['bulk_action'] ?? 'deny';
    $port_group = $_POST['port_group'] ?? 'all';

    // find existing rule_values already present so we don't create duplicates
    $existing = $database->select("select rule_value from v_sentinel_rules where rule_type = 'country'", null, 'all');
    $existing_codes = [];
    if (is_array($existing)) {
        foreach ($existing as $e) {
            $existing_codes[strtoupper($e['rule_value'])] = true;
        }
    }

    $added = 0;
    foreach ($selected as $code) {
        $code = strtoupper(trim($code));
        if (isset($existing_codes[$code])) {
            continue; // already has a rule, skip - edit it manually from the list if needed
        }
        $params = [
            'sentinel_rule_uuid' => uuid(),
            'rule_type' => 'country',
            'rule_value' => $code,
            'action' => $action,
            'port_group' => $port_group,
            'sort_order' => 200,
            'enabled' => 'true',
            'description' => 'bulk added',
        ];
        $sql = "insert into v_sentinel_rules (sentinel_rule_uuid, rule_type, rule_value, action, port_group, sort_order, enabled, description, insert_date) ";
        $sql .= "values (:sentinel_rule_uuid, :rule_type, :rule_value, :action, :port_group, :sort_order, :enabled, :description, now())";
        $database->execute($sql, $params);
        $added++;
    }

    message::add("Added $added country rule(s). Click Apply Rules Now on the Rules page to activate.");
    header('Location: country_rules.php');
    exit;
}

require_once 'resources/header.php';
?>
<div class="action_bar" id="action_bar">
    <div class="heading"><b>Network Sentinel - Bulk Add Countries</b></div>
</div>

<form method="post" action="country_bulk_add.php">
    <table class="table_form">
        <tr>
            <td class="vncell">Action for all selected</td>
            <td>
                <select name="bulk_action">
                    <option value="deny" selected>Deny</option>
                    <option value="allow">Allow</option>
                </select>
            </td>
        </tr>
        <tr>
            <td class="vncell">Port Group</td>
            <td>
                <select name="port_group">
                    <?php foreach (['all' => 'All ports', 'sip' => 'SIP signalling', 'rtp' => 'RTP media', 'web' => 'Web/admin', 'ssh' => 'SSH'] as $pg => $label): ?>
                        <option value="<?php echo $pg; ?>"><?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>

    <br>
    <div style="margin-bottom: 10px;">
        <button type="button" onclick="document.querySelectorAll('.sentinel-cb').forEach(c => c.checked = true);" class="btn btn-default">Select All</button>
        <button type="button" onclick="document.querySelectorAll('.sentinel-cb').forEach(c => c.checked = false);" class="btn btn-default">Clear All</button>
    </div>

    <?php foreach ($continents as $continent_name => $codes): ?>
        <div class="card" style="margin-bottom: 15px; padding: 10px;">
            <div style="margin-bottom: 6px;">
                <b><?php echo escape($continent_name); ?></b>
                <button type="button" onclick="this.closest('div.card').querySelectorAll('.sentinel-cb').forEach(c => c.checked = true);" class="btn btn-default" style="margin-left: 10px;">Select continent</button>
            </div>
            <?php foreach ($codes as $code): ?>
                <label style="display: inline-block; width: 220px; margin: 2px 0;">
                    <input type="checkbox" class="sentinel-cb" name="countries[]" value="<?php echo $code; ?>">
                    <?php echo escape($countries[$code] ?? $code); ?> (<?php echo $code; ?>)
                </label>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <br>
    <button type="submit" class="btn btn-default">Add Selected Rules</button>
    <a href="country_rules.php" class="btn btn-default">Cancel</a>
</form>

<p style="color:#888; margin-top:15px;">
    This only creates the rules in the database - go back to <b>Sentinel Rules</b> and click
    <b>Apply Rules Now</b> to actually activate them on iptables.
</p>

<?php require_once 'resources/footer.php'; ?>
