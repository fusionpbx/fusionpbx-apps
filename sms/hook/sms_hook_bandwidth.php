<?php

//set the include path
$conf = glob("{/usr/local/etc,/etc}/fusionpbx/config.conf", GLOB_BRACE);
set_include_path(parse_ini_file($conf[0])['document.root']);

//includes files
require_once "resources/require.php";
require_once "../sms_hook_common.php";

if ($debug) {
	error_log('[SMS] REQUEST: ' .  print_r($_SERVER, true));
}

if (check_acl()) {
	if  ($_SERVER['CONTENT_TYPE'] == 'application/json; charset=utf-8') {
		$data = json_decode(file_get_contents("php://input"));
		if (is_array($data)) {
			$from = $data[0]->message->from;
			$to = $data[0]->message->owner;
			$text = $data[0]->message->text;
			$msg_type = $data[0]->type;
			$desc = $data[0]->description;
		} else {
			$from = $data->message->from;
			$to = $data->message->owner;
			$text = $data->message->text;
			$msg_type = $data->type;
			$desc = $data->description;

		}
		if ($debug) {
			error_log('[SMS] REQUEST: ' .  print_r($data, true));
		}

		/**
		 * Bandwidth uses HTTP Callbacks webhooks to send events to any publicly addressable url, as defined in 
		 * your messaging application. All Message callbacks are sent as a list/array [ {message metadata} ] 
		 * to the webhook url in the application. You MUST Reply with a HTTP 2xx status code for every 
		 * callback/delivery receipt. Bandwidth will retry every callback over the next 24 hours until a HTTP 2xx 
		 * code is received for the callback. After 24 hours, Bandwidth will no longer try to send the callback.
		 * Bandwidth's Messaging platform has a 10 second timeout for callbacks. This means your server must 
		 * respond to the callback request within 10 seconds, otherwise the platform will try again at a later time.
		 * 
		 * https://dev.bandwidth.com/docs/messaging/webhooks#message-failed/
		 */

		switch ($msg_type) {
			case 'message-delivered': {
				route_and_send_sms($from, $to, $text);			
				return http_response_code(200);
				break;
			}
			case 'message-failed': {
				$text .= $data->description . '\n' . $text;
				route_and_send_sms($from, $to, $text);
				return http_response_code(200);
				break;
			}
			default:
				route_and_send_sms($from, $to, $text);			
				return http_response_code(200);
				break;
		}
	} else {
		error_log('[SMS] REQUEST: No SMS Data Received');
		die("no");
	}
} else {
	error_log('ACCESS DENIED [SMS]: ' .  print_r($_SERVER['REMOTE_ADDR'], true));
	die("access denied");
}

?>