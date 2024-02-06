<?php 

//restrict to command line only
	if(defined('STDIN')) {
		$document_root = str_replace("\\", "/", $_SERVER["PHP_SELF"]);
		preg_match("/^(.*)\/app\/.*$/", $document_root, $matches);
		$document_root = $matches[1];
		set_include_path($document_root);
		include "root.php";
		require_once "resources/require.php";
		require_once "resources/classes/text.php";
		$_SERVER["DOCUMENT_ROOT"] = $document_root;
		$format = 'text'; //html, text
	
		//add multi-lingual support
		$language = new text;
		$text = $language->get();
	}
	else {
		die('access denied');
	}

	//show debug
	$debug = false;

	set_time_limit(55); // Cannot run more than 55 seconds

	$current_timestamp = time();

	$current_minute = (int)date('i', $current_timestamp);

	$sql = "SELECT v_domains.domain_name AS context,";
	$sql .= " v_school_bells.domain_uuid AS domain_uuid,";
	$sql .= " school_bell_leg_a_data AS extension,";
	$sql .= " school_bell_leg_b_type AS full_path,";
	$sql .= " school_bell_ring_timeout AS ring_timeout,";
	$sql .= " school_bell_min as min,";
	$sql .= " school_bell_hour as hour,";
	$sql .= " school_bell_dom as dom,";
	$sql .= " school_bell_mon as mon,";
	$sql .= " school_bell_dow as dow,";
	$sql .= " school_bell_timezone as timezone ";
	$sql .= "FROM v_school_bells ";
	$sql .= "JOIN v_domains ON v_domains.domain_uuid = v_school_bells.domain_uuid ";
	$sql .= " WHERE school_bell_min = :current_minute";
	$sql .= " OR school_bell_min = -1";
	$parameters['current_minute'] = $current_minute;
	$database = new database;
	$school_bells = $database->select($sql, $parameters, 'all');
	if ($debug) {
		echo $sql."\n";
		print_r($parameters);
		print_r($school_bells);
	}
	unset($sql, $parameters);

	/*
	//used for fusionpbx 4.4
	$prep_statement = $db->prepare(check_sql($sql));
	if (!$prep_statement) {
		die('SQL forming error');
	}
	$prep_statement->bindValue('current_minute', $current_minute);
	
	if (!$prep_statement->execute()) {
		die('SQL execute error');
	}

	$school_bells = $prep_statement->fetchAll(PDO::FETCH_NAMED);
	*/
	

	if (count($school_bells) == 0) {
		echo "no bells\n";
		return;
	}

	$freeswitch_event_socket = event_socket_create($_SESSION['event_socket_ip_address'], $_SESSION['event_socket_port'], $_SESSION['event_socket_password']);
	if (!$freeswitch_event_socket) {
		die("Cannot connect to Event socket");
	}

	//$switch_result = event_socket_request($fp, 'api sofia status');
	//print($switch_result);

	foreach ($school_bells as $school_bell) {

		$school_bell_timezone = $school_bell['timezone'];
		date_default_timezone_set($school_bell_timezone);

		$school_bell_hour = (int)$school_bell['hour'];
		$current_hour = (int)date('G', $current_timestamp);

		if ((int)$current_hour != (int)$school_bell_hour) { // Hour is not matched
			if ($debug) {
				echo "Hour is not matched current {$current_hour} != bell {$school_bell_hour}\n";
			}
			continue;
		}

		$school_bell_dom = (int)$school_bell['dom'];
		$current_dom = (int)date('j', $current_timestamp);

		if ($school_bell_dom != -1 && (int)$current_dom != (int)$school_bell_dom) { // Day of the month is not matched
			if ($debug) {
				echo "Day of the month is not matched {$current_dom}  bell {$school_bell_dom}\n";
			}
			continue;
		}

		$school_bell_mon = (int)$school_bell['mon'];
		$current_mon = (int)date('n', $current_timestamp);

		if ($school_bell_mon != -1 && (int)$current_mon != (int)$school_bell_mon) { // Month is not matched
			if ($debug) {
				echo "Month is not matched current {$current_mon} != bell {$school_bell_mon}\n";
			}
			continue;
		}

		$school_bell_dow = (int)$school_bell['dow'];
		$current_dow = (int)date('w', $current_timestamp);

		if ($school_bell_dow != -1 && (int)$current_dow != (int)$school_bell_dow) { // Day of the week is not matched
			if ($debug) {
				echo "Day of the week is not matched current {$current_dow} != bell {$school_bell_dow}\n";
			}
			continue;
		}

		// We got our signal!
		$school_bell_ring_timeout = (int)$school_bell['ring_timeout'];
		if ($school_bell_ring_timeout > 60) {
			$school_bell_ring_timeout = ($school_bell['min'] == "-1") ? 55: $school_bell_ring_timeout;
		}

		$school_bell_ring_timeout = ($school_bell_ring_timeout == 0) ? 5 : $school_bell_ring_timeout;

		$switch_cmd = "bgapi ";
		$switch_cmd .= "originate {ignore_early_media=true,";
		$switch_cmd .= "hangup_after_bridge=true,";
		$switch_cmd .= "domain_name=".$school_bell['context'].",";
		$switch_cmd .= "domain_uuid=".$school_bell['domain_uuid'].",";
		$switch_cmd .= "call_timeout=".$school_bell_ring_timeout."}";
		$switch_cmd .= "loopback/".$school_bell['extension']."/".$school_bell['context'];
		$switch_cmd .= " &playback(".$school_bell['full_path'].")";
		if ($debug) {
			echo $switch_cmd."\n";
		}
		event_socket_request($freeswitch_event_socket, $switch_cmd);

	}

?>
