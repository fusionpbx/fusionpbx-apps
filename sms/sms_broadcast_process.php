<?php

//includes files
require_once dirname(__DIR__, 2) . "/resources/require.php";
require_once "resources/classes/text.php";

//set degug to false by default
$debug = false;

//connect to the database
$database = new database;

//get get data from the database
$sql = "select * from v_sms_broadcast";
$result = $database->select($sql, $parameters, 'all');
unset($sql, $parameters);

$fp = event_socket_create($_SESSION['event_socket_ip_address'], $_SESSION['event_socket_port'], $_SESSION['event_socket_password']);
if (!$fp) {
	//error message
	echo "<div align='center'><strong>Connection to Event Socket failed.</strong></div>";
}

$mailsent = false;

foreach ($result as $sms_broadcast) {
	$sms_from = $sms_broadcast['sms_broadcast_caller_id_number'];
	$domain_uuid = $sms_broadcast['domain_uuid'];
	
	$sql = "select * from v_domains where domain_uuid = :domain_uuid";
	$parameters['domain_uuid'] = $sms_broadcast['domain_uuid'];
	$result_domains = $database->select($sql, $parameters, 'all');
	$domain_name = $result_domains[0]['domain_name'];
	unset($sql, $parameters);
	
	$sms_body = $sms_broadcast['sms_broadcast_destination_data'];
	$sms_broadcast_phone_numbers = explode(PHP_EOL, $sms_broadcast['sms_broadcast_phone_numbers']);
	foreach ($sms_broadcast_phone_numbers as $individual) {
		$number = explode("|",$individual);

		print_r($number);

		$switch_cmd = "api luarun app.lua sms outbound " . $number[0] . "@" . $domain_name . " " . $sms_from . " '" . $sms_body . "' " . $mailsent;
		if ($debug) {
			error_log(print_r($switch_cmd,true));
		}
		$result2 = trim(event_socket_request($fp, $switch_cmd));
		if ($debug) {
			error_log("RESULT: " . print_r($result2,true));
		}

	}
}

die();

?>
