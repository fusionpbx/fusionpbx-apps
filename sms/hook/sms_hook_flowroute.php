<?php

include "../root.php";

require_once "resources/require.php";
require_once "../sms_hook_common.php";

if (check_acl()) {
	if  ($_SERVER['CONTENT_TYPE'] == 'application/json') {
		$data = json_decode(file_get_contents("php://input"));
		route_and_send_sms($data);	
	} else {
	  die("no");
	}
} else {
	error_log('ACCESS DENIED [SMS]: ' .  print_r($_SERVER['REMOTE_ADDR'], true));
	die("access denied");
}
?>