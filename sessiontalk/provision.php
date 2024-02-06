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
	Copyright (C) 2008-2023 All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
	KonradSC <konrd@yahoo.com>
*/

//includes files
	require_once dirname(__DIR__, 2) . "/resources/require.php";
	
	$session_password = $_REQUEST['password'];
	if (strlen($session_password) > 0) {
		unset($_REQUEST['password']);
		header("Location: provision.php?username=".$_REQUEST['username']."&key=".$session_password."&deviceId=".$_REQUEST['deviceId']);
		exit;
	}

	//check permissions
	require_once "resources/check_auth.php";
	if (permission_exists('sessiontalk_view')) {
		//access granted
	}
	else {
		echo "access denied";
		exit;
	}

//add multi-lingual support
	$language = new text;
	$text = $language->get();

	$transport = $_SESSION['provision']['sessiontalk_transport']['text'];
	$srtp = $_SESSION['provision']['sessiontalk_srtp']['text'];
	$username_part = explode('@', $_GET['username']);
	$extension = $username_part[0];
	$domain_name = $username_part[1];
		
	$domain_part = explode('.', $domain_name);
	$sub_domain = $domain_part[0];

	$sql = "SELECT distinct extension, display_name, effective_caller_id_name, outbound_caller_id_number, v_extensions.password ";
	$sql .= " FROM v_extension_users, v_extensions, v_users,v_device_lines AS l, v_devices AS d  ";
	$sql .= " WHERE ((l.user_id = extension) ";
	$sql .= " AND (v_users.user_uuid = v_extension_users.user_uuid)  ";
	$sql .= " AND (v_extensions.extension_uuid = v_extension_users.extension_uuid)   ";
	$sql .= " AND (v_extensions.domain_uuid = :domain_uuid)  ";
	$sql .= " AND (l.user_id=extension)  "; 
	$sql .= " AND (l.device_uuid = d.device_uuid) ";
	$sql .= " AND (v_users.user_uuid = :user_uuid) "; 
	$sql .= " AND (d.domain_uuid = :domain_uuid)) "; 
	$sql .= " ORDER BY extension asc";
	$parameters['domain_uuid'] = $_SESSION["domain_uuid"];
	$parameters['user_uuid'] = $_SESSION['user_uuid'];
	$database = new database;	
	$row = $database->select($sql, $parameters, 'row');	


	$account_array['sipusername'] = $row['extension'];
	$account_array['sippassword'] = $row['password'];
	$account_array['subdomain'] = $sub_domain;
	$account_array['authusername'] = $row['extension'];
	$account_array['transport'] = $transport;
	$account_array['srtp'] = $srtp;
	$account_array['messaging'] = "Disabled";
	$account_array['video'] = "Disabled";
	$account_array['callrecording'] = "Disabled";
	$settings['update'] = "false";
	$settings['errmsg'] = "Contact Support";
	$settings['sipaccounts'][0] = $account_array;	


	header('Content-Type: application/json');
	print_r(json_encode($settings));


?>
