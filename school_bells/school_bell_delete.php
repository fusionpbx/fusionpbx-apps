<?php

require_once dirname(__DIR__, 2) . "/resources/require.php";
require_once "resources/pdo.php";
require_once "resources/check_auth.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !permission_exists('school_bell_delete')) {
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

$db->beginTransaction();
try {
	$bindings = [':domain_uuid' => $domain_uuid, ':school_bell_uuid' => check_str($id)];
	foreach (['v_school_bell_history', 'v_school_bell_pauses', 'v_school_bell_times'] as $child_table) {
		$child = $db->prepare("DELETE FROM {$child_table} WHERE domain_uuid = :domain_uuid AND school_bell_uuid = :school_bell_uuid");
		$child->execute($bindings);
	}
	$mapping = $db->prepare('DELETE FROM v_school_bell_exception_schedules WHERE domain_uuid = :domain_uuid AND school_bell_uuid = :school_bell_uuid');
	$mapping->execute($bindings);
	$prep_statement = $db->prepare("DELETE FROM v_school_bells
		WHERE domain_uuid = :domain_uuid
		AND school_bell_uuid = :school_bell_uuid");
	$prep_statement->execute($bindings);
	$db->commit();
} catch (Throwable $e) {
	if ($db->inTransaction()) $db->rollBack();
	error_log('School Bells schedule deletion failed: '.$e->getMessage());
	http_response_code(500);
	exit('Unable to delete schedule.');
}

message::add('Bell schedule deleted.');
header('Location: school_bells.php');
exit;
?>
