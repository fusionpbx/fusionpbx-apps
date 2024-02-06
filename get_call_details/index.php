<?php
/*
	FusionPBX
	Version: MPL 1.1

	The contents of this file are subject to the Mozilla Public License Version
	1.1 (the "License"); you may not use this file except in compliance with
	the License. You may obtain a copy of the License at
	http://www.mozilla.org/MPL/

	Software distributed under the License is distributed on an "AS IS" basis,
	WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
	for the specific language governing rights and limitations under the
	License.

	The Original Code is FusionPBX

	The Initial Developer of the Original Code is
	Mark J Crane <markjcrane@fusionpbx.com>
	Portions created by the Initial Developer are Copyright (C) 2008-2012
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
include "root.php";
require_once "resources/require.php";
require_once "resources/check_auth.php";
if (permission_exists('get_call_details') || if_group("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//get_call_details
	//get the call details for all calls or all active calls

//usage
	//http://x.x.x.x/app/get_call_details/index.php?dest=101&username=example&password=1234

$fp = event_socket_create($_SESSION['event_socket_ip_address'], $_SESSION['event_socket_port'], $_SESSION['event_socket_password']);
if (!$fp) {
	$msg['error']['message'] = "Connection to Event Socket failed.";
	echo json_encode($row);
}

$response = trim(event_socket_request($fp, "api show calls"));
$response = explode("\n\n", $response);
$response_array = csv_to_named_array($response[0], ',');
unset($response);

if (isset($_REQUEST['dest'])) {
	foreach ($response_array as $row) {
		if ($row['dest'] == trim($_REQUEST['dest'])) {
			echo json_encode($row);
		}
	}
}
else {
	echo json_encode($response_array);
}

?>