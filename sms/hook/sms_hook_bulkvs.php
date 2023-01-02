<?php

//set the include path
$conf = glob("{/usr/local/etc,/etc}/fusionpbx/config.conf", GLOB_BRACE);
set_include_path(parse_ini_file($conf[0])['document.root']);

//includes files
require_once "resources/require.php";
require_once "../sms_hook_common.php";

//if (check_acl()) {
//Make sure that it is a POST request.
if(strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0){
    throw new Exception('Request method must be POST!');
}

//Make sure that the content type of the POST request has been set to application/json
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
if(strcasecmp($contentType, 'application/json') != 0){
    throw new Exception('Content type must be: application/json');
}

//Receive the RAW post data.
$content = trim(file_get_contents("php://input"));
//$mail_body.="RAW post data: ".$content."\n";

//Attempt to decode the incoming RAW post data from JSON.
$decoded = json_decode($content, true);
$mail_body.="decoded data: \n";
$sms_from=$decoded['From'];
$sms_text=$decoded['Message'];
//$sms_media_url=print_r($decoded['MediaURLs'], TRUE);
foreach($decoded['To'] as $key=>$value){
  $sms_to_did_no=$value;
}
foreach($decoded['MediaURLs'] as $media_url){
	$sms_media_url=$media_url." ".$sms_media_url;
}

		
		
		route_and_send_sms($sms_from, $sms_to_did_no,$sms_text.$sms_media_url);
		
//	} else {
	  die("no");
//	}
//} else {
	error_log('ACCESS DENIED [SMS]: ' .  print_r($_SERVER['REMOTE_ADDR'], true));
	die("access denied");
//}

?>
