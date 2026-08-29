<?php
require_once dirname(__DIR__, 2) . '/resources/require.php';
require_once 'resources/check_auth.php';
require_once 'resources/functions/sentinel_functions.php';

//add multi-lingual support
$language = new text;
$text = $language->get();

//get the domain values
$domain_uuid = $_SESSION['domain_uuid'] ?? '';
$domain_name = $_SESSION['domain_name'] ?? '';

//initialize the settings object (required by header.php)
$settings = new settings(['database' => $database, 'domain_uuid' => $domain_uuid]);

$uuid = $_GET['id'] ?? ($_POST['sentinel_rule_uuid'] ?? '');
$row = [
    'sentinel_rule_uuid' => '',
    'rule_type' => 'country',
    'rule_value' => '',
    'action' => 'deny',
    'port_group' => 'sip',
    'sort_order' => 100,
    'enabled' => true,
    'description' => '',
];

if (!empty($uuid)) {
    $existing = $database->select("select * from v_sentinel_rules where sentinel_rule_uuid = :uuid", ['uuid' => $uuid], 'row');
    if (is_array($existing)) {
        $row = array_merge($row, $existing);
    }
}

// save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $is_new = empty($_POST['sentinel_rule_uuid']);
    if ($is_new && !permission_exists('sentinel_rule_add')) { echo "access denied"; exit; }
    if (!$is_new && !permission_exists('sentinel_rule_edit')) { echo "access denied"; exit; }

    $params = [
        'rule_type' => $_POST['rule_type'],
        'rule_value' => strtoupper(trim($_POST['rule_value'])),
        'action' => $_POST['action'],
        'port_group' => $_POST['port_group'],
        'sort_order' => (int)$_POST['sort_order'],
        'enabled' => isset($_POST['enabled']) ? 'true' : 'false',
        'description' => $_POST['description'],
    ];

    if ($is_new) {
        $params['sentinel_rule_uuid'] = uuid();
        $sql = "insert into v_sentinel_rules (sentinel_rule_uuid, rule_type, rule_value, action, port_group, sort_order, enabled, description, insert_date) ";
        $sql .= "values (:sentinel_rule_uuid, :rule_type, :rule_value, :action, :port_group, :sort_order, :enabled, :description, now())";
    } else {
        $params['sentinel_rule_uuid'] = $_POST['sentinel_rule_uuid'];
        $sql = "update v_sentinel_rules set rule_type = :rule_type, rule_value = :rule_value, action = :action, ";
        $sql .= "port_group = :port_group, sort_order = :sort_order, enabled = :enabled, description = :description, update_date = now() ";
        $sql .= "where sentinel_rule_uuid = :sentinel_rule_uuid";
    }
    $database->execute($sql, $params);
    message::add('Rule saved. Click Apply Rules Now on the list page to activate it.');
    header('Location: country_rules.php');
    exit;
}

require_once 'resources/header.php';
$countries = sentinel_country_list();
?>

<div class="action_bar" id="action_bar">
    <div class="heading"><b><?php echo empty($uuid) ? 'Add' : 'Edit'; ?> Sentinel Rule</b></div>
</div>

<form method="post" action="country_rule_edit.php">
    <input type="hidden" name="sentinel_rule_uuid" value="<?php echo escape($row['sentinel_rule_uuid']); ?>">

    <table class="table_form">
        <tr>
            <td class="vncell">Rule Type</td>
            <td>
                <select name="rule_type" id="rule_type" onchange="document.getElementById('country_select').style.display = (this.value=='country') ? 'block' : 'none'; document.getElementById('cidr_input').style.display = (this.value=='cidr') ? 'block' : 'none';">
                    <option value="country" <?php echo $row['rule_type'] === 'country' ? 'selected' : ''; ?>>Country</option>
                    <option value="cidr" <?php echo $row['rule_type'] === 'cidr' ? 'selected' : ''; ?>>Custom CIDR / IP</option>
                </select>
            </td>
        </tr>
        <tr>
            <td class="vncell">Value</td>
            <td>
                <div id="country_select" style="display: <?php echo $row['rule_type'] === 'country' ? 'block' : 'none'; ?>;">
                    <select name="rule_value_country">
                        <?php foreach ($countries as $code => $name): ?>
                            <option value="<?php echo $code; ?>" <?php echo $row['rule_value'] === $code ? 'selected' : ''; ?>><?php echo escape($name); ?> (<?php echo $code; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <div class="description">Full ISO code list lives in resources/functions/sentinel_functions.php - add more as needed.</div>
                </div>
                <div id="cidr_input" style="display: <?php echo $row['rule_type'] === 'cidr' ? 'block' : 'none'; ?>;">
                    <input type="text" name="rule_value_cidr" value="<?php echo $row['rule_type'] === 'cidr' ? escape($row['rule_value']) : ''; ?>" placeholder="203.0.113.0/24">
                </div>
            </td>
        </tr>
        <tr>
            <td class="vncell">Action</td>
            <td>
                <select name="action">
                    <option value="allow" <?php echo $row['action'] === 'allow' ? 'selected' : ''; ?>>Allow (RETURN)</option>
                    <option value="deny" <?php echo $row['action'] === 'deny' ? 'selected' : ''; ?>>Deny (DROP)</option>
                </select>
            </td>
        </tr>
        <tr>
            <td class="vncell">Port Group</td>
            <td>
                <select name="port_group">
                    <?php foreach (['sip' => 'SIP signalling', 'rtp' => 'RTP media', 'web' => 'Web/admin', 'ssh' => 'SSH', 'all' => 'All ports'] as $pg => $label): ?>
                        <option value="<?php echo $pg; ?>" <?php echo $row['port_group'] === $pg ? 'selected' : ''; ?>><?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td class="vncell">Sort Order</td>
            <td>
                <input type="number" name="sort_order" value="<?php echo (int)$row['sort_order']; ?>" style="width: 80px;">
                <div class="description">Lower runs first. Put "allow" rules (e.g. your own country) with a lower number than "deny" rules.</div>
            </td>
        </tr>
        <tr>
            <td class="vncell">Enabled</td>
            <td><input type="checkbox" name="enabled" <?php echo ($row['enabled'] === 't' || $row['enabled'] === true) ? 'checked' : ''; ?>></td>
        </tr>
        <tr>
            <td class="vncell">Description</td>
            <td><input type="text" name="description" value="<?php echo escape($row['description']); ?>" style="width: 100%;"></td>
        </tr>
    </table>

    <br>
    <button type="submit" class="btn btn-default">Save</button>
    <a href="country_rules.php" class="btn btn-default">Cancel</a>
</form>

<script>
// merge the two possible value fields into rule_value before submit
document.querySelector('form').addEventListener('submit', function(e) {
    var type = document.getElementById('rule_type').value;
    var form = e.target;
    var hidden = document.createElement('input');
    hidden.type = 'hidden';
    hidden.name = 'rule_value';
    hidden.value = type === 'country'
        ? form.rule_value_country.value
        : form.rule_value_cidr.value;
    form.appendChild(hidden);
});
</script>

<?php require_once 'resources/footer.php'; ?>
