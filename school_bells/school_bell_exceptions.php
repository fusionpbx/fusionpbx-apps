<?php
require_once dirname(__DIR__, 2) . '/resources/require.php';
require_once 'resources/pdo.php';
require_once 'resources/check_auth.php';
if (!permission_exists('school_bell_view')) { http_response_code(403); exit('Access denied'); }
if (empty($_SESSION['school_bell_exception_csrf'])) $_SESSION['school_bell_exception_csrf'] = bin2hex(random_bytes(32));
$csrf = $_SESSION['school_bell_exception_csrf'];
function sb_e($s) { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function sb_true($v) { return $v === true || $v === 't' || $v === 'true' || $v === '1' || $v === 1; }
$can_edit = permission_exists('school_bell_edit');
$can_add = permission_exists('school_bell_add');
$can_delete = permission_exists('school_bell_delete');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($csrf, (string)($_POST['csrf'] ?? ''))) { http_response_code(403); exit('Invalid security token'); }
    $action = $_POST['action'] ?? '';
    if (($action === 'save' && !$can_add && !$can_edit) || ($action === 'delete' && !$can_delete)) { http_response_code(403); exit('Access denied'); }
    try {
        if ($action === 'save') {
            $id = trim((string)($_POST['id'] ?? ''));
            if ($id !== '' && (!is_uuid($id) || !$can_edit)) throw new RuntimeException('Invalid edit permission or identifier');
            if ($id === '' && !$can_add) throw new RuntimeException('Add permission required');
            $name = trim((string)($_POST['name'] ?? ''));
            $start = trim((string)($_POST['start_date'] ?? ''));
            $end = trim((string)($_POST['end_date'] ?? ''));
            $recurrence = $_POST['recurrence_type'] ?? 'none';
            $all = ($_POST['all_schedules'] ?? '') === '1';
            $enabled = ($_POST['enabled'] ?? '') === '1';
            $ids = $_POST['schedule_ids'] ?? [];
            if (!is_array($ids)) throw new RuntimeException('Invalid schedules');
            $ids = array_values(array_unique($ids));
            if ($name === '' || strlen($name) > 200) throw new RuntimeException('Exception name is required (max 200 characters)');
            foreach ([$start,$end] as $d) {
                $dt = DateTimeImmutable::createFromFormat('!Y-m-d', $d);
                if (!$dt || $dt->format('Y-m-d') !== $d) throw new RuntimeException('Invalid date');
            }
            if ($recurrence !== 'annual' && $end < $start) throw new RuntimeException('End date must not precede start date');
            if (!in_array($recurrence, ['none','annual'], true)) throw new RuntimeException('Invalid recurrence');
            if (!$all && !$ids) throw new RuntimeException('Select at least one schedule or All schedules');
            if (count($ids) > 200) throw new RuntimeException('Too many schedules');
            $db->beginTransaction();
            $valid = $db->prepare('SELECT 1 FROM v_school_bells WHERE domain_uuid = :domain AND school_bell_uuid = :id');
            foreach ($ids as $sid) {
                if (!is_string($sid) || !is_uuid($sid)) throw new RuntimeException('Invalid schedule ID');
                $valid->execute([':domain'=>$domain_uuid, ':id'=>$sid]);
                if (!$valid->fetchColumn()) throw new RuntimeException('Schedule not found in this domain');
            }
            if ($id !== '') {
                $q = $db->prepare('UPDATE v_school_bell_exceptions SET exception_name=:name, start_date=:start, end_date=:end, recurrence_type=:recurrence, all_schedules=:all, enabled=:enabled WHERE school_bell_exception_uuid=:id AND domain_uuid=:domain');
                $q->execute([':name'=>$name, ':start'=>$start, ':end'=>$end, ':recurrence'=>$recurrence, ':all'=>$all?'true':'false', ':enabled'=>$enabled?'true':'false', ':id'=>$id, ':domain'=>$domain_uuid]);
                if ($q->rowCount() < 1) {
                    $check = $db->prepare('SELECT 1 FROM v_school_bell_exceptions WHERE school_bell_exception_uuid=:id AND domain_uuid=:domain');
                    $check->execute([':id'=>$id, ':domain'=>$domain_uuid]);
                    if (!$check->fetchColumn()) throw new RuntimeException('Exception not found');
                }
            } else {
                $id = uuid();
                $q = $db->prepare('INSERT INTO v_school_bell_exceptions (school_bell_exception_uuid,domain_uuid,exception_name,start_date,end_date,recurrence_type,all_schedules,enabled) VALUES (:id,:domain,:name,:start,:end,:recurrence,:all,:enabled)');
                $q->execute([':id'=>$id, ':domain'=>$domain_uuid, ':name'=>$name, ':start'=>$start, ':end'=>$end, ':recurrence'=>$recurrence, ':all'=>$all?'true':'false', ':enabled'=>$enabled?'true':'false']);
            }
            $db->prepare('DELETE FROM v_school_bell_exception_schedules WHERE school_bell_exception_uuid=:id AND domain_uuid=:domain')->execute([':id'=>$id, ':domain'=>$domain_uuid]);
            if (!$all) {
                $q = $db->prepare('INSERT INTO v_school_bell_exception_schedules (school_bell_exception_uuid,domain_uuid,school_bell_uuid) VALUES (:id,:domain,:schedule)');
                foreach ($ids as $sid) $q->execute([':id'=>$id, ':domain'=>$domain_uuid, ':schedule'=>$sid]);
            }
            $db->commit();
            header('Location: school_bell_exceptions.php'); exit;
        } elseif ($action === 'delete') {
            $id = (string)($_POST['id'] ?? '');
            if (!is_uuid($id)) throw new RuntimeException('Invalid exception ID');
            $db->beginTransaction();
            $db->prepare('DELETE FROM v_school_bell_exception_schedules WHERE school_bell_exception_uuid=:id AND domain_uuid=:domain')->execute([':id'=>$id, ':domain'=>$domain_uuid]);
            $db->prepare('DELETE FROM v_school_bell_exceptions WHERE school_bell_exception_uuid=:id AND domain_uuid=:domain')->execute([':id'=>$id, ':domain'=>$domain_uuid]);
            $db->commit();
            header('Location: school_bell_exceptions.php'); exit;
        } else throw new RuntimeException('Invalid action');
    } catch (Throwable $e) {
        if ($db->inTransaction()) $db->rollBack();
        $error = $e->getMessage();
    }
}
$schedules_q = $db->prepare('SELECT school_bell_uuid, school_bell_name FROM v_school_bells WHERE domain_uuid=:domain ORDER BY school_bell_name');
$schedules_q->execute([':domain'=>$domain_uuid]);
$schedules = $schedules_q->fetchAll(PDO::FETCH_ASSOC);
// Build schedule labels in PHP so the page works on PostgreSQL, MySQL, and SQLite.
$q = $db->prepare('SELECT e.* FROM v_school_bell_exceptions e WHERE e.domain_uuid=:domain ORDER BY e.start_date, e.exception_name');
$q->execute([':domain'=>$domain_uuid]);
$exceptions = $q->fetchAll(PDO::FETCH_ASSOC);
$name_q = $db->prepare('SELECT m.school_bell_exception_uuid, b.school_bell_name FROM v_school_bell_exception_schedules m JOIN v_school_bells b ON b.school_bell_uuid=m.school_bell_uuid AND b.domain_uuid=m.domain_uuid WHERE m.domain_uuid=:domain ORDER BY b.school_bell_name');
$name_q->execute([':domain'=>$domain_uuid]);
$schedule_names = [];
foreach ($name_q->fetchAll(PDO::FETCH_ASSOC) as $name_row) $schedule_names[$name_row['school_bell_exception_uuid']][] = $name_row['school_bell_name'];
foreach ($exceptions as &$exception) $exception['schedule_names'] = implode(', ', $schedule_names[$exception['school_bell_exception_uuid']] ?? []);
unset($exception);
$mapq=$db->prepare('SELECT m.school_bell_exception_uuid,m.school_bell_uuid FROM v_school_bell_exception_schedules m JOIN v_school_bell_exceptions e ON e.school_bell_exception_uuid=m.school_bell_exception_uuid AND e.domain_uuid=m.domain_uuid WHERE e.domain_uuid=:domain');
$mapq->execute([':domain'=>$domain_uuid]); $assignments=[];
foreach ($mapq->fetchAll(PDO::FETCH_ASSOC) as $r) $assignments[$r['school_bell_exception_uuid']][]=$r['school_bell_uuid'];
require_once 'resources/header.php';
?>
<style>
.sb2-actions{display:flex;justify-content:space-between;align-items:center;gap:12px;margin:12px 0}
.sb2-btn{background:#4285f4!important;color:white!important;border:1px solid #4285f4!important;border-radius:3px;padding:6px 12px;cursor:pointer}
.sb2-form{border:1px solid #ddd;border-radius:5px;padding:16px;margin:15px 0;max-width:900px}
.sb2-form label{display:block;font-weight:600;margin:10px 0 4px}
.sb2-form input[type=text],.sb2-form input[type=date],.sb2-form select{width:100%;max-width:440px;padding:7px}
.sb2-form .sb2-check label{display:inline;font-weight:normal;margin:0 12px 0 4px}
.sb2-grid{display:grid;grid-template-columns:repeat(2,minmax(160px,1fr));gap:10px}
.sb2-table{width:100%;border-collapse:collapse}.sb2-table th,.sb2-table td{padding:8px;border-bottom:1px solid #ddd;text-align:left}
.sb2-cal{display:grid;grid-template-columns:repeat(7,minmax(0,1fr));gap:3px;margin-top:12px}.sb2-day{border:1px solid #ddd;min-height:56px;padding:5px;font-size:12px}.sb2-off{background:#fff1da}.sb2-head{text-align:center;font-weight:bold;min-height:auto}
@media(max-width:650px){.sb2-grid{grid-template-columns:1fr}.sb2-day{min-height:40px;font-size:10px}}


/* Opaque layout: keep the FusionPBX wallpaper behind the page, not behind its content. */
.sb2-page{background:#fff;color:#303640;padding:20px;border:1px solid #e1e5ea;border-radius:7px;box-shadow:0 2px 10px rgba(0,0,0,.09);margin:12px 0 24px}
.sb2-page h3{color:#334155}
.sb2-page .sb2-actions{flex-wrap:wrap}
.sb2-page .sb2-form{background:#fff;border:1px solid #dce2e8;box-shadow:0 1px 4px rgba(0,0,0,.04)}
.sb2-page .sb2-form input[type=text],.sb2-page .sb2-form input[type=date],.sb2-page .sb2-form select{background:#fff;color:#303640;border:1px solid #bfc8d4;border-radius:3px;box-sizing:border-box}
.sb2-page .sb2-form label{color:#303640}
.sb2-page .sb2-table{background:#fff;color:#303640}
.sb2-page .sb2-table th,.sb2-page .sb2-table td{padding:10px 12px;border-bottom:1px solid #dce2e8;color:#303640}
.sb2-page .sb2-table thead th{background:#f1f5fa;color:#2256a3}
.sb2-page .sb2-table tbody tr:nth-child(even){background:#f8fafc}
.sb2-calendar-panel{background:#fff;border:1px solid #dce2e8;border-radius:6px;padding:16px;margin-top:22px;color:#303640}
.sb2-page .sb2-day{background:#fff;color:#303640;border:1px solid #dce2e8;min-height:70px;padding:7px;overflow-wrap:anywhere}
.sb2-page .sb2-day.sb2-off{background:#fff0d9;color:#54330e;border-color:#e8bc79}
.sb2-page .sb2-day.sb2-head{background:#eef3f9;color:#2256a3;min-height:auto;text-align:center;font-weight:600}
.sb2-page .sb2-btn:hover{background:#2f73db!important;border-color:#2f73db!important}
@media(max-width:650px){.sb2-page{padding:12px}.sb2-calendar-panel{padding:9px}.sb2-page .sb2-day{min-height:42px;padding:3px;font-size:10px}.sb2-page .sb2-table{font-size:12px}}
/* Consistent spacing between action buttons */
.sb2-table td button.sb2-btn {
    margin-right: 8px;
}

.sb2-table td button.sb2-btn:last-child {
    margin-right: 0;
}

/* Consistent top navigation buttons */
.sb2-actions .sb2-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 30px;
    padding: 6px 12px;
    box-sizing: border-box;
    white-space: nowrap;
}

/* Space between Back and Add Exception */
.sb2-actions > div:last-child {
    display: flex;
    align-items: center;
    gap: 8px;
}
</style>
<div class="sb2-page">
<div class="sb2-actions"><div><h3>School Bell Exceptions</h3><p>Holidays, teacher work days, and school breaks. Dates use each schedule's timezone.</p></div><div><a class="sb2-btn" href="school_bells.php">Back</a> <?php if($can_add){ ?><button class="sb2-btn" onclick="sbNew()">+ Add Exception</button><?php } ?></div></div>
<?php if($error){ ?><p style="color:#a00"><?php echo sb_e($error); ?></p><?php } ?>
<?php if($can_add || $can_edit){ ?>
<div id="sb2-editor" class="sb2-form" style="display:none">
<h3 id="sb2-editor-title">Add Exception</h3>
<form method="post" id="sb2-form">
<input type="hidden" name="csrf" value="<?php echo sb_e($csrf); ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" id="sb2-id">
<label for="sb2-name">Name</label><input id="sb2-name" type="text" name="name" maxlength="200" required placeholder="Thanksgiving Break">
<div class="sb2-grid"><div><label for="sb2-start">Start date</label><input id="sb2-start" type="date" name="start_date" required></div><div><label for="sb2-end">End date (inclusive)</label><input id="sb2-end" type="date" name="end_date" required></div></div>
<label for="sb2-recurrence">Repeat</label><select id="sb2-recurrence" name="recurrence_type"><option value="none">Only these dates</option><option value="annual">Every year (same month/day)</option></select>
<div class="sb2-check" style="margin-top:12px"><input id="sb2-enabled" type="checkbox" name="enabled" value="1" checked><label for="sb2-enabled">Enabled</label></div>
<div class="sb2-check" style="margin-top:12px"><input id="sb2-all" type="checkbox" name="all_schedules" value="1" onchange="sbAllChanged()"><label for="sb2-all">All schedules in this domain (including future schedules)</label></div>
<div id="sb2-targets"><label>Specific schedules</label><?php foreach($schedules as $schedule){ ?><div class="sb2-check"><input type="checkbox" class="sb2-target" name="schedule_ids[]" value="<?php echo sb_e($schedule['school_bell_uuid']); ?>" id="target-<?php echo sb_e($schedule['school_bell_uuid']); ?>"><label for="target-<?php echo sb_e($schedule['school_bell_uuid']); ?>"><?php echo sb_e($schedule['school_bell_name']); ?></label></div><?php } ?></div>
<div style="margin-top:14px"><button class="sb2-btn" type="submit">Save Exception</button> <button type="button" class="sb2-btn" onclick="document.getElementById('sb2-editor').style.display='none'">Cancel</button></div>
</form></div>
<?php } ?>
<table class="sb2-table"><thead><tr><th>Exception</th><th>Dates</th><th>Repeat</th><th>Schedules</th><th>Enabled</th><th>Actions</th></tr></thead><tbody>
<?php foreach($exceptions as $e){ ?><tr><td><?php echo sb_e($e['exception_name']); ?></td><td><?php echo sb_e($e['start_date']); ?> – <?php echo sb_e($e['end_date']); ?></td><td><?php echo $e['recurrence_type']==='annual'?'Annually':'One time'; ?></td><td><?php echo sb_true($e['all_schedules'])?'All schedules':sb_e($e['schedule_names']); ?></td><td><?php echo sb_true($e['enabled'])?'Yes':'No'; ?></td><td>
<?php if($can_edit){ ?><button type="button" class="sb2-btn" onclick="sbEdit(<?php echo sb_e(json_encode(['id'=>$e['school_bell_exception_uuid'],'name'=>$e['exception_name'],'start'=>$e['start_date'],'end'=>$e['end_date'],'recurrence'=>$e['recurrence_type'],'all'=>sb_true($e['all_schedules']),'enabled'=>sb_true($e['enabled']),'schedules'=>$assignments[$e['school_bell_exception_uuid']]??[]], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT)); ?>)">Edit</button><?php } ?>
<?php if($can_delete){ ?><form method="post" style="display:inline" onsubmit="return confirm('Delete this exception?')"><input type="hidden" name="csrf" value="<?php echo sb_e($csrf); ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo sb_e($e['school_bell_exception_uuid']); ?>"><button type="submit" class="sb2-btn">Delete</button></form><?php } ?></td></tr><?php } ?>
<?php if(!$exceptions){ ?><tr><td colspan="6">No exceptions defined yet.</td></tr><?php } ?></tbody></table>
<div class="sb2-calendar-panel">
<h3>Exception Calendar</h3><div class="sb2-actions"><button type="button" class="sb2-btn" onclick="sbMonth(-1)">‹ Previous</button><strong id="sb2-month-label"></strong><button type="button" class="sb2-btn" onclick="sbMonth(1)">Next ›</button></div><div id="sb2-calendar" class="sb2-cal"></div><p style="font-size:12px">Highlighted days have an enabled exception. Annual exceptions repeat on the same month/day; movable holidays must be entered for each year.</p>
</div><!-- End calendar panel -->
</div><!-- End exceptions page -->
<script>
(function(){
const exceptions=<?php echo json_encode(array_map(function($e){return ['name'=>$e['exception_name'],'start'=>$e['start_date'],'end'=>$e['end_date'],'annual'=>$e['recurrence_type']==='annual','enabled'=>sb_true($e['enabled'])];},$exceptions),JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
let current=new Date();current.setDate(1);
function active(e,date){if(!e.enabled)return false;const iso=date.getFullYear()+'-'+String(date.getMonth()+1).padStart(2,'0')+'-'+String(date.getDate()).padStart(2,'0');if(!e.annual)return iso>=e.start&&iso<=e.end;const md=iso.slice(5),a=e.start.slice(5),b=e.end.slice(5);return a<=b?md>=a&&md<=b:md>=a||md<=b;}
function render(){document.getElementById('sb2-month-label').textContent=current.toLocaleString(undefined,{month:'long',year:'numeric'});const root=document.getElementById('sb2-calendar');root.replaceChildren();['Sun','Mon','Tue','Wed','Thu','Fri','Sat'].forEach(d=>{let el=document.createElement('div');el.className='sb2-day sb2-head';el.textContent=d;root.appendChild(el)});for(let i=0;i<current.getDay();i++){let el=document.createElement('div');root.appendChild(el)}const n=new Date(current.getFullYear(),current.getMonth()+1,0).getDate();for(let d=1;d<=n;d++){let date=new Date(current.getFullYear(),current.getMonth(),d);let matches=exceptions.filter(e=>active(e,date));let el=document.createElement('div');el.className='sb2-day'+(matches.length?' sb2-off':'');let num=document.createElement('strong');num.textContent=d;el.appendChild(num);matches.forEach(e=>{let t=document.createElement('div');t.textContent=e.name;el.appendChild(t)});root.appendChild(el)}}
window.sbMonth=function(n){current.setMonth(current.getMonth()+n);render()};render();
})();
function sbAllChanged(){document.getElementById('sb2-targets').style.display=document.getElementById('sb2-all').checked?'none':''}
function sbNew(){const f=document.getElementById('sb2-form');f.reset();document.getElementById('sb2-id').value='';document.getElementById('sb2-editor-title').textContent='Add Exception';sbAllChanged();document.getElementById('sb2-editor').style.display='block'}
function sbEdit(e){sbNew();document.getElementById('sb2-editor-title').textContent='Edit Exception';document.getElementById('sb2-id').value=e.id;document.getElementById('sb2-name').value=e.name;document.getElementById('sb2-start').value=e.start;document.getElementById('sb2-end').value=e.end;document.getElementById('sb2-recurrence').value=e.recurrence;document.getElementById('sb2-enabled').checked=e.enabled;document.getElementById('sb2-all').checked=e.all;document.querySelectorAll('.sb2-target').forEach(x=>x.checked=e.schedules.includes(x.value));sbAllChanged();document.getElementById('sb2-editor').scrollIntoView({behavior:'smooth'})}
</script>
<?php require_once 'resources/footer.php'; ?>
