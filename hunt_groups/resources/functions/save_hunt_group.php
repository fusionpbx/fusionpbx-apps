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


function save_hunt_group() {

	//Hunt Group Lua Notes:
		//get the domain
		//loop through all Hunt Groups
			//get the Hunt Group information such as the name and description
			//add each Hunt Group to the dialplan
			//get the list of destinations then build the Hunt Group lua

	//get the domain
		global $db, $domain_uuid, $host, $config;

		$tmp = "";
		$tmp .= "\n";
		$tmp .= " domain_name = \"".$domain."\"; //by default this is the ipv4 address of FreeSWITCH used for transfer to voicemail\n";
		$tmp .= "\n";
		$tmp .= "\n";

	//prepare for hunt group .lua files to be written. delete all hunt groups that are prefixed with huntgroup_ and have a file extension of .lua
		$v_prefix = 'v_huntgroup_';
		if($dh = opendir($_SESSION['switch']['scripts']['dir'])) {
			$files = Array();
			while($file = readdir($dh)) {
				if($file != "." && $file != ".." && $file[0] != '.') {
					if(is_dir($dir . "/" . $file)) {
						//this is a directory
					} else {
						if (substr($file,0, strlen($v_prefix)) == $v_prefix && substr($file,-4) == '.lua') {
							if ($file != "huntgroup_originate.lua") {
								unlink($_SESSION['switch']['scripts']['dir'].'/'.$file);
							}
						}
					}
				}
			}
			closedir($dh);
		}

	//loop through all Hunt Groups
		$x = 0;

		$sql = "select * from v_hunt_groups ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
		foreach ($result as &$row) {
				//get the Hunt Group information such as the name and description
					//$row['hunt_group_uuid']
					//$row['hunt_group_extension']
					//$row['hunt_group_name']
					//$row['hunt_group_type']
					//$row['hunt_group_timeout']
					//$row['hunt_group_context']
					//$row['hunt_group_ringback']
					//$row['hunt_group_cid_name_prefix']
					//$row['hunt_group_pin']
					//$row['hunt_group_caller_announce']
					//$row['hunt_group_enabled']
					//$row['hunt_group_description']
					$domain_uuid = $row['domain_uuid'];
					$dialplan_uuid = $row['dialplan_uuid'];

				//add each hunt group to the dialplan
					if (strlen($row['hunt_group_uuid']) > 0) {
						//set default action to add
							$action = 'add';
						//check whether the dialplan entry exists in the database
							$action = 'add'; //set default action to add
							$i = 0;
							$sql = "select count(*) as num_rows from v_dialplans ";
							$sql .= "where domain_uuid = '".$domain_uuid."' ";
							$sql .= "and dialplan_uuid = '".$dialplan_uuid."' ";
							$prep_statement_2 = $db->prepare(check_sql($sql));
							if ($prep_statement_2) {
								$prep_statement_2->execute();
								$row2 = $prep_statement_2->fetch(PDO::FETCH_ASSOC);
								if ($row2['num_rows'] > 0) {
									//$num_rows = $row2['num_rows'];
									$action = 'update';
								}
							}
							unset($prep_statement, $result);

						if ($action == 'add') {
							//create dialplan entry for each huntgroup
								$app_uuid = '0610f841-2e27-4c5f-7926-08ab3aad02e0';
								if ($row['hunt_group_enabled'] == "false") {
									$dialplan_enabled = 'false';
								}
								else {
									$dialplan_enabled = 'true';
								}
								if (strlen($dialplan_uuid) == 0) {
									//create a dialplan uuid
										$dialplan_uuid = uuid();
									//update the hunt groups table with the database
										$sql = "update v_hunt_groups ";
										$sql .= "set dialplan_uuid = '".$dialplan_uuid."' ";
										$sql .= "where domain_uuid = '".$domain_uuid."' ";
										$sql .= "and hunt_group_uuid = '".$row['hunt_group_uuid']."' ";
										$db->query($sql);
										unset($sql);
								}

								require_once "resources/classes/dialplan.php";
								$dialplan = new dialplan;
								$dialplan->domain_uuid = $domain_uuid;
								$dialplan->app_uuid = $app_uuid;
								$dialplan->dialplan_uuid = $dialplan_uuid;
								$dialplan->dialplan_name = $row['hunt_group_name'];
								//$dialplan->dialplan_continue = $dialplan_continue;
								$dialplan->dialplan_order = '330';
								$dialplan->dialplan_context = $_SESSION['context'];
								$dialplan->dialplan_enabled = $dialplan_enabled;
								$dialplan->dialplan_description = $row['hunt_group_description'];
								$dialplan->dialplan_add();
								unset($dialplan);
						}

						if ($action == 'update') {
							//update the huntgroup fifo
								$dialplan_name = $row['hunt_group_name'];
								$dialplan_order = '330';
								$context = $row['hunt_group_context'];
								if ($row['hunt_group_enabled'] == "false") {
									$enabled = 'false';
								}
								else {
									$enabled = 'true';
								}
								$description = $row['hunt_group_description'];
								$hunt_group_uuid = $row['hunt_group_uuid'];

								$sql = "update v_dialplans set ";
								$sql .= "dialplan_name = '$dialplan_name', ";
								$sql .= "dialplan_order = '$dialplan_order', ";
								$sql .= "dialplan_context = '$context', ";
								$sql .= "dialplan_enabled = '$enabled', ";
								$sql .= "dialplan_description = '$description' ";
								$sql .= "where domain_uuid = '".$domain_uuid."' ";
								$sql .= "and dialplan_uuid = '".$dialplan_uuid."' ";
								$db->query($sql);
								unset($sql);

								$sql = "delete from v_dialplan_details ";
								$sql .= "where domain_uuid = '$domain_uuid' ";
								$sql .= "and dialplan_uuid = '$dialplan_uuid' ";
								$db->query($sql);
								unset($sql);
						}

						//if action is add or update
							if ($action == 'add' || $action == 'update') {
								require_once "resources/classes/dialplan.php";
								$dialplan = new dialplan;
								$dialplan->domain_uuid = $domain_uuid;
								$dialplan->dialplan_uuid = $dialplan_uuid;
								$dialplan->dialplan_detail_tag = 'condition'; //condition, action, antiaction
								$dialplan->dialplan_detail_type = 'destination_number';
								$dialplan->dialplan_detail_data = '^'.$row['hunt_group_extension'].'$';
								//$dialplan->dialplan_detail_break = '';
								//$dialplan->dialplan_detail_inline = '';
								$dialplan->dialplan_detail_group = '1';
								$dialplan->dialplan_detail_order = '010';
								$dialplan->dialplan_detail_add();
								unset($dialplan);

								$dialplan = new dialplan;
								$dialplan->domain_uuid = $domain_uuid;
								$dialplan->dialplan_uuid = $dialplan_uuid;
								$dialplan->dialplan_detail_tag = 'action'; //condition, action, antiaction
								$dialplan->dialplan_detail_type = 'lua';
								$dialplan->dialplan_detail_data = 'v_huntgroup_'.$_SESSION['domains'][$domain_uuid]['domain_name'].'_'.$row['hunt_group_extension'].'.lua';
								//$dialplan->dialplan_detail_break = '';
								//$dialplan->dialplan_detail_inline = '';
								$dialplan->dialplan_detail_group = '1';
								$dialplan->dialplan_detail_order = '020';
								$dialplan->dialplan_detail_add();
								unset($dialplan);

								$dialplan = new dialplan;
								$dialplan->domain_uuid = $domain_uuid;
								$dialplan->dialplan_uuid = $dialplan_uuid;
								$dialplan->dialplan_detail_tag = 'condition'; //condition, action, antiaction
								$dialplan->dialplan_detail_type = 'destination_number';
								$dialplan->dialplan_detail_data = '^\*'.$row['hunt_group_extension'].'$';
								$dialplan->dialplan_detail_break = 'on-true';
								//$dialplan->dialplan_detail_inline = '';
								$dialplan->dialplan_detail_group = '2';
								$dialplan->dialplan_detail_order = '020';
								$dialplan->dialplan_detail_add();
								unset($dialplan);

								$dialplan = new dialplan;
								$dialplan->domain_uuid = $domain_uuid;
								$dialplan->dialplan_uuid = $dialplan_uuid;
								$dialplan->dialplan_detail_tag = 'action'; //condition, action, antiaction
								$dialplan->dialplan_detail_type = 'set';
								$dialplan->dialplan_detail_data = 'fifo_music=$${hold_music}';
								//$dialplan->dialplan_detail_break = '';
								//$dialplan->dialplan_detail_inline = '';
								$dialplan->dialplan_detail_group = '2';
								$dialplan->dialplan_detail_order = '020';
								$dialplan->dialplan_detail_add();
								unset($dialplan);

								$hunt_group_timeout_type = $row['hunt_group_timeout_type'];
								$hunt_group_timeout_destination = $row['hunt_group_timeout_destination'];
								if ($hunt_group_timeout_type == "voicemail") { $hunt_group_timeout_destination = '*99'.$hunt_group_timeout_destination; }
								$dialplan = new dialplan;
								$dialplan->domain_uuid = $domain_uuid;
								$dialplan->dialplan_uuid = $dialplan_uuid;
								$dialplan->dialplan_detail_tag = 'action'; //condition, action, antiaction
								$dialplan->dialplan_detail_type = 'set';
								$dialplan->dialplan_detail_data = 'fifo_orbit_exten='.$hunt_group_timeout_destination.':'.$row['hunt_group_timeout'];
								//$dialplan->dialplan_detail_break = '';
								//$dialplan->dialplan_detail_inline = '';
								$dialplan->dialplan_detail_group = '2';
								$dialplan->dialplan_detail_order = '030';
								$dialplan->dialplan_detail_add();
								unset($dialplan);

								$dialplan = new dialplan;
								$dialplan->domain_uuid = $domain_uuid;
								$dialplan->dialplan_uuid = $dialplan_uuid;
								$dialplan->dialplan_detail_tag = 'action'; //condition, action, antiaction
								$dialplan->dialplan_detail_type = 'fifo';
								$dialplan->dialplan_detail_data = $row['hunt_group_extension'].'@${domain_name} in';
								//$dialplan->dialplan_detail_break = '';
								//$dialplan->dialplan_detail_inline = '';
								$dialplan->dialplan_detail_group = '2';
								$dialplan->dialplan_detail_order = '040';
								$dialplan->dialplan_detail_add();
								unset($dialplan);
							}

					} //end if strlen hunt_group_uuid; add the Hunt Group to the dialplan

				//get the list of destinations then build the Hunt Group Lua
					$tmp = "\n";
					$tmp .= "session:preAnswer();\n";
					$tmp .= "extension = '".$row['hunt_group_extension']."';\n";
					$tmp .= "result = '';\n";
					$tmp .= "timeoutpin = 7500;\n";
					$tmp .= "sip_profile = 'internal';\n";
					$tmp .= "\n";

					$tmp .=	"function extension_registered(domain_name, sip_profile, extension)\n";
					$tmp .=	"	api = freeswitch.API();\n";
					$tmp .=	"	result = api:execute(\"sofia_contact\", sip_profile..\"/\"..extension..\"@\"..domain_name);\n";
					$tmp .=	"	if (result == \"error/user_not_registered\") then\n";
					$tmp .=	"		return false;\n";
					$tmp .=	"	else\n";
					$tmp .=	"		return true;\n";
					$tmp .=	"	end\n";
					$tmp .=	"end\n";
					$tmp .=	"\n";

					$tmp .= "\n";
					$tmp .= "sounds_dir = session:getVariable(\"sounds_dir\");\n";
					$tmp .= "uuid = session:getVariable(\"uuid\");\n";
					$tmp .= "dialed_extension = session:getVariable(\"dialed_extension\");\n";
					$tmp .= "domain_name = session:getVariable(\"domain_name\");\n";
					$tmp .= "caller_id_name = session:getVariable(\"caller_id_name\");\n";
					$tmp .= "caller_id_number = session:getVariable(\"caller_id_number\");\n";
					$tmp .= "outbound_caller_id_name = session:getVariable(\"outbound_caller_id_name\");\n";
					$tmp .= "outbound_caller_id_number = session:getVariable(\"outbound_caller_id_number\");\n";
					$tmp .= "\n";

					$tmp .= "--set the sounds path for the language, dialect and voice\n";
					$tmp .= "	default_language = session:getVariable(\"default_language\");\n";
					$tmp .= "	default_dialect = session:getVariable(\"default_dialect\");\n";
					$tmp .= "	default_voice = session:getVariable(\"default_voice\");\n";
					$tmp .= "	if (not default_language) then default_language = 'en'; end\n";
					$tmp .= "	if (not default_dialect) then default_dialect = 'us'; end\n";
					$tmp .= "	if (not default_voice) then default_voice = 'callie'; end\n";
					$tmp .= "\n";

					//pin number requested from caller if provided
						if (strlen($row['hunt_group_pin']) > 0) {
							$tmp .= "pin = '".$row['hunt_group_pin']."';\n";
							$tmp .= "digits = session:playAndGetDigits(".strlen($row['hunt_group_pin']).", ".strlen($row['hunt_group_pin']).", 3, 3000, \"#\", sounds_dir..\"/\"..default_language..\"/\"..default_dialect..\"/\"..default_voice..\"/custom/please_enter_the_pin_number.wav\", \"\", \"\\\\d+\");\n";
							$tmp .= "\n";
							$tmp .= "\n";
							$tmp .= "if (digits == pin) then\n";
							$tmp .= "	--continue\n";
							$tmp .= "\n";
						}

					//caller announce requested from caller if provided
						if ($row['hunt_group_caller_announce'] == "true" || $row['hunt_group_call_prompt'] == "true") {
							if ($row['hunt_group_caller_announce'] == "true") {
								$tmp .=	"function originate(domain_name, session, sipuri, extension, caller_id_name, caller_id_number, caller_announce) \n";
							}
							else {
								$tmp .=	"function originate(domain_name, session, sipuri, extension, caller_id_name, caller_id_number) \n";
							}
							$tmp .=	"	--caller_id_name = caller_id_name.replace(\" \", \"..\");\n";
							$tmp .=	"	caller_id_name = string.gsub(caller_id_name, \" \", \"..\");\n";
							//$tmp .=	"	--session:execute(\"luarun\", \"huntgroup_originate.lua \"..domain_name..\" \"..uuid..\" \"..sipuri..\" \"..extension..\" \"..caller_id_name..\" \"..caller_id_number..\" \"..caller_announce);\n";
							$tmp .=	"	api = freeswitch.API();\n";
							if ($row['hunt_group_caller_announce'] == "true") {
								$tmp .=	"	result = api:execute(\"luarun\", \"huntgroup_originate.lua \"..domain_name..\" \"..uuid..\" \"..sipuri..\" \"..extension..\" \"..caller_id_name..\" \"..caller_id_number..\" \"..caller_announce);\n";
							}
							else {
								$tmp .=	"	result = api:execute(\"luarun\", \"huntgroup_originate.lua \"..domain_name..\" \"..uuid..\" \"..sipuri..\" \"..extension..\" \"..caller_id_name..\" \"..caller_id_number..\"\");\n";
							}
							$tmp .=	"end";
							$tmp .=	"\n";

							if ($row['hunt_group_caller_announce'] == "true") {
								$tmp .=	"caller_announce = \"".$tmp_dir."/\"..extension..\"_\"..uuid..\".wav\";\n";
								$tmp .=	"session:streamFile(sounds_dir..\"/\"..default_language..\"/\"..default_dialect..\"/\"..default_voice..\"/custom/please_say_your_name_and_reason_for_calling.wav\");\n";
								$tmp .=	"session:execute(\"gentones\", \"%(1000, 0, 640)\");\n";
								$tmp .=	"session:execute(\"set\", \"playback_terminators=#\");\n";
								$tmp .=	"session:execute(\"record\", caller_announce..\" 180 200\");\n";
							}
							$tmp .=	"\n";
							$tmp .=	"session:setAutoHangup(false);\n";
							$tmp .=	"session:execute(\"transfer\", \"*\"..extension..\" XML ".$_SESSION["context"]."\");\n";
							$tmp .=	"\n";
						}

					//set caller id
						if (strlen($row['hunt_group_cid_name_prefix'])> 0) {
							$tmp .= "session:execute(\"set\", \"effective_caller_id_name=".$row['hunt_group_cid_name_prefix']."#\"..caller_id_name);\n";
							$tmp .= "session:execute(\"set\", \"outbound_caller_id_name=".$row['hunt_group_cid_name_prefix']."#\"..caller_id_name);\n";
						}

					//set ring back
						if (isset($row['hunt_group_ringback'])){
							if ($row['hunt_group_ringback'] == "music"){
								$tmp .= "session:execute(\"set\", \"ringback=\${hold_music}\");          --set to music\n";
								$tmp .= "session:execute(\"set\", \"transfer_ringback=\${hold_music}\"); --set to music\n";
							}
							else {
								$tmp .= "session:execute(\"set\", \"ringback=".$row['hunt_group_ringback']."\"); --set to ringtone\n";
								$tmp .= "session:execute(\"set\", \"transfer_ringback=".$row['hunt_group_ringback']."\"); --set to ringtone\n";
							}
							if ($row['hunt_group_ringback'] == "ring"){
								$tmp .= "session:execute(\"set\", \"ringback=\${us-ring}\"); --set to ringtone\n";
								$tmp .= "session:execute(\"set\", \"transfer_ringback=\${us-ring}\"); --set to ringtone\n";
							}
						}
						else {
							$tmp .= "session:execute(\"set\", \"ringback=\${hold_music}\");          --set to ringtone\n";
							$tmp .= "session:execute(\"set\", \"transfer_ringback=\${hold_music}\"); --set to ringtone\n";
						}

					if ($row['hunt_group_timeout'] > 0) {
						//$tmp .= "session:setVariable(\"call_timeout\", \"".$row['hunt_group_timeout']."\");\n";
						$tmp .= "session:setVariable(\"continue_on_fail\", \"true\");\n";
					}
					$tmp .= "session:setVariable(\"hangup_after_bridge\", \"true\");\n";
					$tmp .= "\n";
					$tmp .= "--freeswitch.consoleLog( \"info\", \"dialed extension:\"..dialed_extension..\"\\n\" );\n";
					$tmp .= "--freeswitch.consoleLog( \"info\", \"domain: \"..domain..\"\\n\" );\n";
					$tmp .= "--freeswitch.consoleLog( \"info\", \"us_ring: \"..us_ring..\"\\n\" );\n";
					$tmp .= "--freeswitch.consoleLog( \"info\", \"domain_name: \"..domain_name..\"\\n\" );\n";
					$tmp .= "\n";

					$tmp .= "--freeswitch.consoleLog( \"info\", \"action call now don't wait for dtmf\\n\" );\n";
					if ($row['hunt_group_caller_announce'] == "true" || $row['hunt_group_call_prompt'] == "true") {
						//do nothing
					}
					else {
						$tmp .= "if session:ready() then\n";
						//$tmp .= "	session.answer();\n";
					}
					$tmp .= "\n";

					$i = 0;
					$sql = "select * from v_hunt_group_destinations ";
					$sql .= "where hunt_group_uuid = '".$row['hunt_group_uuid']."' ";
					$sql .= "and domain_uuid = '$domain_uuid' ";
					//$sql .= "and destination_enabled = 'true' ";
					$sql .= "order by destination_order asc ";
					$prep_statement_2 = $db->prepare($sql);
					$prep_statement_2->execute();
					while($ent = $prep_statement_2->fetch(PDO::FETCH_ASSOC)) {
						//$ent['hunt_group_uuid']
						//$ent['destination_data']
						//$ent['destination_type']
						//$ent['destination_profile']
						//$ent['destination_order']
						//$ent['destination_enabled']
						//$ent['destination_description']

						$destination_timeout = $ent['destination_timeout'];
						$hunt_group_cid_name_prefix = $row['hunt_group_cid_name_prefix'];
						if (strlen($hunt_group_cid_name_prefix) > 0) {
							$hunt_group_cid_name_prefix .= "#";
						}
						if (strlen($destination_timeout) == 0) {
							if (strlen($row['hunt_group_timeout']) == 0) {
								$destination_timeout = '30';
							}
							else {
								$destination_timeout = $row['hunt_group_timeout'];
							}
						}

						//set the default profile
						if (strlen($ent['destination_data']) == 0) { $ent['destination_data'] = "internal"; }

						if ($ent['destination_type'] == "extension") {
							//$tmp .= "	sofia_contact_".$ent['destination_data']." = \"\${sofia_contact(".$ent['destination_profile']."/".$ent['destination_data']."@\"..domain_name..\")}\";\n";
							$tmp_sub_array["application"] = "bridge";
							$tmp_sub_array["type"] = "extension";
							$tmp_sub_array["extension"] = $ent['destination_data'];

							//$tmp_sub_array["data"] = "\"[leg_timeout=$destination_timeout]\"..sofia_contact_".$ent['destination_data'];
							$tmp_sub_array["data"] = "\"[leg_timeout=$destination_timeout,origination_caller_id_name='".$hunt_group_cid_name_prefix."\"..caller_id_name..\"',origination_caller_id_number=\"..caller_id_number..\"]user/".$ent['destination_data']."@\"..domain_name";
							$tmp_array[$i] = $tmp_sub_array;
							unset($tmp_sub_array);
						}
						if ($ent['destination_type'] == "voicemail") {
							$tmp_sub_array["application"] = "voicemail";
							$tmp_sub_array["type"] = "voicemail";
							$tmp .= "	session:answer();\n";
							$tmp .= "	session:execute(\"transfer\", \"*99".$ent['destination_data']." XML ".$_SESSION["context"]." \");\n";
							//$tmp_sub_array["application"] = "voicemail";
							//$tmp_sub_array["data"] = "default \${domain_name} ".$ent['destination_data'];
							//$tmp_array[$i] = $tmp_sub_array;
							unset($tmp_sub_array);
						}
						if ($ent['destination_type'] == "sip uri") {
							$tmp_sub_array["application"] = "bridge";
							$tmp_sub_array["type"] = "sip uri";
							//$destination_data = "{user=foo}loopback/".$ent['destination_data']."/default/XML";
							$bridge_array = outbound_route_to_bridge ($domain_uuid, $ent['destination_data']);
							$destination_data = $bridge_array[0];
							$tmp_sub_array["application"] = "bridge";
							$tmp_sub_array["data"] = "\"[leg_timeout=$destination_timeout,origination_caller_id_name='".$hunt_group_cid_name_prefix."\"..caller_id_name..\"',origination_caller_id_number=\"..caller_id_number..\"]".$destination_data."\"";
							$tmp_array[$i] = $tmp_sub_array;
							unset($tmp_sub_array);
							unset($destination_data);
						}
						$i++;
					} //end while
					unset ($sql, $prep_statement_2);
					unset($i, $ent);

					$i = 0;
					if(count($tmp_array) > 0) {
						foreach ($tmp_array as $ent) {
							$tmpdata = $ent["data"];
							if ($ent["application"] == "voicemail") { $tmpdata = "*99".$tmpdata; }
							if ($i < 1) {
								$tmp_buffer = $tmpdata;
							}
							else {
								$tmp_buffer .= "..\",\"..".$tmpdata;
							}
							$i++;
						}
					}
					unset($i);
					$tmp_application = $tmp_array[0]["application"];

					if ($row['hunt_group_type'] == "simultaneous" || $row['hunt_group_type'] == "follow_me_simultaneous" || $row['hunt_group_type'] ==  "call_forward") {
						$tmp_switch = "simultaneous";
					}
					if ($row['hunt_group_type'] == "sequence" || $row['hunt_group_type'] == "follow_me_sequence" || $row['hunt_group_type'] ==  "sequentially") {
						$tmp_switch = "sequence";
					}
					switch ($tmp_switch) {
					case "simultaneous":
						if ($row['hunt_group_caller_announce'] == "true" || $row['hunt_group_call_prompt'] == "true") {
							$i = 0;
							if (count($tmp_array) > 0) {
								foreach ($tmp_array as $tmp_row) {
									$tmpdata = $tmp_row["data"];
									if ($tmp_row["application"] == "voicemail") {
										$tmpdata = "*99".$tmpdata;
									}
									else {
										if ($tmp_row["type"] == "extension") {
											$tmp .= "if (extension_registered(domain_name, sip_profile, '".$tmp_row["extension"]."')) then\n";
											$tmp .= "	";
										}
										if ($row['hunt_group_caller_announce'] == "true") {
											$tmp .= "result = originate (domain_name, session, ".$tmpdata.", extension, caller_id_name, caller_id_number, caller_announce);\n";
										}
										else {
											$tmp .= "result = originate (domain_name, session, ".$tmpdata.", extension, caller_id_name, caller_id_number);\n";
										}
										if ($tmp_row["type"] == "extension") {
											$tmp .= "end\n";
										}
									}
								}
							}
						}
						else {
							$tmp .= "\n";
							if (strlen($tmp_buffer) > 0) {
								$tmp .= "	session:execute(\"".$tmp_application."\", $tmp_buffer);\n";
							}
						}
						break;
					case "sequence":
						$tmp .= "\n";
						$i = 0;
						if (count($tmp_array) > 0) {
							if ($row['hunt_group_caller_announce'] == "true" || $row['hunt_group_call_prompt'] == "true") {
								$i = 0;
								if (count($tmp_array) > 0) {
									foreach ($tmp_array as $tmp_row) {
										$tmpdata = $tmp_row["data"];
										if ($tmp_row["application"] == "voicemail") {
											$tmpdata = "*99".$tmpdata;
										}
										else {
											if ($tmp_row["type"] == "extension") {
												$tmp .= "if (extension_registered(domain_name, sip_profile, '".$tmp_row["extension"]."')) then\n";
												$tmp .= "	";
											}
											if ($row['hunt_group_caller_announce'] == "true") {
												$tmp .= "result = originate (domain_name, session, ".$tmpdata.", extension, caller_id_name, caller_id_number, caller_announce);\n";
											}
											else {
												$tmp .= "result = originate (domain_name, session, ".$tmpdata.", extension, caller_id_name, caller_id_number);\n";
											}
											if ($tmp_row["type"] == "extension") {
												$tmp .= "end\n";
											}
										}
									}
								}
							}
							else {
								foreach ($tmp_array as $tmp_row) {
									if (strlen($tmp_row["data"]) > 0) {
										$tmp .= "	session:execute(\"".$tmp_application."\", ".$tmp_row["data"].");\n";
									}
								}
							}
							unset($tmp_row);
						}
						break;
					}
					unset($tmp_switch, $tmp_buffer, $tmp_array);

					//set the timeout destination
						$hunt_group_timeout_destination = $row['hunt_group_timeout_destination'];
						if ($row['hunt_group_timeout_type'] == "extension") { $hunt_group_timeout_type = "transfer"; }
						if ($row['hunt_group_timeout_type'] == "voicemail") { $hunt_group_timeout_type = "transfer"; $hunt_group_timeout_destination = "*99".$hunt_group_timeout_destination." XML ".$_SESSION["context"]; }
						if ($row['hunt_group_timeout_type'] == "sip uri") { $hunt_group_timeout_type = "bridge"; }
						$tmp .= "\n";
						if ($row['hunt_group_caller_announce'] == "true" || $row['hunt_group_call_prompt'] == "true") {
							//do nothing
						}
						else {
							$tmp .= "	--timeout\n";
							if ($row['hunt_group_type'] != 'dnd') {
								$tmp .= "	originate_disposition = session:getVariable(\"originate_disposition\");\n";
								$tmp .= "	if originate_disposition ~= \"SUCCESS\" then\n";
							}
							if ($row['hunt_group_timeout_type'] == "voicemail") {
								$tmp .= "			session:answer();\n";
							}
							$tmp .= "			session:execute(\"".$hunt_group_timeout_type."\", \"".$hunt_group_timeout_destination."\");\n";
							if ($row['hunt_group_type'] != 'dnd') {
								$tmp .= "	end\n";
							}
						}

						if ($row['hunt_group_caller_announce'] == "true" || $row['hunt_group_call_prompt'] == "true") {
							//do nothing
						}
						else {
							$tmp .= "end --end if session:ready\n";
						}
						$tmp .= "\n";
						//pin number requested from caller if provided
						if (strlen($row['hunt_group_pin']) > 0) {
							$tmp .= "else \n";
							$tmp .= "	session:streamFile(sounds_dir..\"/\"..default_language..\"/\"..default_dialect..\"/\"..default_voice..\"/custom/your_pin_number_is_incorect_goodbye.wav\");\n";
							$tmp .= "	session:hangup();\n";
							$tmp .= "end\n";
							$tmp .= "\n";
						}

					//unset variables
						$tmp .= "\n";
						$tmp .= "--clear variables\n";
						$tmp .= "dialed_extension = \"\";\n";
						$tmp .= "new_extension = \"\";\n";
						$tmp .= "domain_name = \"\";\n";
						$tmp .= "\n";

					//remove invalid characters from the file names
						$huntgroup_extension = $row['hunt_group_extension'];
						$huntgroup_extension = str_replace(" ", "_", $huntgroup_extension);
						$huntgroup_extension = preg_replace("/[\*\:\\/\<\>\|\'\"\?]/", "", $huntgroup_extension);

					//write the hungroup lua script
						if (strlen($row['hunt_group_extension']) > 0) {
							if ($row['hunt_group_enabled'] != "false") {
								$hunt_group_filename = "v_huntgroup_".$_SESSION['domains'][$domain_uuid]['domain_name']."_".$huntgroup_extension.".lua";
								//echo "location".$_SESSION['switch']['scripts']['dir']."/".$hunt_group_filename;
								$fout = fopen($_SESSION['switch']['scripts']['dir']."/".$hunt_group_filename,"w");
								fwrite($fout, $tmp);
								unset($hunt_group_filename);
								fclose($fout);
							}
						}
		} //end while

	//save the dialplan xml files
		save_dialplan_xml();

} //end huntgroup function lua

?>