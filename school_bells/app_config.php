<?php

//application details
	$apps[$x]['name'] = "School Bell";
	$apps[$x]['uuid'] = "aadeda26-f3b5-4114-96f4-a7591f069fdf";
	$apps[$x]['category'] = "Switch";
	$apps[$x]['subcategory'] = "";
	$apps[$x]['version'] = "2.0";
	$apps[$x]['license'] = "Mozilla Public License 1.1";
	$apps[$x]['url'] = "http://www.fusionpbx.com";
	$apps[$x]['description']['en-us'] = "Provide an ability to set up bell schedules for schools";

//permission details
	$y = 0;
	$apps[$x]['permissions'][$y]['name'] = "school_bell_view";
	$apps[$x]['permissions'][$y]['menu']['uuid'] = "71a52fe5-cb91-4ff6-9cee-896b0c687465";
	$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
	$apps[$x]['permissions'][$y]['groups'][] = "admin";
	$y += 1;

	$apps[$x]['permissions'][$y]['name'] = "school_bell_add";
	$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
	$apps[$x]['permissions'][$y]['groups'][] = "admin";
	$y += 1;

	$apps[$x]['permissions'][$y]['name'] = "school_bell_edit";
	$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
	$apps[$x]['permissions'][$y]['groups'][] = "admin";
	$y += 1;

	$apps[$x]['permissions'][$y]['name'] = "school_bell_delete";
	$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
	$apps[$x]['permissions'][$y]['groups'][] = "admin";
	$y += 1;

//schema: v_school_bells
	$y = 0;
	$z = 0;
	$apps[$x]['db'][$y]['table']['name'] = "v_school_bells";
	$apps[$x]['db'][$y]['table']['parent'] = "";

	$fields = [
		['domain_uuid', 'uuid', 'foreign', 'v_domains', 'domain_uuid'],
		['school_bell_uuid', 'uuid', 'primary', '', ''],
		['school_bell_name', 'text', '', '', ''],
		['school_bell_leg_a_type', 'text', '', '', ''],
		['school_bell_leg_a_data', 'text', '', '', ''],
		['school_bell_leg_b_type', 'text', '', '', ''],
		['school_bell_leg_b_data', 'text', '', '', ''],
		['school_bell_ring_timeout', 'numeric', '', '', ''],
		['school_bell_min', 'numeric', '', '', ''],
		['school_bell_hour', 'numeric', '', '', ''],
		['school_bell_dom', 'numeric', '', '', ''],
		['school_bell_mon', 'numeric', '', '', ''],
		['school_bell_dow', 'numeric', '', '', ''],
		['school_bell_timezone', 'text', '', '', ''],
		['school_bell_enabled', 'text', '', '', ''],
		['school_bell_description', 'text', '', '', ''],
		['school_bell_monday', 'boolean', '', '', ''],
		['school_bell_tuesday', 'boolean', '', '', ''],
		['school_bell_wednesday', 'boolean', '', '', ''],
		['school_bell_thursday', 'boolean', '', '', ''],
		['school_bell_friday', 'boolean', '', '', ''],
		['school_bell_saturday', 'boolean', '', '', ''],
		['school_bell_sunday', 'boolean', '', '', ''],
	];

	foreach ($fields as $field) {
		$apps[$x]['db'][$y]['fields'][$z]['name'] = $field[0];

		if ($field[1] == 'uuid') {
			$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
			$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
			$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		}
		else {
			$apps[$x]['db'][$y]['fields'][$z]['type'] = $field[1];
		}

		if ($field[2] != '') {
			$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = $field[2];
		}
		if ($field[3] != '') {
			$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = $field[3];
			$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = $field[4];
		}
		$z += 1;
	}

//schema: v_school_bell_times
	$y += 1;
	$z = 0;
	$apps[$x]['db'][$y]['table']['name'] = "v_school_bell_times";
	$apps[$x]['db'][$y]['table']['parent'] = "v_school_bells";

	$apps[$x]['db'][$y]['fields'][$z]['name'] = "school_bell_time_uuid";
	$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "uuid";
	$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
	$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "char(36)";
	$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = "primary";
	$z += 1;

	$apps[$x]['db'][$y]['fields'][$z]['name'] = "domain_uuid";
	$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "uuid";
	$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
	$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "char(36)";
	$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = "foreign";
	$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = "v_domains";
	$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = "domain_uuid";
	$z += 1;

	$apps[$x]['db'][$y]['fields'][$z]['name'] = "school_bell_uuid";
	$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "uuid";
	$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
	$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "char(36)";
	$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = "foreign";
	$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = "v_school_bells";
	$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = "school_bell_uuid";
	$z += 1;

	$apps[$x]['db'][$y]['fields'][$z]['name'] = "school_bell_hour";
	$apps[$x]['db'][$y]['fields'][$z]['type'] = "numeric";
	$z += 1;

	$apps[$x]['db'][$y]['fields'][$z]['name'] = "school_bell_minute";
	$apps[$x]['db'][$y]['fields'][$z]['type'] = "numeric";
	$z += 1;

	$apps[$x]['db'][$y]['fields'][$z]['name'] = "school_bell_order";
	$apps[$x]['db'][$y]['fields'][$z]['type'] = "numeric";

//schema: v_school_bell_pauses
	$y += 1;
	$z = 0;
	$apps[$x]['db'][$y]['table']['name'] = "v_school_bell_pauses";
	$apps[$x]['db'][$y]['table']['parent'] = "v_school_bells";
	$pause_fields = [
		['school_bell_pause_uuid', 'uuid', 'primary', '', ''],
		['domain_uuid', 'uuid', 'foreign', 'v_domains', 'domain_uuid'],
		['school_bell_uuid', 'uuid', 'foreign', 'v_school_bells', 'school_bell_uuid'],
		['pause_date', 'date', '', '', ''],
	];
	foreach ($pause_fields as $field) {
		$apps[$x]['db'][$y]['fields'][$z]['name'] = $field[0];
		if ($field[1] === 'uuid') {
			$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
			$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
			$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		} else {
			$apps[$x]['db'][$y]['fields'][$z]['type'] = $field[1];
		}
		if ($field[2] !== '') $apps[$x]['db'][$y]['fields'][$z]['key']['type'] = $field[2];
		if ($field[3] !== '') {
			$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = $field[3];
			$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = $field[4];
		}
		$z += 1;
	}

//schema: v_school_bell_exceptions
	$y += 1;
	$z = 0;
	$apps[$x]['db'][$y]['table']['name'] = "v_school_bell_exceptions";
	$apps[$x]['db'][$y]['table']['parent'] = "";
	$exception_fields = [
		['school_bell_exception_uuid', 'uuid', 'primary', '', ''],
		['domain_uuid', 'uuid', 'foreign', 'v_domains', 'domain_uuid'],
		['exception_name', 'text', '', '', ''],
		['start_date', 'date', '', '', ''],
		['end_date', 'date', '', '', ''],
		['recurrence_type', 'text', '', '', ''],
		['all_schedules', 'boolean', '', '', ''],
		['enabled', 'boolean', '', '', ''],
	];
	foreach ($exception_fields as $field) {
		$apps[$x]['db'][$y]['fields'][$z]['name'] = $field[0];
		if ($field[1] === 'uuid') {
			$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
			$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
			$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		} else {
			$apps[$x]['db'][$y]['fields'][$z]['type'] = $field[1];
		}
		if ($field[2] !== '') $apps[$x]['db'][$y]['fields'][$z]['key']['type'] = $field[2];
		if ($field[3] !== '') {
			$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = $field[3];
			$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = $field[4];
		}
		$z += 1;
	}

//schema: v_school_bell_exception_schedules
	$y += 1;
	$z = 0;
	$apps[$x]['db'][$y]['table']['name'] = "v_school_bell_exception_schedules";
	$apps[$x]['db'][$y]['table']['parent'] = "v_school_bell_exceptions";
	$mapping_fields = [
		['school_bell_exception_uuid', 'uuid', 'foreign', 'v_school_bell_exceptions', 'school_bell_exception_uuid'],
		['domain_uuid', 'uuid', 'foreign', 'v_domains', 'domain_uuid'],
		['school_bell_uuid', 'uuid', 'foreign', 'v_school_bells', 'school_bell_uuid'],
	];
	foreach ($mapping_fields as $field) {
		$apps[$x]['db'][$y]['fields'][$z]['name'] = $field[0];
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = $field[2];
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = $field[3];
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = $field[4];
		$z += 1;
	}

//schema: v_school_bell_history
	$y += 1;
	$z = 0;
	$apps[$x]['db'][$y]['table']['name'] = "v_school_bell_history";
	$apps[$x]['db'][$y]['table']['parent'] = "";
	$history_fields = [
		['school_bell_history_uuid', 'uuid', 'primary', '', ''],
		['domain_uuid', 'uuid', 'foreign', 'v_domains', 'domain_uuid'],
		['school_bell_uuid', 'uuid', 'foreign', 'v_school_bells', 'school_bell_uuid'],
		['schedule_name', 'text', '', '', ''],
		['scheduled_local_date', 'date', '', '', ''],
		['scheduled_local_time', 'time', '', '', ''],
		['schedule_timezone', 'text', '', '', ''],
		['decision', 'text', '', '', ''],
		['detail', 'text', '', '', ''],
		['created_at', 'timestamptz', '', '', ''],
	];
	foreach ($history_fields as $field) {
		$apps[$x]['db'][$y]['fields'][$z]['name'] = $field[0];
		if ($field[1] === 'uuid') {
			$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
			$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
			$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		} elseif ($field[1] === 'timestamptz') {
			$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'timestamptz';
			$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'date';
			$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'datetime';
		} elseif ($field[1] === 'time') {
			$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'time';
			$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
			$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'time';
		} else {
			$apps[$x]['db'][$y]['fields'][$z]['type'] = $field[1];
		}
		if ($field[2] !== '') $apps[$x]['db'][$y]['fields'][$z]['key']['type'] = $field[2];
		if ($field[3] !== '') {
			$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = $field[3];
			$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = $field[4];
		}
		$z += 1;
	}

?>
