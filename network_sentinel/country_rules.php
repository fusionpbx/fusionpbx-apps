<?php
require_once dirname(__DIR__, 2) . '/resources/require.php';
require_once 'resources/check_auth.php';

if (!permission_exists('sentinel_rule_view')) {
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

require_once 'resources/functions/sentinel_functions.php';

$action = $_GET['action'] ?? ($_POST['bulk_action'] ?? '');
$uuid = $_GET['id'] ?? '';

// single-row toggle enable/disable
if ($action === 'toggle' && !empty($uuid) && permission_exists('sentinel_rule_edit')) {
    $database->execute(
        "update v_sentinel_rules set enabled = not enabled, update_date = now() where sentinel_rule_uuid = :uuid",
        ['uuid' => $uuid]
    );
    message::add($text['message-update'] ?? 'Updated');
}

// single-row delete
if ($action === 'delete' && !empty($uuid) && permission_exists('sentinel_rule_delete')) {
    $database->execute("delete from v_sentinel_rules where sentinel_rule_uuid = :uuid", ['uuid' => $uuid]);
    message::add($text['message-delete'] ?? 'Deleted');
}

// bulk actions (delete / enable / disable) on checked rows
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['selected_rules']) && is_array($_POST['selected_rules'])) {
    $ids = $_POST['selected_rules'];
    $placeholders = [];
    $params = [];
    foreach ($ids as $i => $id) {
        $key = "id$i";
        $placeholders[] = ":$key";
        $params[$key] = $id;
    }
    $in_clause = implode(',', $placeholders);

    if ($action === 'bulk_delete' && permission_exists('sentinel_rule_delete')) {
        $database->execute("delete from v_sentinel_rules where sentinel_rule_uuid in ($in_clause)", $params);
        message::add(count($ids) . ' rule(s) deleted.');
    } elseif ($action === 'bulk_enable' && permission_exists('sentinel_rule_edit')) {
        $database->execute("update v_sentinel_rules set enabled = true, update_date = now() where sentinel_rule_uuid in ($in_clause)", $params);
        message::add(count($ids) . ' rule(s) enabled.');
    } elseif ($action === 'bulk_disable' && permission_exists('sentinel_rule_edit')) {
        $database->execute("update v_sentinel_rules set enabled = false, update_date = now() where sentinel_rule_uuid in ($in_clause)", $params);
        message::add(count($ids) . ' rule(s) disabled.');
    }
}

// apply now
$apply_output = '';
if ($action === 'apply' && permission_exists('sentinel_apply')) {
    [$ok, $apply_output] = sentinel_apply_rules($database);
    message::add($ok ? 'Sentinel rules applied successfully' : 'Apply failed - see output below', $ok ? 'positive' : 'negative');
}

$sql = "select * from v_sentinel_rules order by sort_order asc, rule_value asc";
$rules = $database->select($sql, null, 'all');

require_once 'resources/header.php';
?>

<div class="action_bar" id="action_bar">
    <div class="heading"><b>Network Sentinel - Rules</b></div>
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

<?php if (!empty($apply_output)): ?>
<div class="card" style="margin: 10px 0; padding: 10px;">
    <b>Apply output:</b>
    <pre style="white-space: pre-wrap;"><?php echo escape($apply_output); ?></pre>
</div>
<?php endif; ?>

<form method="post" action="country_rules.php" id="rules_form">
    <div style="margin-bottom: 10px;">
        <button type="submit" name="bulk_action" value="bulk_delete" class="btn btn-default"
                onclick="return confirm('Delete all selected rules? This cannot be undone.');">Delete Selected</button>
        <button type="submit" name="bulk_action" value="bulk_enable" class="btn btn-default">Enable Selected</button>
        <button type="submit" name="bulk_action" value="bulk_disable" class="btn btn-default">Disable Selected</button>
    </div>

    <table class="list">
        <tr class="list-header">
            <th><input type="checkbox" id="select_all" onclick="document.querySelectorAll('.row-cb').forEach(c => c.checked = this.checked);"></th>
            <th>Type</th>
            <th>Value</th>
            <th>Action</th>
            <th>Port Group</th>
            <th>Enabled</th>
            <th>Description</th>
            <th class="right">Edit</th>
        </tr>
    <?php
    if (is_array($rules)) {
        foreach ($rules as $row) {
            $enabled_icon = $row['enabled'] === 't' || $row['enabled'] === true ? 'check' : 'close';
            $action_color = strtolower($row['action']) === 'allow' ? 'green' : 'red';
    ?>
        <tr class="list-row">
            <td><input type="checkbox" class="row-cb" name="selected_rules[]" value="<?php echo escape($row['sentinel_rule_uuid']); ?>"></td>
            <td><?php echo escape($row['rule_type']); ?></td>
            <td><?php echo escape($row['rule_value']); ?></td>
            <td><span style="color: <?php echo $action_color; ?>; font-weight: bold;"><?php echo escape(strtoupper($row['action'])); ?></span></td>
            <td><?php echo escape($row['port_group']); ?></td>
            <td>
                <a href="country_rules.php?action=toggle&id=<?php echo urlencode($row['sentinel_rule_uuid']); ?>">
                    <?php echo $enabled_icon === 'check' ? 'Yes' : 'No'; ?>
                </a>
            </td>
            <td><?php echo escape($row['description']); ?></td>
            <td class="right">
                <a href="country_rule_edit.php?id=<?php echo urlencode($row['sentinel_rule_uuid']); ?>">edit</a>
                &nbsp;|&nbsp;
                <a href="country_rules.php?action=delete&id=<?php echo urlencode($row['sentinel_rule_uuid']); ?>" onclick="return confirm('Delete this rule?');">delete</a>
            </td>
        </tr>
    <?php
        }
    } else {
    ?>
        <tr class="list-row"><td colspan="8">No rules yet - click Add Rule to create one.</td></tr>
    <?php
    }
    ?>
    </table>
</form>

<p style="margin-top: 15px; color: #888;">
    Remember to click <b>Apply Rules Now</b> after adding/editing/deleting - changes are only
    saved to the database until applied, they don't touch iptables automatically.
</p>

<?php require_once 'resources/footer.php'; ?>
