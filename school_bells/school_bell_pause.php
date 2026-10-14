<?php
require_once dirname(__DIR__, 2) . "/resources/require.php";
require_once "resources/pdo.php";
require_once "resources/check_auth.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !permission_exists('school_bell_edit')) {
    http_response_code(403); exit('Access denied');
}
$csrf = $_POST['csrf'] ?? '';
if (!is_string($csrf) || empty($_SESSION['school_bell_pause_csrf']) || !hash_equals($_SESSION['school_bell_pause_csrf'], $csrf)) {
    http_response_code(403); exit('Invalid security token');
}
$action = $_POST['action'] ?? '';
$ids = json_decode($_POST['schedule_ids'] ?? '[]', true);
if (!in_array($action, ['pause', 'resume'], true) || !is_array($ids) || count($ids) < 1 || count($ids) > 200) {
    http_response_code(400); exit('Invalid request');
}
$ids = array_values(array_unique($ids));
foreach ($ids as $id) {
    if (!is_string($id) || !is_uuid($id)) {
        http_response_code(400); exit('Invalid schedule identifier');
    }
}

try {
    $db->beginTransaction();
    $find = $db->prepare('SELECT school_bell_uuid, school_bell_timezone FROM v_school_bells WHERE domain_uuid = :domain_uuid AND school_bell_uuid = :id');
    $insert = $db->prepare('INSERT INTO v_school_bell_pauses (school_bell_pause_uuid, domain_uuid, school_bell_uuid, pause_date) VALUES (:uuid, :domain_uuid, :id, :pause_date)');
    $exists = $db->prepare('SELECT 1 FROM v_school_bell_pauses WHERE domain_uuid = :domain_uuid AND school_bell_uuid = :id AND pause_date = :pause_date');
    $delete = $db->prepare('DELETE FROM v_school_bell_pauses WHERE domain_uuid = :domain_uuid AND school_bell_uuid = :id AND pause_date = :pause_date');
    foreach ($ids as $id) {
        $find->execute([':domain_uuid' => $domain_uuid, ':id' => $id]);
        $schedule = $find->fetch(PDO::FETCH_ASSOC);
        if (!$schedule) throw new RuntimeException('One or more schedules were not found');
        try { $tz = new DateTimeZone($schedule['school_bell_timezone'] ?: date_default_timezone_get()); }
        catch (Exception $e) { $tz = new DateTimeZone('UTC'); }
        $date = (new DateTimeImmutable('now', $tz))->format('Y-m-d');
        if ($action === 'pause') {
            $exists->execute([':domain_uuid' => $domain_uuid, ':id' => $id, ':pause_date' => $date]);
            if (!$exists->fetchColumn()) {
                $insert->execute([':uuid' => uuid(), ':domain_uuid' => $domain_uuid, ':id' => $id, ':pause_date' => $date]);
            }
        } else {
            $delete->execute([':domain_uuid' => $domain_uuid, ':id' => $id, ':pause_date' => $date]);
        }
    }
    $db->commit();
    header('Location: school_bells.php'); exit;
} catch (Throwable $e) {
    if ($db->inTransaction()) $db->rollBack();
    error_log('School Bells pause update failed: '.$e->getMessage());
    http_response_code(500); exit('Unable to update schedule pause. Check server logs.');
}
