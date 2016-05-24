<?php

include "../root.php";

require_once "resources/require.php";
require_once "../sms_hook_common.php";

if (check_acl()) {
	if  ($_SERVER['REQUEST_METHOD'] == 'POST') {
			error_log('REQUEST: ' .  print_r($_REQUEST, true));
			$data = (object) ['body' => $_REQUEST['Body'],
				'to' => str_replace("+", "", $_REQUEST['To']),
				'from' => intval(str_replace("+", "", $_REQUEST['From']))
				];
		route_and_send_sms($data);
	} else {
	  die("no");
	}
} else {
	error_log('ACCESS DENIED [SMS]: ' .  print_r($_SERVER['REMOTE_ADDR'], true));
	die("access denied");
}
?>