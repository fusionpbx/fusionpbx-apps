<?php

require_once dirname(__DIR__, 2) . "/resources/require.php";
require_once "resources/pdo.php";
require_once "resources/check_auth.php";
require_once __DIR__ . "/school_bell_security.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !permission_exists('school_bell_edit')) {
	http_response_code(403);
	exit('Access denied');
}

$csrf = (string)($_POST['csrf'] ?? '');
if (empty($_SESSION['school_bell_csrf']) || !hash_equals($_SESSION['school_bell_csrf'], $csrf)) {
	http_response_code(403);
	exit('Invalid security token');
}

$id = (string)($_POST['id'] ?? '');
if (!is_uuid($id)) {
	http_response_code(400);
	exit('Invalid schedule identifier');
}
$school_bell_uuid = check_str($id);

$sql = "SELECT v_domains.domain_name AS context, b.domain_uuid, b.school_bell_name,
	b.school_bell_leg_a_data AS extension, b.school_bell_leg_b_type AS full_path,
	b.school_bell_leg_b_data AS recording_filename, b.school_bell_ring_timeout AS ring_timeout
	FROM v_school_bells b
	JOIN v_domains ON v_domains.domain_uuid = b.domain_uuid
	JOIN v_recordings r ON r.domain_uuid = b.domain_uuid AND r.recording_filename = b.school_bell_leg_b_data
	WHERE b.domain_uuid = :domain_uuid AND b.school_bell_uuid = :school_bell_uuid
	LIMIT 1";
$prep_statement = $db->prepare($sql);
$prep_statement->execute([
	':domain_uuid' => $domain_uuid,
	':school_bell_uuid' => $school_bell_uuid
]);
$school_bell = $prep_statement->fetch(PDO::FETCH_ASSOC);

if (!$school_bell) {
	message::add('Bell schedule not found.');
	header('Location: school_bells.php');
	exit;
}

if (!school_bell_valid_destination($school_bell['extension'])
	|| !school_bell_valid_context($school_bell['context'])
	|| !school_bell_valid_recording_filename($school_bell['recording_filename'])
	|| !school_bell_valid_recording_path($school_bell['full_path'])) {
	message::add('Bell destination or recording is invalid.');
	header('Location: school_bells.php');
	exit;
}

$ring_timeout = max(1, min(60, (int)$school_bell['ring_timeout']));
$freeswitch_event_socket = event_socket_create(
	$_SESSION['event_socket_ip_address'],
	$_SESSION['event_socket_port'],
	$_SESSION['event_socket_password']
);

if (!$freeswitch_event_socket) {
	message::add('Cannot connect to FreeSWITCH event socket.');
	header('Location: school_bells.php');
	exit;
}

$switch_cmd = "bgapi originate {ignore_early_media=true,hangup_after_bridge=true,";
$switch_cmd .= "domain_name=".$school_bell['context'].',';
$switch_cmd .= "domain_uuid=".$school_bell['domain_uuid'].',';
$switch_cmd .= "call_timeout=".$ring_timeout."}";
$switch_cmd .= "loopback/".$school_bell['extension']."/".$school_bell['context'];
$switch_cmd .= " &playback(".$school_bell['full_path'].")";

$reply = event_socket_request($freeswitch_event_socket, $switch_cmd);
if ($reply === false || (is_string($reply) && strpos($reply, '-ERR') !== false)) {
	message::add('FreeSWITCH rejected the test bell.');
} else {
	message::add('Test bell sent for '.$school_bell['school_bell_name'].'.');
}
header('Location: school_bells.php');
exit;
?>
