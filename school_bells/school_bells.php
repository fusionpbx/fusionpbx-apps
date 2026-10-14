<?php

require_once dirname(__DIR__, 2) . "/resources/require.php";
require_once "resources/pdo.php";
require_once "resources/check_auth.php";

if (!permission_exists('school_bell_view')) {
	echo "access denied";
	exit;
}

if (empty($_SESSION['school_bell_pause_csrf'])) {
    $_SESSION['school_bell_pause_csrf'] = bin2hex(random_bytes(32));
}
if (empty($_SESSION['school_bell_csrf'])) {
    $_SESSION['school_bell_csrf'] = bin2hex(random_bytes(32));
}
$pause_csrf = $_SESSION['school_bell_pause_csrf'];
$school_bell_csrf = $_SESSION['school_bell_csrf'];

$pause_sql = "SELECT p.school_bell_uuid, p.pause_date FROM v_school_bell_pauses p WHERE p.domain_uuid = :domain_uuid";
$pause_stmt = $db->prepare($pause_sql);
$pause_stmt->execute([':domain_uuid' => $domain_uuid]);
$pauses = [];
foreach ($pause_stmt->fetchAll(PDO::FETCH_ASSOC) as $pause) {
    $pauses[$pause['school_bell_uuid']][$pause['pause_date']] = true;
}

$language = new text;
$text = $language->get();

$sql = "SELECT b.*, (SELECT COUNT(*) FROM v_school_bell_times tc WHERE tc.school_bell_uuid = b.school_bell_uuid AND tc.domain_uuid = b.domain_uuid) AS bell_count ";
$sql .= "FROM v_school_bells b ";
$sql .= "WHERE b.domain_uuid = :domain_uuid ";
$sql .= "ORDER BY b.school_bell_name";

$prep_statement = $db->prepare($sql);
$prep_statement->bindValue(':domain_uuid', $domain_uuid);
$prep_statement->execute();
$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);

function school_bell_value_true($value) {
	return $value === true || $value === 1 || $value === '1' || $value === 't' || $value === 'true';
}

function school_bell_days($row) {
	$map = [
		'Mon' => 'school_bell_monday',
		'Tue' => 'school_bell_tuesday',
		'Wed' => 'school_bell_wednesday',
		'Thu' => 'school_bell_thursday',
		'Fri' => 'school_bell_friday',
		'Sat' => 'school_bell_saturday',
		'Sun' => 'school_bell_sunday'
	];

	$selected = [];

	foreach ($map as $label => $field) {
		if (school_bell_value_true($row[$field] ?? false)) {
			$selected[] = $label;
		}
	}

	return implode(' ', $selected);
}

require_once __DIR__ . '/school_bell_phase3_helpers.php';
$times_stmt = $db->prepare('SELECT school_bell_uuid, school_bell_hour, school_bell_minute FROM v_school_bell_times WHERE domain_uuid=:domain ORDER BY school_bell_hour, school_bell_minute');
$times_stmt->execute([':domain'=>$domain_uuid]); $sb3_times=[];
foreach ($times_stmt->fetchAll(PDO::FETCH_ASSOC) as $t) $sb3_times[$t['school_bell_uuid']][]=$t;
$ex_stmt = $db->prepare("SELECT e.domain_uuid,e.exception_name,e.start_date,e.end_date,e.recurrence_type,e.all_schedules,m.school_bell_uuid FROM v_school_bell_exceptions e LEFT JOIN v_school_bell_exception_schedules m ON m.school_bell_exception_uuid=e.school_bell_exception_uuid AND m.domain_uuid=e.domain_uuid WHERE e.domain_uuid=:domain AND e.enabled=true");
$ex_stmt->execute([':domain'=>$domain_uuid]); $sb3_exceptions=[];
foreach ($ex_stmt->fetchAll(PDO::FETCH_ASSOC) as $e) $sb3_exceptions[$e['domain_uuid']][]=$e;
$sb3_next=[]; $sb3_status=[];
foreach ($result as $schedule) {
    $id=$schedule['school_bell_uuid'];
    $local_today=(new DateTimeImmutable('now',sb3_timezone($schedule['school_bell_timezone'] ?? '')))->format('Y-m-d');
    $reason=sb3_exception_name($sb3_exceptions,$domain_uuid,$id,$local_today);
    $sb3_status[$id]=$reason!==null ? 'Exception: '.$reason : (isset($pauses[$id][$local_today]) ? 'Skipped today' : (($schedule['school_bell_enabled'] ?? '')==='true' ? 'Normal' : 'Disabled'));
    $sb3_next[$id]=sb3_next_bell($schedule,$sb3_times,$pauses,$sb3_exceptions);
}
$paused_today = [];
foreach ($result as $schedule) {
    $tz_name = $schedule['school_bell_timezone'] ?: date_default_timezone_get();
    try { $tz = new DateTimeZone($tz_name); }
    catch (Exception $e) { $tz = new DateTimeZone('UTC'); }
    $today = (new DateTimeImmutable('now', $tz))->format('Y-m-d');
    $paused_today[$schedule['school_bell_uuid']] = isset($pauses[$schedule['school_bell_uuid']][$today]);
}
require_once "resources/header.php";
$document['title'] = "School Bell Schedules";
?>

<style>
.school-bell-header {
	display: flex;
	align-items: flex-start;
	justify-content: space-between;
	gap: 15px;
	margin-bottom: 12px;
}

.school-bell-title {
	font-weight: 600;
	font-size: 16px;
	margin-bottom: 5px;
}

.school-bell-description {
	margin: 0;
}

.school-bell-toolbar {
	display: flex;
	align-items: center;
	justify-content: flex-end;
	gap: 6px;
	flex-wrap: wrap;
}

.school-bell-selection-actions {
	display: none;
	align-items: center;
	gap: 6px;
	flex-wrap: wrap;
}

/* Match the blue FusionPBX action buttons */
.school-bell-action-btn {
	display: inline-block;
	background: #4285f4 !important;
	border: 1px solid #4285f4 !important;
	color: #ffffff !important;
	border-radius: 3px;
	padding: 5px 11px !important;
	font-size: 12px;
	line-height: 1.4;
	cursor: pointer;
	text-decoration: none !important;
	box-shadow: none !important;
}

.school-bell-action-btn:hover,
.school-bell-action-btn:focus {
	background: #2f73db !important;
	border-color: #2f73db !important;
	color: #ffffff !important;
	text-decoration: none !important;
}

 .school-bell-status-paused { font-weight: 600; color: #ae6110; }
.school-bell-table {
	width: 100%;
	border-collapse: collapse;
}

.school-bell-table th,
.school-bell-table td {
	padding: 8px 8px !important;
	vertical-align: middle !important;
	line-height: 1.25 !important;
}

.school-bell-table th:first-child,
.school-bell-table td:first-child {
	width: 36px;
	text-align: center;
}

.school-bell-table tbody tr.school-bell-row {
	cursor: pointer;
}

.school-bell-table tbody tr.school-bell-selected td {
	background: rgba(66, 139, 202, 0.12) !important;
}

.school-bell-table input[type="checkbox"] {
	margin: 0;
	cursor: pointer;
}

.school-bell-count {
	text-align: center;
}

.school-bell-empty {
	padding: 18px 8px !important;
	text-align: center;
}

.school-bell-footer {
	margin-top: 10px;
	font-size: 12px;
}

@media (max-width: 900px) {
	.school-bell-header {
		flex-direction: column;
	}

	.school-bell-toolbar {
		justify-content: flex-start;
	}
}
</style>

<div class="school-bell-header">
	<div>
		<div class="school-bell-title">
			School Bell Schedules (<?php echo count($result); ?>)
		</div>

		<p class="school-bell-description">
			Create schedules with multiple days of the week and multiple bell times.
		</p>
	</div>

	<div class="school-bell-toolbar">
        <input type="button" class="btn school-bell-action-btn" value="History" onclick="window.location='school_bell_history.php'">
        <input type="button" class="btn school-bell-action-btn" value="Exceptions" onclick="window.location='school_bell_exceptions.php'">

		<div id="school-bell-selection-actions" class="school-bell-selection-actions">

			<?php if (permission_exists('school_bell_edit')) { ?>
                <input type="button" class="btn school-bell-action-btn" id="school-bell-pause" value="Skip Today">
                <input type="button" class="btn school-bell-action-btn" id="school-bell-resume" value="Resume Today" style="display:none">
				<input type="button"
					class="btn school-bell-action-btn"
					id="school-bell-test"
					value="Test">

				<input type="button"
					class="btn school-bell-action-btn"
					id="school-bell-edit"
					value="Edit">
			<?php } ?>

			<?php if (permission_exists('school_bell_add')) { ?>
				<input type="button"
					class="btn school-bell-action-btn"
					id="school-bell-copy"
					value="Copy">
			<?php } ?>

			<?php if (permission_exists('school_bell_delete')) { ?>
				<input type="button"
					class="btn school-bell-action-btn"
					id="school-bell-delete"
					value="Delete">
			<?php } ?>

		</div>

		<?php if (permission_exists('school_bell_add')) { ?>
			<input type="button"
				class="btn school-bell-action-btn"
				value="+ Add"
				onclick="window.location='school_bell_edit.php'">
		<?php } ?>

	</div>
</div>

<table class="tr_hover school-bell-table" border="0" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th>
				<input type="checkbox"
				id="school-bell-select-all"
				aria-label="Select all bell schedules">
			</th>
			<th>Schedule</th>
			<th>Days</th>
			<th>Destination</th>
			<th>Recording</th>
			<th>Bell Times</th>
			<th>Enabled</th>
            <th>Today</th>
            <th>Next Bell</th>
			<th>Description</th>
		</tr>
	</thead>

	<tbody>

<?php if (count($result) === 0) { ?>

		<tr>
			<td colspan="10" class="school-bell-empty">
				No bell schedules found.
			</td>
		</tr>

<?php } else { ?>

<?php
$c = 0;
$row_style[0] = "row_style0";
$row_style[1] = "row_style1";

foreach ($result as $row) {

	$id = $row['school_bell_uuid'];
	$days = school_bell_days($row);
?>

		<tr class="school-bell-row"
			data-id="<?php echo escape($id); ?>"
			data-name="<?php echo escape($row['school_bell_name']); ?>"
            data-paused="<?php echo $paused_today[$id] ? '1' : '0'; ?>">

			<td class="<?php echo $row_style[$c]; ?>">
				<input
					type="checkbox"
					class="school-bell-select"
					value="<?php echo escape($id); ?>"
					aria-label="Select <?php echo escape($row['school_bell_name']); ?>">
			</td>

			<td class="<?php echo $row_style[$c]; ?>">
				<a href="school_bell_edit.php?id=<?php echo escape($id); ?>"><?php echo escape($row['school_bell_name']); ?></a>&nbsp;
			</td>

			<td class="<?php echo $row_style[$c]; ?>">
				<?php echo escape($days); ?>&nbsp;
			</td>

			<td class="<?php echo $row_style[$c]; ?>">
				<?php echo escape($row['school_bell_leg_a_data']); ?>&nbsp;
			</td>

			<td class="<?php echo $row_style[$c]; ?>">
				<?php echo escape($row['school_bell_leg_b_data']); ?>&nbsp;
			</td>

			<td class="<?php echo $row_style[$c]; ?> school-bell-count">
				<?php echo (int)$row['bell_count']; ?>
			</td>

			<td class="<?php echo $row_style[$c]; ?>">
				<?php echo escape($row['school_bell_enabled']); ?>&nbsp;
			</td>

            <td class="<?php echo $row_style[$c]; ?>">
                <?php echo escape($sb3_status[$id]); ?>
            </td>
            <td class="<?php echo $row_style[$c]; ?>">
                <?php echo $sb3_next[$id] ? escape($sb3_next[$id]->format('M j, g:i A T')) : '—'; ?>
            </td>
			<td class="<?php echo $row_style[$c]; ?>">
				<?php echo escape($row['school_bell_description']); ?>&nbsp;
			</td>

		</tr>

<?php
	$c = 1 - $c;
}
?>

<?php } ?>

	</tbody>
</table>

<?php if (count($result) > 0) { ?>
	<div class="school-bell-footer">
		Showing 1 to <?php echo count($result); ?> of <?php echo count($result); ?> entries
	</div>
<?php } ?>

<form id="school-bell-pause-form" method="post" action="school_bell_pause.php" style="display:none">
<input type="hidden" name="csrf" value="<?php echo escape($pause_csrf); ?>">
<input type="hidden" name="action" value="">
<input type="hidden" name="schedule_ids" value="">
</form>
<form id="school-bell-delete-form" method="post" action="school_bell_delete.php" style="display:none">
<input type="hidden" name="csrf" value="<?php echo escape($school_bell_csrf); ?>">
<input type="hidden" name="id" value="">
</form>
<form id="school-bell-copy-form" method="post" action="school_bell_copy.php" style="display:none">
<input type="hidden" name="csrf" value="<?php echo escape($school_bell_csrf); ?>">
<input type="hidden" name="id" value="">
</form>
<form id="school-bell-test-form" method="post" action="school_bell_test.php" style="display:none">
<input type="hidden" name="csrf" value="<?php echo escape($school_bell_csrf); ?>">
<input type="hidden" name="id" value="">
</form>
<script>
(function() {
	var rows = document.querySelectorAll('.school-bell-row');
	var actionBar = document.getElementById('school-bell-selection-actions');
	var selectAll = document.getElementById('school-bell-select-all');

	if (selectAll) {
		selectAll.addEventListener('change', function() {
		Array.prototype.forEach.call(rows, function(row) {
			row.querySelector('.school-bell-select').checked = selectAll.checked;
		});
		updateSelection();
		});
	}

	var testButton = document.getElementById('school-bell-test');
	var editButton = document.getElementById('school-bell-edit');
	var copyButton = document.getElementById('school-bell-copy');
	var deleteButton = document.getElementById('school-bell-delete');

    var selectedId = null;
    var selectedName = '';
    var pauseButton = document.getElementById('school-bell-pause');
    var resumeButton = document.getElementById('school-bell-resume');
    var pauseForm = document.getElementById('school-bell-pause-form');
    var selectedIds = [];
    function submitPause(action) {
        if (!selectedIds.length) return;
        if (action === 'pause' && !confirm('Skip all remaining bells today for ' + selectedIds.length + ' selected schedule(s)?')) return;
        if (action === 'resume' && !confirm('Resume today for ' + selectedIds.length + ' selected schedule(s)?')) return;
        pauseForm.querySelector('[name="action"]').value = action;
        pauseForm.querySelector('[name="schedule_ids"]').value = JSON.stringify(selectedIds);
        pauseForm.submit();
    }
    if (pauseButton) pauseButton.addEventListener('click', function(){submitPause('pause');});
    if (resumeButton) resumeButton.addEventListener('click', function(){submitPause('resume');});

    function updateSelection() {
        selectedIds = [];
        var anyPaused = false;
        var anyUnpaused = false;
        Array.prototype.forEach.call(rows, function(row) {
            var checked = row.querySelector('.school-bell-select').checked;
            row.classList.toggle('school-bell-selected', checked);
            if (checked) {
                selectedIds.push(row.getAttribute('data-id'));
                if (row.getAttribute('data-paused') === '1') anyPaused = true;
                else anyUnpaused = true;
            }
        });
        selectedId = selectedIds.length === 1 ? selectedIds[0] : null;
        selectedName = selectedId ? document.querySelector('.school-bell-row.school-bell-selected').getAttribute('data-name') : '';
        actionBar.style.display = selectedIds.length ? 'flex' : 'none';
        if (pauseButton) pauseButton.style.display = anyUnpaused ? '' : 'none';
        if (resumeButton) resumeButton.style.display = anyPaused ? '' : 'none';
        // Test, Edit and Copy are single-schedule actions.
        [testButton, editButton, copyButton].forEach(function(btn) {if (btn) btn.style.display = selectedId ? '' : 'none';});
        // Existing delete handler handles one schedule only.
        if (deleteButton) deleteButton.style.display = selectedId ? '' : 'none';
	// Keep the Select All checkbox synchronized
	if (selectAll) {
		selectAll.checked =
		rows.length > 0 && selectedIds.length === rows.length;

		selectAll.indeterminate =
		selectedIds.length > 0 && selectedIds.length < rows.length;
	}
   }

	Array.prototype.forEach.call(rows, function(row) {

		var checkbox = row.querySelector('.school-bell-select');

		checkbox.addEventListener('click', function(event) {
			event.stopPropagation();
		});

		checkbox.addEventListener('change', function() {
			updateSelection();
		});

		/* Clicking anywhere except the checkbox opens the schedule editor */
		row.addEventListener('click', function(event) {

			if (event.target.classList.contains('school-bell-select')) {
				return;
			}

			var id = row.getAttribute('data-id');

			if (id) {
				window.location = 'school_bell_edit.php?id=' + encodeURIComponent(id);
			}
		});
	});

	if (testButton) {
		testButton.addEventListener('click', function() {

			if (!selectedId) {
				return;
			}

			if (confirm('Ring "' + selectedName + '" now?')) {
                var testForm = document.getElementById('school-bell-test-form');
                testForm.querySelector('[name="id"]').value = selectedId;
                testForm.submit();
			}
		});
	}

	if (editButton) {
		editButton.addEventListener('click', function() {

			if (!selectedId) {
				return;
			}

			window.location = 'school_bell_edit.php?id=' + encodeURIComponent(selectedId);
		});
	}

	if (copyButton) {
		copyButton.addEventListener('click', function() {

			if (!selectedId) {
				return;
			}

			window.schoolBellCopyForm = document.getElementById('school-bell-copy-form');
                window.schoolBellCopyForm.querySelector('[name="id"]').value = selectedId;
                window.schoolBellCopyForm.submit();
		});
	}

	if (deleteButton) {
		deleteButton.addEventListener('click', function() {

			if (!selectedId) {
				return;
			}

			if (confirm('Delete "' + selectedName + '"?')) {
                var deleteForm = document.getElementById('school-bell-delete-form');
                deleteForm.querySelector('[name="id"]').value = selectedId;
                deleteForm.submit();
			}
		});
	}

})();
</script>

<?php require_once "resources/footer.php"; ?>
