<?php

//includes files
require_once dirname(__DIR__, 3) . "/resources/require.php";
require_once "../sms_hook_common.php";

if (check_acl()) {
	if  ($_SERVER['CONTENT_TYPE'] == 'application/json') {
		$data = json_decode(file_get_contents("php://input"));
		if ($debug) {
			error_log('[SMS] REQUEST: ' .  print_r($data, true));
		}
		$from = intval(preg_replace('/(^[1])/','', $data->data->attributes->from));
		route_and_send_sms($from, $data->data->attributes->to, $data->data->attributes->body);	
	} else {
	  die("no");
	}
} else {
	error_log('ACCESS DENIED [SMS]: ' .  print_r($_SERVER['REMOTE_ADDR'], true));
	die("access denied");
}

?>
