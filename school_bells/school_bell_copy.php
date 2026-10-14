<?php

require_once dirname(__DIR__, 2) . "/resources/require.php";
require_once "resources/pdo.php";
require_once "resources/check_auth.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !permission_exists('school_bell_add')) {
	http_response_code(403);
	exit('Access denied');
}

$csrf = (string)($_POST['csrf'] ?? '');
if (empty($_SESSION['school_bell_csrf']) || !hash_equals($_SESSION['school_bell_csrf'], $csrf)) {
	http_response_code(403);
	exit('Invalid security token');
}

if (!isset($_POST['id']) || !is_uuid($_POST['id'])) {
	http_response_code(400);
	exit('Invalid schedule identifier');
}

$source_uuid = check_str($_POST['id']);
$new_uuid = uuid();

//load source schedule
$sql = "SELECT * FROM v_school_bells ";
$sql .= "WHERE domain_uuid = :domain_uuid ";
$sql .= "AND school_bell_uuid = :school_bell_uuid ";
$sql .= "LIMIT 1";

$prep_statement = $db->prepare($sql);
$prep_statement->bindValue(':domain_uuid', $domain_uuid);
$prep_statement->bindValue(':school_bell_uuid', $source_uuid);
$prep_statement->execute();
$source = $prep_statement->fetch(PDO::FETCH_ASSOC);

if (!$source) {
	message::add('Bell schedule not found.');
	header('Location: school_bells.php');
	exit;
}

try {

	$db->beginTransaction();

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
	$sql .= ":domain_uuid, :new_uuid, :name, ";
	$sql .= ":leg_a_type, :leg_a_data, :leg_b_type, :leg_b_data, ";
	$sql .= ":ring_timeout, :min, :hour, :dom, :mon, :dow, ";
	$sql .= ":timezone, 'false', :description, ";
	$sql .= ":monday, :tuesday, :wednesday, :thursday, :friday, :saturday, :sunday";
	$sql .= ")";

	$prep_statement = $db->prepare($sql);

	$prep_statement->bindValue(':domain_uuid', $domain_uuid);
	$prep_statement->bindValue(':new_uuid', $new_uuid);
	$prep_statement->bindValue(':name', $source['school_bell_name'].' Copy');
	$prep_statement->bindValue(':leg_a_type', $source['school_bell_leg_a_type']);
	$prep_statement->bindValue(':leg_a_data', $source['school_bell_leg_a_data']);
	$prep_statement->bindValue(':leg_b_type', $source['school_bell_leg_b_type']);
	$prep_statement->bindValue(':leg_b_data', $source['school_bell_leg_b_data']);
	$prep_statement->bindValue(':ring_timeout', $source['school_bell_ring_timeout']);
	$prep_statement->bindValue(':min', $source['school_bell_min']);
	$prep_statement->bindValue(':hour', $source['school_bell_hour']);
	$prep_statement->bindValue(':dom', $source['school_bell_dom']);
	$prep_statement->bindValue(':mon', $source['school_bell_mon']);
	$prep_statement->bindValue(':dow', $source['school_bell_dow']);
	$prep_statement->bindValue(':timezone', $source['school_bell_timezone']);
	$prep_statement->bindValue(':description', $source['school_bell_description']);
	$prep_statement->bindValue(':monday', $source['school_bell_monday'], PDO::PARAM_BOOL);
	$prep_statement->bindValue(':tuesday', $source['school_bell_tuesday'], PDO::PARAM_BOOL);
	$prep_statement->bindValue(':wednesday', $source['school_bell_wednesday'], PDO::PARAM_BOOL);
	$prep_statement->bindValue(':thursday', $source['school_bell_thursday'], PDO::PARAM_BOOL);
	$prep_statement->bindValue(':friday', $source['school_bell_friday'], PDO::PARAM_BOOL);
	$prep_statement->bindValue(':saturday', $source['school_bell_saturday'], PDO::PARAM_BOOL);
	$prep_statement->bindValue(':sunday', $source['school_bell_sunday'], PDO::PARAM_BOOL);
	$prep_statement->execute();

	//copy child times
	$sql = "SELECT school_bell_hour, school_bell_minute, school_bell_order ";
	$sql .= "FROM v_school_bell_times ";
	$sql .= "WHERE domain_uuid = :domain_uuid ";
	$sql .= "AND school_bell_uuid = :school_bell_uuid ";
	$sql .= "ORDER BY school_bell_order, school_bell_hour, school_bell_minute";

	$prep_statement = $db->prepare($sql);
	$prep_statement->bindValue(':domain_uuid', $domain_uuid);
	$prep_statement->bindValue(':school_bell_uuid', $source_uuid);
	$prep_statement->execute();
	$times = $prep_statement->fetchAll(PDO::FETCH_ASSOC);

	$sql = "INSERT INTO v_school_bell_times (";
	$sql .= "school_bell_time_uuid, domain_uuid, school_bell_uuid, ";
	$sql .= "school_bell_hour, school_bell_minute, school_bell_order";
	$sql .= ") VALUES (";
	$sql .= ":time_uuid, :domain_uuid, :school_bell_uuid, ";
	$sql .= ":hour, :minute, :bell_order";
	$sql .= ")";

	foreach ($times as $time) {
		$prep_statement = $db->prepare($sql);
		$prep_statement->bindValue(':time_uuid', uuid());
		$prep_statement->bindValue(':domain_uuid', $domain_uuid);
		$prep_statement->bindValue(':school_bell_uuid', $new_uuid);
		$prep_statement->bindValue(':hour', (int)$time['school_bell_hour'], PDO::PARAM_INT);
		$prep_statement->bindValue(':minute', (int)$time['school_bell_minute'], PDO::PARAM_INT);
		$prep_statement->bindValue(':bell_order', (int)$time['school_bell_order'], PDO::PARAM_INT);
		$prep_statement->execute();
	}

	$db->commit();

	message::add('Bell schedule copied. The copy is disabled until you enable it.');
	header('Location: school_bell_edit.php?id='.$new_uuid);
	exit;
}
catch (Throwable $e) {

	if ($db->inTransaction()) {
		$db->rollBack();
	}

	message::add('Unable to copy bell schedule: '.$e->getMessage());
	header('Location: school_bells.php');
	exit;
}
?>
