<?php
// Shared read-only schedule projections. All times are evaluated in the schedule timezone.
function sb3_truth($v) { return $v === true || $v === 1 || $v === '1' || $v === 't' || $v === 'true'; }
function sb3_timezone($name) {
    try { return new DateTimeZone($name ?: date_default_timezone_get()); }
    catch (Throwable $e) { return new DateTimeZone('UTC'); }
}
function sb3_exception_match($e, $id, $date) {
    if (!sb3_truth($e['all_schedules']) && $e['school_bell_uuid'] !== $id) return false;
    if ($e['recurrence_type'] === 'annual') {
        $md=substr($date,5); $a=substr($e['start_date'],5); $b=substr($e['end_date'],5);
        return $a <= $b ? ($md >= $a && $md <= $b) : ($md >= $a || $md <= $b);
    }
    return $date >= $e['start_date'] && $date <= $e['end_date'];
}
function sb3_exception_name($exceptions, $domain, $id, $date) {
    foreach ($exceptions[$domain] ?? [] as $e) {
        if (sb3_exception_match($e,$id,$date)) return $e['exception_name'];
    }
    return null;
}
function sb3_next_bell($schedule, $times, $pauses, $exceptions, $now = null) {
    if (($schedule['school_bell_enabled'] ?? '') !== 'true') return null;
    $tz=sb3_timezone($schedule['school_bell_timezone'] ?? '');
    $now=$now ?: new DateTimeImmutable('now', $tz);
    $now=$now->setTimezone($tz);
    $id=$schedule['school_bell_uuid']; $domain=$schedule['domain_uuid'];
    $days=['school_bell_sunday','school_bell_monday','school_bell_tuesday','school_bell_wednesday','school_bell_thursday','school_bell_friday','school_bell_saturday'];
    // Search 400 calendar days; enough for a school-year exception range.
    for ($n=0; $n<400; $n++) {
        $day = $n === 0 ? $now : $now->setTime(12,0)->modify("+$n days");
        $date=$day->format('Y-m-d');
        if (!sb3_truth($schedule[$days[(int)$day->format('w')]] ?? false)) continue;
        if (isset($pauses[$id][$date]) || sb3_exception_name($exceptions,$domain,$id,$date) !== null) continue;
        foreach ($times[$id] ?? [] as $time) {
            $candidate=DateTimeImmutable::createFromFormat('!Y-m-d H:i',sprintf('%s %02d:%02d',$date,$time['school_bell_hour'],$time['school_bell_minute']),$tz);
            if (!$candidate || $candidate->format('Y-m-d H:i') !== sprintf('%s %02d:%02d',$date,$time['school_bell_hour'],$time['school_bell_minute'])) continue;
            if ($candidate > $now) return $candidate;
        }
    }
    return null;
}
