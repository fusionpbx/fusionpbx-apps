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
	Copyright (C) 2008-2016 All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
	KonradSC <konrd@yahoo.com>
	Michael S <michaelasuiter@gmail.com>
*/


	//includes
	require_once "root.php";
	require_once "resources/require.php";
	require_once "resources/classes/device.php";
	require_once "resources/functions/functions.php";

	//logging
	openlog("FusionPBX", LOG_PID | LOG_PERROR, LOG_LOCAL0);

	$language = new text;
	$text = $language->get();

	//Read REQUEST
	$reprovision = $_REQUEST['reprovision'] == "true" ? true : false;
	$password = $_REQUEST['password'];
	$device_id = $_REQUEST['deviceId'];
	$username_part = explode("@", $_REQUEST['username']);
	$domain_name = $username_part[1];

	//Read default/domain settimgs
	if (strlen($device_id) > 0 && strlen($password) > 0) {

		//get the default settings
		$sql = "select * from v_default_settings ";
		$sql .= "where default_setting_enabled = 'true' ";
		$sql .= "order by default_setting_order asc ";
		$database = new database;
		$result = $database->select($sql, null, 'all');
		//unset the previous settings
		if (is_array($result) && @sizeof($result) != 0) {
			foreach ($result as $row) {
				unset($_SESSION[$row['default_setting_category']]);
			}
			//set the settings as a session
			foreach ($result as $row) {
				$name = $row['default_setting_name'];
				$category = $row['default_setting_category'];
				$subcategory = $row['default_setting_subcategory'];
				if (strlen($subcategory) == 0) {
					if ($name == "array") {
						$_SESSION[$category][] = $row['default_setting_value'];
					}
					else {
						$_SESSION[$category][$name] = $row['default_setting_value'];
					}
				}
				else {
					if ($name == "array") {
						$_SESSION[$category][$subcategory][] = $row['default_setting_value'];
					}
					else {
						$_SESSION[$category][$subcategory]['uuid'] = $row['default_setting_uuid'];
						$_SESSION[$category][$subcategory][$name] = $row['default_setting_value'];
					}
				}
			}
		}
		unset($sql, $result, $row);

		//get the domain UUID
		$sql = "select domain_uuid from v_domains ";
		$sql .= "where domain_name = :domain_name ";
		$parameters['domain_name'] = $domain_name;
		$database = new database;
		$domain_uuid = $database->select($sql, $parameters, 'column');
		unset($sql, $parameters);
		$_SESSION['domain_uuid'] = $domain_uuid;
		$_SESSION['domain_name'] = $domain_name;


		//get the domain settings
		if (is_uuid($domain_uuid)) {
			$sql = "select * from v_domain_settings ";
			$sql .= "where domain_uuid = :domain_uuid ";
			$sql .= "and domain_setting_enabled = 'true' ";
			$sql .= "order by domain_setting_order asc ";
			$parameters['domain_uuid'] = $domain_uuid;
			$database = new database;
			$result = $database->select($sql, $parameters, 'all');
			//unset the arrays that domains are overriding
			if (is_array($result) && @sizeof($result) != 0) {
				foreach ($result as $row) {
					$name = $row['domain_setting_name'];
					$category = $row['domain_setting_category'];
					$subcategory = $row['domain_setting_subcategory'];
					if ($name == "array") {
						unset($_SESSION[$category][$subcategory]);
					}
				}
				//set the settings as a session
				foreach ($result as $row) {
					$name = $row['domain_setting_name'];
					$category = $row['domain_setting_category'];
					$subcategory = $row['domain_setting_subcategory'];
					if (strlen($subcategory) == 0) {
						//$$category[$name] = $row['domain_setting_value'];
						if ($name == "array") {
							$_SESSION[$category][] = $row['domain_setting_value'];
						}
						else {
							$_SESSION[$category][$name] = $row['domain_setting_value'];
						}
					}
					else {
						//$$category[$subcategory][$name] = $row['domain_setting_value'];
						if ($name == "array") {
							$_SESSION[$category][$subcategory][] = $row['domain_setting_value'];
						}
						else {
							$_SESSION[$category][$subcategory][$name] = $row['domain_setting_value'];
						}
					}
				}
			}
			unset($sql, $result, $parameters);
		}

	}
	else {
		header("HTTP/1.0 400 Bad Request");
		$settings['errmsg'] = "Bad Request";
		send_json($settings, true);
	}



	//check if password is used
	$sql = "SELECT device_uuid FROM v_device_settings as s ";
	$sql .= "WHERE s.device_setting_Value = :password ";
	$sql .= "AND s.device_setting_subcategory = 'sessiontalk_pass' ";
	$parameters['password'] = $password;
	$database = new database;
	$password_device_uuid = $database->select($sql,$parameters,'column');
	unset($sql,$parameters);

	//check if deviceid exists
	$sql = "SELECT device_uuid FROM v_device_settings as s ";
	$sql .= "WHERE s.device_setting_value = :deviceid ";
	$sql .= "AND s.device_setting_subcategory = 'sessiontalk_deviceid' ";
	$parameters['deviceid'] = $device_id;
	$database = new database;
	$deviceid_device_uuid = $database->select($sql,$parameters,'column');
	unset($sql,$parameters);

	$max_activations = $_SESSION['provision']['sessiontalk_max_activations']['numeric'] ?: 1;
	$srtp = $_SESSION['provision']['sessiontalk_srtp']['text'] ?: "Disabled";

	if ($password_device_uuid == $deviceid_device_uuid && is_uuid($deviceid_device_uuid) && !$reprovision ) {
		// Case: User scans same QR Code for same device id
		// deviceid and and key exist and match.
		// Skip device recreation steps and simmply provision. IF no lines exist, it must be intentional on the admin's part.
		$settings['errmsg'] = "Re-Activated Existing Device";
	}
	elseif (!$reprovision) {
		$password_decoded = base64_url_decode($password);
		$iv = substr($password_decoded, 0, 16);
		$password_split = substr($password_decoded, 16);


		//Fetch the active keys for this domain
		$sql = "SELECT * FROM v_sessiontalk_keys ";
		$sql .= "WHERE domain_uuid = :domain_uuid ";
		$parameters['domain_uuid'] = $domain_uuid;
		$database = new database;
		$key = $database->select($sql,$parameters,'row');
		unset($sql,$parameters);

		$cipher = "AES-128-CBC";
		//attempt decrypion with key1
		$password_decrypted = openssl_decrypt($password_split, $cipher, $key['key1'], $options = 0, $iv);
		//attempt with key2 if failed key1
		if (!$password_decrypted) {
			$password_decrypted = openssl_decrypt($password_split, $cipher, $key['key2'], $options = 0, $iv);
		}
		
		if (!$password_decrypted) {
			$settings['errmsg'] = "Expired Key - Decryption Failure";
			send_json($settings, true);
		}

		
		$password_part = explode('@', $password_decrypted);
		$expiration = $password_part[2];

		// Get the extension details
		$sql = "SELECT * FROM v_extensions as e ";
		$sql .= "WHERE domain_uuid = :domain_uuid ";
		$sql .= "AND extension = :extension ";
		$parameters['extension'] = $password_part[0];
		$parameters['domain_uuid'] = $domain_uuid;
		$database = new database;
		$extension = $database->select($sql,$parameters,'row');
		unset($sql,$parameters);
	
		if ($expiration > date("U")) {

			$activate_new = true;

			//temp permission
			$_SESSION['permissions']['device_delete'] = true;

			if (!$deviceid_device_uuid && !$password_device_uuid) {
				// Case: User scans QR Code for the first time
				// Case: User scans original QR Code after device deleted (and phone deprovisioned to login screen)
				// Case: User scans New QR Code after original device deleted (and phone deprovisioned to login screen)
				// These 3 cases are indistinguishable
				// No extra steps, placeholder. Falls through to the create device.
			}
			elseif ($deviceid_device_uuid && !$password_device_uuid) {

				// Case: User Scans New QR code but DeviceID Exists. This means the user's app was deprovisioned and kicked to the login screen.
				// deviceid exists but key doesn't
				// Action: delete existing devicee

				// Take Advantage of the device class from the devices app to delete the device. So much easier.
				// I should write a Save function for that class.
				$device = new st_device;
				$records[0]['uuid'] = $deviceid_device_uuid;
				$records[0]['checked'] = "true";
				$device->delete($records);
				unset($records, $device);

			}
			elseif (!$deviceid_device_uuid && $password_device_uuid) {

				// Case: User re-scans existing QR Code during the validity period with new deviceid. 
				// deviceid doesn't exist but key still does.
				// Action: Delete existing device
				$sql = "UPDATE v_device_settings ";
				$sql .= "SET device_setting_value = :device_setting_value ";
				$sql .= "WHERE device_setting_subcategory = 'sessiontalk_deviceid' ";
				$sql .= "AND device_uuid = :device_uuid ";
				$parameters['device_setting_value'] = $device_id;
				$parameters['device_uuid'] = $password_device_uuid;
				$database = new database;
				$database->execute($sql, $parameters);
				unset($sql, $parameters);

				$deviceid_device_uuid = $password_device_uuid;
				$activate_new = false;

			}
			elseif ($deviceid_device_uuid != $password_device_uuid) {
				// Case: Mobile App De-Activated by empty provision, but re-activated with a newly generated QR Code
				// delete both devices if they exist and are not the same (and QR isn't expired of course)
				$device = new st_device;
				$records[0]['uuid'] = $deviceid_device_uuid;
				$records[0]['checked'] = "true";
				$records[1]['uuid'] = $password_device_uuid;
				$records[1]['checked'] = "true";
				$device->delete($records);
				unset($record, $device);
			}

			if ($activate_new) {
				if (isset($max_activations) && $max_activations != 0) {
					// Count Devices for this extension
					// Not a perfect method, if you have manually added the same line to multiple devices it is still counted.
					// Also if you add the same line multiple times to a single device for some reason it will still be counted.
					$sql = "SELECT count(*) FROM v_devices as d ";
					$sql .= "JOIN v_device_lines as l ON d.device_uuid = l.device_uuid ";
					$sql .= "WHERE l.user_id = :extension ";
					$sql .= "AND l.server_address = :domain_name ";
					$sql .= "AND d.device_vendor = 'sessiontalk' ";
					$sql .= "AND l.enabled = 'true' ";
					$parameters['extension'] = $password_part[0];
					$parameters['domain_name'] = $domain_name;
					$database = new database;
					$activations = $database->select($sql, $parameters, 'column');
					unset($sql, $parameters);
				}


				if ($max_activations > $activations OR $max_activations == 0) {
					// Create Device with device_settings sessiontalk_pass, sessiontalk_deviceid, extension_uuid
					$device_uuid = uuid();
					$password_device_uuid = $device_uuid;
					$deviceid_device_uuid = $device_uuid;
					//prepare the array
					$array['devices'][0]['domain_uuid'] = $domain_uuid;
					$array['devices'][0]['device_uuid'] = $device_uuid;
					$array['devices'][0]['device_mac_address'] = substr("000000000000".$password_part[0], -12);
					//$array['devices'][0]['device_provisioned_ip'] = $device_provisioned_ip;
					$array['devices'][0]['device_label'] = $extension['extension'];
					// $array['devices'][0]['device_user_uuid'] = $device_user_uuid;
					// $array['devices'][0]['device_username'] = $device_username;
					// $array['devices'][0]['device_password'] = $device_password;
					$array['devices'][0]['device_vendor'] = "sessiontalk";
					$array['devices'][0]['device_model'] = "SessionCloud";
					// $array['devices'][0]['device_firmware_version'] = $device_firmware_version;
					$array['devices'][0]['device_enabled'] = "true";
					$array['devices'][0]['device_enabled_date'] = 'now()';
					$array['devices'][0]['device_template'] = "sessiontalk";
					// $array['devices'][0]['device_profile_uuid'] = is_uuid($device_profile_uuid) ? $device_profile_uuid : null;
					$array['devices'][0]['device_description'] = $text['device-description-sessiontalk'].$extension['extension'] ?: "Sessiontalk Mobile App ".$extension['extension'];
					$y = 0;

					//create the device line. we only create one during provision, reprovisions will read manually added lines.
					$device_line_uuid = uuid();
					$array['devices'][0]['device_lines'][$y]['domain_uuid'] = $domain_uuid;
					$array['devices'][0]['device_lines'][$y]['device_uuid'] = $device_uuid;
					$array['devices'][0]['device_lines'][$y]['device_line_uuid'] = $device_line_uuid;
					$array['devices'][0]['device_lines'][$y]['line_number'] = 1;
					$array['devices'][0]['device_lines'][$y]['server_address'] = $domain_name;
					$array['devices'][0]['device_lines'][$y]['display_name'] = $extension["effective_caller_id_name"];
					$array['devices'][0]['device_lines'][$y]['user_id'] = $extension['number_alias'] ?: $extension["extension"];
					$array['devices'][0]['device_lines'][$y]['auth_id'] = $extension['number_alias'] ?: $extension["extension"];
					$array['devices'][0]['device_lines'][$y]['password'] = $extension['password'];
					$array['devices'][0]['device_lines'][$y]['enabled'] = "true";
					$array['devices'][0]['device_lines'][$y]['sip_port'] = $_SESSION['provision']['line_sip_port']['numeric'];
					$array['devices'][0]['device_lines'][$y]['sip_transport'] =  $_SESSION['provision']['sessiontalk_transport']['text'] ?: strtoupper($_SESSION['provision']['line_sip_transport']);
					$array['devices'][0]['device_lines'][$y]['register_expires'] = $_SESSION['provision']['line_register_expires']['numeric'];
					$array['devices'][0]['device_settings'][$y]['device_uuid'] = $device_uuid;
					$array['devices'][0]['device_settings'][$y]['domain_uuid'] = $domain_uuid;
					$array['devices'][0]['device_settings'][$y]['device_setting_uuid'] = uuid();
					$array['devices'][0]['device_settings'][$y]['device_setting_subcategory'] = "sessiontalk_pass";
					$array['devices'][0]['device_settings'][$y]['device_setting_value'] = $password;
					$array['devices'][0]['device_settings'][++$y]['device_uuid'] = $device_uuid;
					$array['devices'][0]['device_settings'][$y]['domain_uuid'] = $domain_uuid;
					$array['devices'][0]['device_settings'][$y]['device_setting_uuid'] = uuid();
					$array['devices'][0]['device_settings'][$y]['device_setting_subcategory'] = "sessiontalk_deviceid";
					$array['devices'][0]['device_settings'][$y]['device_setting_value'] = $device_id;
					$array['devices'][0]['device_settings'][++$y]['device_uuid'] = $device_uuid;
					$array['devices'][0]['device_settings'][$y]['domain_uuid'] = $domain_uuid;
					$array['devices'][0]['device_settings'][$y]['device_setting_uuid'] = uuid();
					$array['devices'][0]['device_settings'][$y]['device_setting_subcategory'] = "json_md5";
					$array['devices'][0]['device_settings'][$y]['device_setting_value'] = "";

					//temp permissions
					$_SESSION['permissions']['device_line_add'] = true;
					$_SESSION['permissions']['device_add'] = true;
					$_SESSION['permissions']['device_setting_add'] = true;

					//save the device
					$database = new database;
					$database->app_name = 'sessiontalk';
					$database->app_uuid = '85774108-716c-46cb-a34b-ce80b212bc82';
					$database->save($array);


				}
				else {
					$settings['errmsg'] = "Max Devices Exceeded";
					send_json($settings, true);
				}
			}


		}
		else {
			//header("HTTP/1.0 403 Forbidden");
			$settings['errmsg'] = "Expired Key";
			send_json($settings, true);
		}

	}

	// get device lines for associated device
	if ($password_device_uuid == $deviceid_device_uuid && is_uuid($deviceid_device_uuid)) {
		$sql = "SELECT l.user_id, l.password, l.sip_transport, d.device_uuid, l.display_name, l.server_address, d.device_enabled ";
		$sql .= "FROM v_devices as d ";
		$sql .= "JOIN v_device_lines as l ";
		$sql .= "ON l.device_uuid = d.device_uuid ";
		$sql .= "WHERE d.device_uuid = :device_uuid ";
		$sql .= "AND l.enabled = 'true' ";
		$parameters['device_uuid'] = $password_device_uuid;
		$database = new database;
		$lines = $database->select($sql, $parameters, 'all');
		unset($sql, $parameters);
	
		//loop through the lines

		if (is_array($lines) && count($lines) != 0) {
			$settings['errmsg'] = $settings['errmsg'] ?: "Valid Activation";
			$i = 0;
			foreach ($lines as $line) {
				$line_domain = explode('.', $line['server_address']);
				$line_sub = $line_domain[0];
				// uncomment after they add the displayname feature. It can be set manually in the app now, supposed to be available in the JSON in the future.
				// $account_array['displayname'] = $line['display_name'] ?: $line['user_id'];
				$account_array['sipusername'] = $line['user_id'];
				$account_array['sippassword'] = $line['password'];
				$account_array['subdomain'] = $line_sub ?: $sub_domain;
				$account_array['authusername'] = $line['user_id'];
				$account_array['transport'] = $line['sip_transport'] ?: $transport;
				$account_array['srtp'] = $srtp;
				$account_array['messaging'] = "Disabled";
				$account_array['video'] = "Disabled";
				$account_array['callrecording'] = "Disabled";
				$settings['sipaccounts'][$i++] = $account_array;
				unset($line_domain,$line_sub);
			}

		}
		elseif ($lines[0]['device_enabled'] == "false") {
			$destructive = false;
			$settings['errmsg'] = "Device Disabled";
		}
		else {
			$destructive = true;
			$settings['errmsg'] = "No Lines Enabled";
		}

	}
	elseif ($device_id && $password) {
		//header("HTTP/1.0 403 Forbidden");
		$settings['errmsg'] = "Invalid Device";
		$destructive = true;
	}
	else {
		//header("HTTP/1.0 400 Forbidden");
		$settings['errmsg'] = "Bad Request";
		$destructive = true;
	}



	//send actual json with update status based on logic, update json md5 on device settings
	send_json($settings, $destructive);

?>