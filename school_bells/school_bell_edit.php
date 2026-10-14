<?php

require_once dirname(__DIR__, 2) . "/resources/require.php";
require_once "resources/pdo.php";
require_once "resources/check_auth.php";
require_once __DIR__ . "/school_bell_security.php";

$request_id = trim((string)($_REQUEST['id'] ?? ''));
if ($request_id !== '' && !is_uuid($request_id)) {
	http_response_code(400);
	exit('Invalid schedule identifier');
}

$action = ($request_id !== '') ? 'update' : 'add';
if (($action === 'update' && !permission_exists('school_bell_edit'))
	|| ($action === 'add' && !permission_exists('school_bell_add'))) {
	http_response_code(403);
	exit('Access denied');
}

if (empty($_SESSION['school_bell_csrf'])) {
	$_SESSION['school_bell_csrf'] = bin2hex(random_bytes(32));
}
$school_bell_csrf = $_SESSION['school_bell_csrf'];

if ($_SERVER['REQUEST_METHOD'] === 'POST'
	&& !hash_equals($school_bell_csrf, (string)($_POST['csrf'] ?? ''))) {
	http_response_code(403);
	exit('Invalid security token');
}

$language = new text;
$text = $language->get();

$school_bell_uuid = ($action === 'update') ? check_str($request_id) : uuid();

$timezone_identifiers_list = timezone_identifiers_list();

$school_bell_name = '';
$school_bell_leg_a_data = '';
$school_bell_leg_b_data = '';
$school_bell_ring_timeout = 5;
$school_bell_timezone = date_default_timezone_get();
$school_bell_enabled = 'true';
$school_bell_description = '';

$days = [
	'monday' => false,
	'tuesday' => false,
	'wednesday' => false,
	'thursday' => false,
	'friday' => false,
	'saturday' => false,
	'sunday' => false
];

$bell_times = [];
$errors = [];

function school_bell_edit_true($value) {
	return $value === true || $value === 1 || $value === '1' || $value === 't' || $value === 'true';
}

if ($action === 'update' && count($_POST) === 0) {

	$sql = "SELECT * FROM v_school_bells ";
	$sql .= "WHERE domain_uuid = :domain_uuid ";
	$sql .= "AND school_bell_uuid = :school_bell_uuid ";
	$sql .= "LIMIT 1";

	$prep_statement = $db->prepare($sql);
	$prep_statement->bindValue(':domain_uuid', $domain_uuid);
	$prep_statement->bindValue(':school_bell_uuid', $school_bell_uuid);
	$prep_statement->execute();
	$row = $prep_statement->fetch(PDO::FETCH_ASSOC);

	if ($row) {
		$school_bell_name = $row['school_bell_name'];
		$school_bell_leg_a_data = $row['school_bell_leg_a_data'];
		$school_bell_leg_b_data = $row['school_bell_leg_b_data'];
		$school_bell_ring_timeout = (int)$row['school_bell_ring_timeout'];
		$school_bell_timezone = $row['school_bell_timezone'];
		$school_bell_enabled = $row['school_bell_enabled'];
		$school_bell_description = $row['school_bell_description'];

		foreach ($days as $day => $unused) {
			$days[$day] = school_bell_edit_true($row['school_bell_'.$day] ?? false);
		}
	}

	$sql = "SELECT school_bell_hour, school_bell_minute ";
	$sql .= "FROM v_school_bell_times ";
	$sql .= "WHERE domain_uuid = :domain_uuid ";
	$sql .= "AND school_bell_uuid = :school_bell_uuid ";
	$sql .= "ORDER BY school_bell_order, school_bell_hour, school_bell_minute";

	$prep_statement = $db->prepare($sql);
	$prep_statement->bindValue(':domain_uuid', $domain_uuid);
	$prep_statement->bindValue(':school_bell_uuid', $school_bell_uuid);
	$prep_statement->execute();

	foreach ($prep_statement->fetchAll(PDO::FETCH_ASSOC) as $time_row) {
		$bell_times[] = sprintf(
			'%02d:%02d',
			(int)$time_row['school_bell_hour'],
			(int)$time_row['school_bell_minute']
		);
	}
}

if (count($_POST) > 0) {

	$school_bell_name = trim((string)($_POST['school_bell_name'] ?? ''));
	$school_bell_leg_a_data = trim((string)($_POST['school_bell_leg_a_data'] ?? ''));
	$school_bell_leg_b_data = trim((string)($_POST['school_bell_leg_b_data'] ?? ''));
	$school_bell_ring_timeout = (int)($_POST['school_bell_ring_timeout'] ?? 5);
	$school_bell_timezone = trim((string)($_POST['school_bell_timezone'] ?? ''));
	$school_bell_enabled = (($_POST['school_bell_enabled'] ?? 'false') === 'true') ? 'true' : 'false';
	$school_bell_description = trim((string)($_POST['school_bell_description'] ?? ''));

	foreach ($days as $day => $unused) {
		$days[$day] = isset($_POST['days'][$day]);
	}

	$bell_times = [];

	foreach (($_POST['bell_times'] ?? []) as $time) {
		$time = trim((string)$time);

		if (preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $time)) {
			$bell_times[$time] = $time;
		}
	}

	$bell_times = array_values($bell_times);
	sort($bell_times);

	if ($school_bell_name === '') {
		$errors[] = 'Schedule Name is required.';
	}
	if ($school_bell_leg_a_data === '') {
		$errors[] = 'Destination is required.';
	}
	if ($school_bell_leg_b_data === '') {
		$errors[] = 'Recording is required.';
	}
	if (!in_array(true, $days, true)) {
		$errors[] = 'Select at least one day of the week.';
	}
	if (count($bell_times) === 0) {
		$errors[] = 'Add at least one bell time.';
	}
	if (!in_array($school_bell_timezone, $timezone_identifiers_list, true)) {
		$errors[] = 'Select a valid timezone.';
	}
	if (!school_bell_valid_destination($school_bell_leg_a_data)) {
		$errors[] = 'Destination contains unsupported characters.';
	}
	if (!school_bell_valid_recording_filename($school_bell_leg_b_data)) {
		$errors[] = 'Recording filename is invalid.';
	}
	else {
		$recording_check = $db->prepare(
			'SELECT 1 FROM v_recordings WHERE domain_uuid = :domain AND recording_filename = :filename LIMIT 1'
		);
		$recording_check->execute([
			':domain' => $domain_uuid,
			':filename' => $school_bell_leg_b_data
		]);
		if (!$recording_check->fetchColumn()) {
			$errors[] = 'Select a recording belonging to this domain.';
		}
	}

	if ($school_bell_ring_timeout < 1) {
		$school_bell_ring_timeout = 5;
	}
	if ($school_bell_ring_timeout > 60) {
		$school_bell_ring_timeout = 60;
	}

	if (count($errors) === 0) {

		$full_path = $_SESSION['switch']['recordings']['dir'].'/'.
			$_SESSION['domain_name'].'/'.
			$school_bell_leg_b_data;

		try {
			$db->beginTransaction();

			if ($action === 'add') {

				$sql = "INSERT INTO v_school_bells (";
				$sql .= "domain_uuid, school_bell_uuid, school_bell_name, ";
				$sql .= "school_bell_leg_a_type, school_bell_leg_a_data, ";
				$sql .= "school_bell_leg_b_type, school_bell_leg_b_data, ";
				$sql .= "school_bell_ring_timeout, school_bell_min, school_bell_hour, ";
				$sql .= "school_bell_dom, school_bell_mon, school_bell_dow, ";
				$sql .= "school_bell_timezone, school_bell_enabled, school_bell_description, ";
				$sql .= "school_bell_monday, school_bell_tuesday, school_bell_wednesday, ";
				$sql .= "school_bell_thursday, school_bell_friday, ";
				$sql .= "school_bell_saturday, school_bell_sunday";
				$sql .= ") VALUES (";
				$sql .= ":domain_uuid, :school_bell_uuid, :name, ";
				$sql .= "'loopback/', :destination, :full_path, :recording, ";
				$sql .= ":ring_timeout, -1, -1, -1, -1, -1, ";
				$sql .= ":timezone, :enabled, :description, ";
				$sql .= ":monday, :tuesday, :wednesday, :thursday, :friday, :saturday, :sunday";
				$sql .= ")";
			}
			else {

				$sql = "UPDATE v_school_bells SET ";
				$sql .= "school_bell_name = :name, ";
				$sql .= "school_bell_leg_a_type = 'loopback/', ";
				$sql .= "school_bell_leg_a_data = :destination, ";
				$sql .= "school_bell_leg_b_type = :full_path, ";
				$sql .= "school_bell_leg_b_data = :recording, ";
				$sql .= "school_bell_ring_timeout = :ring_timeout, ";
				$sql .= "school_bell_timezone = :timezone, ";
				$sql .= "school_bell_enabled = :enabled, ";
				$sql .= "school_bell_description = :description, ";
				$sql .= "school_bell_monday = :monday, ";
				$sql .= "school_bell_tuesday = :tuesday, ";
				$sql .= "school_bell_wednesday = :wednesday, ";
				$sql .= "school_bell_thursday = :thursday, ";
				$sql .= "school_bell_friday = :friday, ";
				$sql .= "school_bell_saturday = :saturday, ";
				$sql .= "school_bell_sunday = :sunday ";
				$sql .= "WHERE domain_uuid = :domain_uuid ";
				$sql .= "AND school_bell_uuid = :school_bell_uuid";
			}

			$prep_statement = $db->prepare($sql);
			$prep_statement->bindValue(':domain_uuid', $domain_uuid);
			$prep_statement->bindValue(':school_bell_uuid', $school_bell_uuid);
			$prep_statement->bindValue(':name', $school_bell_name);
			$prep_statement->bindValue(':destination', $school_bell_leg_a_data);
			$prep_statement->bindValue(':full_path', $full_path);
			$prep_statement->bindValue(':recording', $school_bell_leg_b_data);
			$prep_statement->bindValue(':ring_timeout', $school_bell_ring_timeout, PDO::PARAM_INT);
			$prep_statement->bindValue(':timezone', $school_bell_timezone);
			$prep_statement->bindValue(':enabled', $school_bell_enabled);
			$prep_statement->bindValue(':description', $school_bell_description);

			foreach ($days as $day => $selected) {
				$prep_statement->bindValue(':'.$day, $selected, PDO::PARAM_BOOL);
			}

			$prep_statement->execute();

			$sql = "DELETE FROM v_school_bell_times ";
			$sql .= "WHERE domain_uuid = :domain_uuid ";
			$sql .= "AND school_bell_uuid = :school_bell_uuid";

			$prep_statement = $db->prepare($sql);
			$prep_statement->bindValue(':domain_uuid', $domain_uuid);
			$prep_statement->bindValue(':school_bell_uuid', $school_bell_uuid);
			$prep_statement->execute();

			$sql = "INSERT INTO v_school_bell_times (";
			$sql .= "school_bell_time_uuid, domain_uuid, school_bell_uuid, ";
			$sql .= "school_bell_hour, school_bell_minute, school_bell_order";
			$sql .= ") VALUES (";
			$sql .= ":school_bell_time_uuid, :domain_uuid, :school_bell_uuid, ";
			$sql .= ":hour, :minute, :bell_order";
			$sql .= ")";

			foreach ($bell_times as $index => $time) {

				list($hour, $minute) = array_map('intval', explode(':', $time));

				$prep_statement = $db->prepare($sql);
				$prep_statement->bindValue(':school_bell_time_uuid', uuid());
				$prep_statement->bindValue(':domain_uuid', $domain_uuid);
				$prep_statement->bindValue(':school_bell_uuid', $school_bell_uuid);
				$prep_statement->bindValue(':hour', $hour, PDO::PARAM_INT);
				$prep_statement->bindValue(':minute', $minute, PDO::PARAM_INT);
				$prep_statement->bindValue(':bell_order', $index, PDO::PARAM_INT);
				$prep_statement->execute();
			}

			$db->commit();

			message::add(($action === 'add')
				? 'Bell schedule added.'
				: 'Bell schedule updated.');

			header('Location: school_bells.php');
			return;
		}
		catch (Throwable $e) {

			if ($db->inTransaction()) {
				$db->rollBack();
			}

			$errors[] = 'Database error: '.$e->getMessage();
		}
	}
}

$sql = "SELECT recording_name, recording_filename ";
$sql .= "FROM v_recordings ";
$sql .= "WHERE domain_uuid = :domain_uuid ";
$sql .= "ORDER BY recording_name ASC";

$prep_statement = $db->prepare($sql);
$prep_statement->bindValue(':domain_uuid', $domain_uuid);
$prep_statement->execute();
$recordings = $prep_statement->fetchAll(PDO::FETCH_ASSOC);

require_once "resources/header.php";
$document['title'] = ($action === 'add') ? 'Add Bell Schedule' : 'Edit Bell Schedule';
?>

<style>
.school-bell-days {
	display: flex;
	flex-wrap: wrap;
	gap: 8px;
	margin: 4px 0;
}
.school-bell-day {
	display: inline-flex;
	align-items: center;
	gap: 6px;
	padding: 7px 10px;
	border: 1px solid #c8c8c8;
	border-radius: 5px;
}
.school-bell-times {
	display: grid;
	grid-template-columns: repeat(4, minmax(170px, 1fr));
	gap: 8px 14px;
	max-width: 980px;
	margin-bottom: 10px;
}
.school-bell-time-row {
	display: flex;
	align-items: center;
	gap: 6px;
	min-width: 0;
}
.school-bell-time-row input[type="time"] {
	min-width: 0;
	width: 145px;
}
.school-bell-remove-time {
	min-width: 30px;
	padding-left: 8px !important;
	padding-right: 8px !important;
	font-weight: bold;
}
.school-bell-editor-actions {
	display: flex;
	gap: 8px;
	justify-content: flex-end;
	flex-wrap: wrap;
}
@media (max-width: 1100px) {
	.school-bell-times {
		grid-template-columns: repeat(3, minmax(170px, 1fr));
	}
}
@media (max-width: 800px) {
	.school-bell-times {
		grid-template-columns: repeat(2, minmax(170px, 1fr));
	}
}
@media (max-width: 520px) {
	.school-bell-times {
		grid-template-columns: 1fr;
	}
}
</style>

<form id="school-bell-edit-test-form" method="post" action="school_bell_test.php" style="display:none"><input type="hidden" name="csrf" value="<?php echo escape($school_bell_csrf); ?>"><input type="hidden" name="id" value="<?php echo escape($school_bell_uuid); ?>"></form>
<form id="school-bell-edit-copy-form" method="post" action="school_bell_copy.php" style="display:none"><input type="hidden" name="csrf" value="<?php echo escape($school_bell_csrf); ?>"><input type="hidden" name="id" value="<?php echo escape($school_bell_uuid); ?>"></form>
<form method="post" id="school-bell-form">
<input type="hidden" name="csrf" value="<?php echo escape($school_bell_csrf); ?>">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="30%" nowrap="nowrap">
			<b><?php echo ($action === 'add') ? 'Add Bell Schedule' : 'Edit Bell Schedule'; ?></b>
		</td>
		<td width="70%" align="right">
			<div class="school-bell-editor-actions">
				<?php if ($action === 'update' && permission_exists('school_bell_edit')) { ?>
					<input type="button" class="btn" value="Test Bell"
						onclick="if (confirm('Ring this bell now?')) document.getElementById('school-bell-edit-test-form').submit();">
				<?php } ?>
				<?php if ($action === 'update' && permission_exists('school_bell_add')) { ?>
					<input type="button" class="btn" value="Copy"
						onclick="document.getElementById('school-bell-edit-copy-form').submit();">
				<?php } ?>
				<input type="button" class="btn" value="Back"
					onclick="window.location='school_bells.php'">
				<input type="submit" class="btn" value="Save">
			</div>
		</td>
	</tr>
</table>

<?php if (count($errors) > 0) { ?>
	<div style="margin:15px 0;padding:12px;border:1px solid #c00;">
		<?php foreach ($errors as $error) { ?>
			<?php echo escape($error); ?><br>
		<?php } ?>
	</div>
<?php } ?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">

	<tr>
		<td class="vncellreq">Schedule Name</td>
		<td class="vtable">
			<input class="formfld" type="text" name="school_bell_name"
				value="<?php echo escape($school_bell_name); ?>" required>
		</td>
	</tr>

	<tr>
		<td class="vncellreq">Days</td>
		<td class="vtable">
			<div class="school-bell-days">
				<?php foreach ($days as $day => $selected) { ?>
					<label class="school-bell-day">
						<input type="checkbox"
							name="days[<?php echo escape($day); ?>]"
							<?php echo $selected ? 'checked' : ''; ?>>
						<?php echo ucfirst($day); ?>
					</label>
				<?php } ?>
			</div>
		</td>
	</tr>

	<tr>
		<td class="vncellreq">Destination</td>
		<td class="vtable">
			<input class="formfld" type="text"
				name="school_bell_leg_a_data"
				value="<?php echo escape($school_bell_leg_a_data); ?>" required>
			<br>
			Extension or paging destination to originate.
		</td>
	</tr>

	<tr>
		<td class="vncellreq">Recording</td>
		<td class="vtable">
			<select class="formfld" name="school_bell_leg_b_data" required>
				<option value=""></option>
				<?php foreach ($recordings as $recording) { ?>
					<option
						value="<?php echo escape($recording['recording_filename']); ?>"
						<?php echo ($school_bell_leg_b_data === $recording['recording_filename']) ? 'selected' : ''; ?>>
						<?php echo escape($recording['recording_name']); ?>
					</option>
				<?php } ?>
			</select>
		</td>
	</tr>

	<tr>
		<td class="vncellreq">Bell Times</td>
		<td class="vtable">

			<div id="school-bell-times" class="school-bell-times">

				<?php
				if (count($bell_times) === 0) {
					$bell_times = [''];
				}

				foreach ($bell_times as $time) {
				?>
					<div class="school-bell-time-row">
						<input class="formfld"
							type="time"
							name="bell_times[]"
							value="<?php echo escape($time); ?>"
							required>
						<button type="button"
							class="btn school-bell-remove-time"
							title="Remove this time"
							aria-label="Remove this time">&times;</button>
					</div>
				<?php
				}
				?>

			</div>

			<button type="button" class="btn" id="school-bell-add-time">
				+ Add Time
			</button>

		</td>
	</tr>

	<tr>
		<td class="vncell">Ring Timeout</td>
		<td class="vtable">
			<input class="formfld"
				type="number"
				min="1"
				max="60"
				name="school_bell_ring_timeout"
				value="<?php echo escape($school_bell_ring_timeout); ?>">
		</td>
	</tr>

	<tr>
		<td class="vncellreq">Timezone</td>
		<td class="vtable">
			<select class="formfld" name="school_bell_timezone">
				<?php foreach ($timezone_identifiers_list as $timezone) { ?>
					<option
						value="<?php echo escape($timezone); ?>"
						<?php echo ($timezone === $school_bell_timezone) ? 'selected' : ''; ?>>
						<?php echo escape($timezone); ?>
					</option>
				<?php } ?>
			</select>
		</td>
	</tr>

	<tr>
		<td class="vncell">Enabled</td>
		<td class="vtable">
			<select class="formfld" name="school_bell_enabled">
				<option value="true"
					<?php echo ($school_bell_enabled === 'true') ? 'selected' : ''; ?>>
					True
				</option>
				<option value="false"
					<?php echo ($school_bell_enabled !== 'true') ? 'selected' : ''; ?>>
					False
				</option>
			</select>
		</td>
	</tr>

	<tr>
		<td class="vncell">Description</td>
		<td class="vtable">
			<input class="formfld"
				type="text"
				name="school_bell_description"
				value="<?php echo escape($school_bell_description); ?>">
		</td>
	</tr>

</table>

<?php if ($action === 'update') { ?>
	<input type="hidden" name="id"
		value="<?php echo escape($school_bell_uuid); ?>">
<?php } ?>

<br>
<div align="right">
	<input type="submit" class="btn" value="Save">
</div>

</form>

<script>
(function() {
	var container = document.getElementById('school-bell-times');
	var addButton = document.getElementById('school-bell-add-time');

	function bindRemove(button) {
		button.addEventListener('click', function() {

			var rows = container.querySelectorAll('.school-bell-time-row');

			if (rows.length === 1) {
				rows[0].querySelector('input[type="time"]').value = '';
				return;
			}

			button.closest('.school-bell-time-row').remove();
		});
	}

	Array.prototype.forEach.call(
		container.querySelectorAll('.school-bell-remove-time'),
		bindRemove
	);

	addButton.addEventListener('click', function() {

		var row = document.createElement('div');
		row.className = 'school-bell-time-row';

		var input = document.createElement('input');
		input.className = 'formfld';
		input.type = 'time';
		input.name = 'bell_times[]';
		input.required = true;

		var remove = document.createElement('button');
		remove.type = 'button';
		remove.className = 'btn school-bell-remove-time';
		remove.title = 'Remove this time';
		remove.setAttribute('aria-label', 'Remove this time');
		remove.innerHTML = '&times;';

		row.appendChild(input);
		row.appendChild(remove);
		container.appendChild(row);

		bindRemove(remove);
		input.focus();
	});
})();
</script>

<?php require_once "resources/footer.php"; ?>
