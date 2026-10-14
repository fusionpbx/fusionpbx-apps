# School Bells for FusionPBX

School Bells provides domain-scoped bell schedules for FusionPBX. A schedule rings a selected extension through FreeSWITCH at one or more local times on selected days, using a recording owned by the current FusionPBX domain.

## Installation

Install the complete `school_bells` directory under `app/` in a FusionPBX checkout, then install or upgrade the application through the FusionPBX Applications screen. The application manifest defines the schedule, time, pause, exception, exception-mapping, and history tables; no manual SQL is required for a fresh installation.

Existing installations created with earlier development migrations must be backed up and reconciled before upgrading. Do not delete existing data to make an installer pass. Review the generated schema and migrate existing rows in a maintenance window.

The application creates `school_bell_view`, `school_bell_add`, `school_bell_edit`, and `school_bell_delete` permissions. Administrators receive them by default; review delegated administrator roles before production use.

## Configuration

1. Create or select a recording in the same FusionPBX domain.
2. Add a schedule and choose its destination extension, recording, timezone, days, and one or more local times.
3. Leave a schedule disabled while checking its configuration.
4. Use **Test Bell** only after verifying the destination and recording.
5. Use pauses and exceptions for one-off closures and date ranges.

Recording paths are derived from the FusionPBX recording catalog. The form rejects arbitrary absolute paths, traversal, and values outside the current domain.

## Scheduler

Run `school_bells_cron.php` from the FusionPBX application directory once per minute as the FusionPBX service account. Adjust the user, PHP path, and document root for the host:

```cron
* * * * * www-data /usr/bin/php /var/www/fusionpbx/app/school_bells/school_bells_cron.php >/dev/null 2>&1
```

Do not expose the script as a public web request. It is command-line-only, filters disabled schedules, evaluates each schedule in its configured timezone, and skips paused or excepted dates.

The scheduler takes a non-blocking host lock so overlapping invocations do not originate duplicate calls on the same server. History records scheduler decisions and FreeSWITCH submission results; a history row means a request was submitted, not that the speaker confirmed playback. Monitor the history page and FreeSWITCH logs for operational failures.

DST behavior follows PHP's timezone database. Test spring-forward and fall-back dates for each timezone used by a school before relying on them for critical announcements.

## Security and tenancy

All reads and mutations are scoped to the active FusionPBX domain. Mutating forms use CSRF tokens; deletion, copy, pause, exception, and test actions use POST requests. Destinations, contexts, recording values, identifiers, and schedule inputs are validated before database or FreeSWITCH use.

Keep the event-socket password in FusionPBX configuration. Never put it in this README, a cron command, or a ticket.

## Troubleshooting

- **No bell rings:** confirm the schedule is enabled, its local day/time and timezone are correct, cron is running, and the extension can be called manually.
- **Recording rejected:** select a recording belonging to the active domain rather than entering a filesystem path.
- **Duplicate or skipped execution:** inspect the history page, scheduler lock, system cron log, and FreeSWITCH event-socket logs.
- **Schema errors during upgrade:** stop, preserve the backup, and reconcile existing development tables with the manifest before retrying. Never work around an installer error by dropping production tables.

## Pull request contents

Submit the whole `app/school_bells/` directory, including the manifest, menu, translations, PHP pages, `resources/classes/`, and this README. Do not submit databases, recordings, server configuration, credentials, or a host crontab.
