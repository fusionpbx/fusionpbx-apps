<?php
require_once dirname(__DIR__, 2) . '/resources/require.php';
require_once 'resources/pdo.php';
require_once 'resources/check_auth.php';

if (!permission_exists('school_bell_view')) {
    http_response_code(403);
    exit('Access denied');
}

function sb3_h($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function sb3_friendly_time($row) {
    try {
        $timezone = new DateTimeZone($row['schedule_timezone'] ?: 'UTC');
        $local = DateTimeImmutable::createFromFormat(
            '!Y-m-d H:i:s',
            $row['scheduled_local_date'] . ' ' . substr($row['scheduled_local_time'], 0, 8),
            $timezone
        );
        if ($local) {
            return $local->format('M j, Y') . ' — ' . $local->format('g:i A T');
        }
    } catch (Throwable $e) {
        // Display the saved date/time if the timezone cannot be parsed.
    }
    return $row['scheduled_local_date'] . ' ' . substr($row['scheduled_local_time'], 0, 5);
}

function sb3_friendly_status($decision) {
    switch ($decision) {
        case 'submitted': return ['Bell Sent', 'sent'];
        case 'skipped_pause': return ['Skipped', 'skipped'];
        case 'skipped_exception': return ['Skipped', 'skipped'];
        case 'request_error': return ['Send Failed', 'failed'];
        case 'claimed': return ['In Progress', 'unknown'];
        default: return ['Unknown', 'unknown'];
    }
}

function sb3_friendly_note($row) {
    switch ($row['decision']) {
        case 'submitted': return 'Bell request sent';
        case 'skipped_pause': return 'Schedule paused for today';
        case 'skipped_exception': return 'Scheduled day off or school closure';
        case 'request_error': return 'Unable to send bell request';
        case 'claimed': return 'Scheduler claimed this slot; completion was not recorded';
        default: return 'Status unavailable';
    }
}

$limit = 100;
$q = $db->prepare("SELECT h.schedule_name, h.scheduled_local_date, h.scheduled_local_time,
    h.schedule_timezone, h.decision, h.detail
    FROM v_school_bell_history h
    WHERE h.domain_uuid = :domain
    ORDER BY h.created_at DESC
    LIMIT $limit");
$q->execute([':domain' => $domain_uuid]);
$history = $q->fetchAll(PDO::FETCH_ASSOC);

require_once 'resources/header.php';
?>
<style>
.sb3-wrap{background:#fff;color:#344054;border:1px solid #dbe1e8;border-radius:5px;padding:16px;margin:12px 0}
.sb3-head{display:flex;align-items:center;justify-content:space-between;gap:12px}
.sb3-head h3{margin:0 0 5px}
.sb3-btn{display:inline-flex;align-items:center;justify-content:center;background:#4285f4!important;color:#fff!important;border:1px solid #4285f4;border-radius:3px;padding:7px 12px;text-decoration:none!important;white-space:nowrap}
.sb3-table{width:100%;border-collapse:collapse;margin-top:15px}
.sb3-table th,.sb3-table td{padding:10px 12px;text-align:left;border-bottom:1px solid #dbe1e8;vertical-align:middle}
.sb3-table th{background:#eef3f9;color:#1d4f9a}
.sb3-table tbody tr:nth-child(even){background:#f7f9fc}
.sb3-muted{font-size:12px;color:#667085}
.sb3-status{display:inline-flex;align-items:center;gap:6px;font-weight:600;white-space:nowrap}
.sb3-status::before{content:'';width:8px;height:8px;border-radius:50%;background:currentColor;display:inline-block}
.sb3-status-sent{color:#217346}
.sb3-status-skipped{color:#a56414}
.sb3-status-failed{color:#b42318}
.sb3-status-unknown{color:#667085}
.sb3-help{margin:14px 0 0;color:#667085;font-size:12px}
@media(max-width:650px){.sb3-wrap{padding:10px}.sb3-table th,.sb3-table td{padding:8px}}
</style>
<div class="sb3-wrap">
    <div class="sb3-head">
        <div>
            <h3>Bell Activity History</h3>
            <p class="sb3-muted">View recent bell activity, skipped bells, and any issues.</p>
        </div>
        <a class="sb3-btn" href="school_bells.php">Back</a>
    </div>
    <div style="overflow-x:auto">
        <table class="sb3-table">
            <thead><tr><th>Date &amp; Time</th><th>Bell Schedule</th><th>Status</th><th>Notes</th></tr></thead>
            <tbody>
            <?php foreach ($history as $row) {
                list($status, $class) = sb3_friendly_status($row['decision']);
            ?>
                <tr>
                    <td><?php echo sb3_h(sb3_friendly_time($row)); ?></td>
                    <td><?php echo sb3_h($row['schedule_name']); ?></td>
                    <td><span class="sb3-status sb3-status-<?php echo sb3_h($class); ?>"><?php echo sb3_h($status); ?></span></td>
                    <td><?php echo sb3_h(sb3_friendly_note($row)); ?></td>
                </tr>
            <?php } ?>
            <?php if (!$history) { ?>
                <tr><td colspan="4">No bell activity recorded yet. Activity will appear when a scheduled bell time is reached.</td></tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <p class="sb3-help">Showing up to 100 recent entries. “Bell Sent” means the system submitted the bell request; it does not confirm the speakers played the sound.</p>
</div>
<?php require_once 'resources/footer.php'; ?>
