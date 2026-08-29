<?php
require_once dirname(__DIR__, 2) . '/resources/require.php';
require_once 'resources/check_auth.php';

if (!permission_exists('sentinel_setting_edit')) {
    echo "access denied";
    exit;
}

//add multi-lingual support
$language = new text;
$text = $language->get();

//get the domain values
$domain_uuid = $_SESSION['domain_uuid'] ?? '';
$domain_name = $_SESSION['domain_name'] ?? '';

//initialize the settings object (required by header.php - do not reuse this variable name below)
$settings = new settings(['database' => $database, 'domain_uuid' => $domain_uuid]);

// settings with a fixed set of valid values get a dropdown instead of free text -
// this also prevents stray/invisible characters (e.g. from a non-English keyboard)
// from silently breaking an exact string comparison in the apply script.
$sentinel_enum_settings = [
    'default_policy' => [
        'allow' => 'allow - block only explicit Deny rules',
        'deny'  => 'deny - whitelist only, block everything else',
    ],
    'filter_mode' => [
        'ports'     => 'ports - only sip/rtp/web/ssh below',
        'all_ports' => 'all_ports - every port/protocol',
    ],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['setting'] as $name => $value) {
        $database->execute(
            "update v_sentinel_settings set setting_value = :value where setting_name = :name",
            ['value' => trim($value), 'name' => $name]
        );
    }
    message::add('Settings saved. Click Apply Rules Now to activate.');
    header('Location: sentinel_settings.php');
    exit;
}

$sentinel_settings_rows = $database->select("select * from v_sentinel_settings order by setting_name asc", null, 'all');

require_once 'resources/header.php';
?>

<div class="action_bar" id="action_bar">
    <div class="heading"><b>Network Sentinel - Settings</b></div>
    <div class="actions">
        <?php if (permission_exists('sentinel_rule_add')): ?>
            <a href="country_rule_edit.php" class="btn btn-default"><span>Add Rule</span></a>
            <a href="country_bulk_add.php" class="btn btn-default"><span>Bulk Add by Continent</span></a>
        <?php endif; ?>
        <?php if (permission_exists('sentinel_apply')): ?>
            <a href="country_rules.php?action=apply" class="btn btn-default" onclick="return confirm('Apply all enabled rules to iptables now?');">
                <span>Apply Rules Now</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<form method="post" action="sentinel_settings.php">
    <table class="table_form">
    <?php if (is_array($sentinel_settings_rows)) foreach ($sentinel_settings_rows as $s): ?>
        <tr>
            <td class="vncell"><?php echo escape($s['setting_name']); ?></td>
            <td>
                <?php if (isset($sentinel_enum_settings[$s['setting_name']])): ?>
                    <select name="setting[<?php echo escape($s['setting_name']); ?>]">
                        <?php foreach ($sentinel_enum_settings[$s['setting_name']] as $value => $label): ?>
                            <option value="<?php echo escape($value); ?>" <?php echo (trim($s['setting_value']) === $value) ? 'selected' : ''; ?>>
                                <?php echo escape($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <input type="text" name="setting[<?php echo escape($s['setting_name']); ?>]"
                           value="<?php echo escape($s['setting_value']); ?>" style="width: 100%;">
                <?php endif; ?>
                <div class="description"><?php echo escape($s['description']); ?></div>
            </td>
        </tr>
    <?php endforeach; ?>
    </table>
    <br>
    <button type="submit" class="btn btn-default">Save</button>
</form>

<p style="color:#b00; margin-top:15px;">
    Set <b>admin_whitelist_ips</b> to your own management IP(s) before applying deny rules -
    otherwise you can lock yourself out if your IP falls inside a blocked country/range.
</p>

<?php require_once 'resources/footer.php'; ?>
