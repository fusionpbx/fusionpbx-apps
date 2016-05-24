<?php

include "../root.php";

require_once "resources/require.php";
require_once "../sms_hook_common.php";

if(check_acl()) { // IP whitelisting sucks, we should get proper auth like tokens or something
		route_and_send_sms($_POST['source'], $_POST['destination'], $_POST['body']);
} else {
	error_log('ACCESS DENIED [SMS]: ' .  print_r($_SERVER['REMOTE_ADDR'], true));
	die("access denied");
}
