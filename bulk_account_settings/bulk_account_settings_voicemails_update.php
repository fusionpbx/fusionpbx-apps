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
	Portions created by the Initial Developer are Copyright (C) 2008-2016
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	KonradSC <konrd@yahoo.com>
*/

//set the include path
	$conf = glob("{/usr/local/etc,/etc}/fusionpbx/config.conf", GLOB_BRACE);
	set_include_path(parse_ini_file($conf[0])['document.root']);

//includes files
	require_once "resources/require.php";
	require_once "resources/check_auth.php";

//check permissions
	if (permission_exists('bulk_account_settings_voicemails')) {
		//access granted
	}
	else {
		echo "access denied";
		exit;
	}

//add multi-lingual support
	$language = new text;
	$text = $language->get();

//check for the ids
	if (is_array($_REQUEST) && sizeof($_REQUEST) > 0) {

		$voicemail_uuids = $_REQUEST["id"];
		$option_selected = $_REQUEST["option_selected"];
		$new_setting = $_REQUEST["new_setting"];
		$option_action = $_REQUEST["option_action"];
		$voicemail_option_param = $_REQUEST["voicemail_option_param"];
		$voicemail_option_order = (int)$_REQUEST["voicemail_option_order"];
		$voicemail_option_description = $_REQUEST["voicemail_option_description"];

		//seperate the action and the param
		$option_array = explode(":", $voicemail_option_param);
		$voicemail_option_action = array_shift($option_array);
		$voicemail_option_param = join(':', $option_array);
		preg_match ('/voicemail_option_(.)/',$option_selected, $matches);
		$voicemail_option_digits = $matches[1];
		
		foreach($voicemail_uuids as $voicemail_uuid) {
			$voicemail_uuid = check_str($voicemail_uuid);
			if ($voicemail_uuid != '') {
			//Voicemail Options
				if (preg_match ('/voicemail_option_/',$option_selected)) {
					//Add Options
					if ($option_action == 'add'){

						$sql = "select * from v_voicemail_options ";
						$sql .= "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
						$sql .= "and voicemail_uuid = '".$voicemail_uuid."' ";
						$sql .= "and voicemail_option_digits = '".$voicemail_option_digits."' ";
						$sql .= "and voicemail_option_order = '".$voicemail_option_order."' ";
						$database = new database;
						$voicemails = $database->select($sql, 'all');
						unset ($database);
						if (is_array($voicemails)) { 
							foreach ($voicemails as &$row) {
								$voicemail_option_uuid = $row["voicemail_option_uuid"];
							}
							unset ($prep_statement);
						}
						if (empty($voicemail_option_uuid)) {
							$voicemail_option_uuid = uuid();
						}
						
						$i=0;
						$array["voicemail_options"][$i]["voicemail_option_uuid"] = $voicemail_option_uuid;
						$array["voicemail_options"][$i]["domain_uuid"] = $_SESSION['domain_uuid'];
						$array["voicemail_options"][$i]["voicemail_uuid"] = $voicemail_uuid;
						$array["voicemail_options"][$i]["voicemail_option_digits"] = $voicemail_option_digits;
						$array["voicemail_options"][$i]["voicemail_option_description"] = $voicemail_option_description;
						$array["voicemail_options"][$i]["voicemail_option_order"] = (int)$voicemail_option_order;
						$array["voicemail_options"][$i]["voicemail_option_action"] = trim($voicemail_option_action);
						$array["voicemail_options"][$i]["voicemail_option_param"] = trim($voicemail_option_param);
						
						$database = new database;
						$database->app_name = 'bulk_account_settings';
						$database->app_uuid = null;
						$database->save($array);
						$message = $database->message;
						
						unset($database,$array,$i,$voicemail_option_uuid);	

					} elseif ($option_action == 'remove') {
					//delete the voicemail option
						$sql = "delete from v_voicemail_options ";
						$sql .= "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
						$sql .= "and voicemail_uuid = '".$voicemail_uuid."' ";
						$sql .= "and voicemail_option_digits = '".$voicemail_option_digits."' ";
						$prep_statement = $db->prepare(check_sql($sql));
						$prep_statement->execute();
						unset($prep_statement, $sql);							
					}
					
				} else {
				//All other Voicemail properties	
				//get the voicemails array
					$sql = "select * from v_voicemails ";
					$sql .= "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
					$sql .= "and voicemail_uuid = '".$voicemail_uuid."' ";
					$database = new database;
					$voicemails = $database->select($sql, 'all');
					if (is_array($voicemails)) { 
						foreach ($voicemails as &$row) {
							$voicemail = $row["voicemail"];
						}
						unset ($prep_statement);
					}

						$array["voicemails"][$i]["domain_uuid"] = $domain_uuid;
						$array["voicemails"][$i]["voicemail_uuid"] = $voicemail_uuid;
						$array["voicemails"][$i][$option_selected] = $new_setting;
	
						$database = new database;
						$database->app_name = 'bulk_account_settings';
						$database->app_uuid = null;
						$database->save($array);
						$message = $database->message;

						unset($database,$array,$i);
				}
			}
		}
	}

//redirect the browser
	$_SESSION["message"] = $text['message-update'];
	header("Location: bulk_account_settings_voicemails.php?option_selected=".$option_selected."");
	return;

?>
