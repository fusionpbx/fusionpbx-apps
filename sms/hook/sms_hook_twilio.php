<?php

//set the include path
$conf = glob("{/usr/local/etc,/etc}/fusionpbx/config.conf", GLOB_BRACE);
set_include_path(parse_ini_file($conf[0])['document.root']);

//includes files
require_once "resources/require.php";
require_once "../sms_hook_common.php";

if (check_acl()) {
	if  ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if ($debug) {
			error_log('[SMS] REQUEST: ' .  print_r($_REQUEST, true));
		}
		$body=$_REQUEST['Body'];
                if($_REQUEST['MediaUrl0']) $body.=" " . $_REQUEST['MediaUrl0'];
                route_and_send_sms($_REQUEST['From'], str_replace("+","",$_REQUEST['To']), $body);
	} else {
	  die("no");
	}
} else {
	error_log('ACCESS DENIED [SMS]: ' .  print_r($_SERVER['REMOTE_ADDR'], true));
	die("access denied");
}

?>
