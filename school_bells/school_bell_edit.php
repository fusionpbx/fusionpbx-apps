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
	Portions created by the Initial Developer are Copyright (C) 2008-2018
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
	Igor Olhovskiy <igorolhovskiy@gmail.com>
*/

//set the include path
	$conf = glob("{/usr/local/etc,/etc}/fusionpbx/config.conf", GLOB_BRACE);
	set_include_path(parse_ini_file($conf[0])['document.root']);

//includes files
	require_once "resources/require.php";
	require_once "resources/check_auth.php";

//check permissions
	if (permission_exists('school_bell_edit') || permission_exists('school_bell_add')) {
		//access granted
	} else {
		echo "access denied";
		exit;
	}

//add multi-lingual support
	$language = new text;
	$text = $language->get();

// Get timezones
	$timezone_identifiers_list = timezone_identifiers_list();
//action add or update
	if (isset($_REQUEST["id"])) {
		$action = "update";
		$school_bell_uuid = check_str($_REQUEST["id"]);
	} else {
		$action = "add";
	}

//get http post variables and set them to php variables
	if (count($_POST) > 0) {
		$school_bell_name = check_str($_POST["school_bell_name"]);
		$school_bell_leg_a_type = check_str($_POST["school_bell_leg_a_type"]);
		$school_bell_leg_a_data = check_str($_POST["school_bell_leg_a_data"]);
		$school_bell_leg_b_type = check_str($_POST["school_bell_leg_b_type"]);
		$school_bell_leg_b_data = check_str($_POST["school_bell_leg_b_data"]);
		$school_bell_ring_timeout = (int)check_str($_POST["school_bell_ring_timeout"]);
		$school_bell_min = (int)check_str($_POST["school_bell_min"]);
		$school_bell_hour = (int)check_str($_POST["school_bell_hour"]);
		$school_bell_dom = (int)check_str($_POST["school_bell_dom"]);
		$school_bell_mon = (int)check_str($_POST["school_bell_mon"]);
		$school_bell_dow = (int)check_str($_POST["school_bell_dow"]);
		$school_bell_timezone = check_str($_POST["school_bell_timezone"]);
		$school_bell_enabled = check_str($_POST["school_bell_enabled"]);
		$school_bell_description = $_POST["school_bell_description"];
	
			// Filter values:
		if (strlen($school_bell_leg_a_type) == 0) {
			$school_bell_leg_a_type = "loopback/";
		}

		if (strlen($school_bell_leg_b_data) > 0) {
			$school_bell_leg_b_type = $_SESSION['switch']['recordings']['dir']."/".$_SESSION['domain_name']."/".$school_bell_leg_b_data;
		}

		// Set default ring timeout to 3 sec
		if ($school_bell_ring_timeout < 0) {
			$school_bell_ring_timeout = 3;
		}

		if ($school_bell_min != -1 && ($school_bell_min > 59 || $school_bell_min < 0)) {
			$school_bell_min = 0;
		}

		if ($school_bell_hour != -1 && ($school_bell_hour < 0 || $school_bell_hour > 23)) {
			$school_bell_hour = 0;
		}

		if ($school_bell_dom != -1 && ($school_bell_dom < 1 || $school_bell_dom > 31)) {
			$school_bell_dom = 1;
		}

		if ($school_bell_mon != -1 && ($school_bell_mon < 1 || $school_bell_mon > 12)) {
			$school_bell_mon = 1;
		}

		if ($school_bell_dow != -1 && ($school_bell_dow < 0 || $school_bell_dow > 6)) {
			$school_bell_dow = 0;
		}

		if (!in_array($school_bell_timezone, $timezone_identifiers_list)) {
			$school_bell_timezone = date_default_timezone_get();
		}

		if (strlen($school_bell_enabled) == 0) {
			$school_bell_enabled = 'true';
		}
	}
//handle the http post
	if (count($_POST) > 0 && strlen($_POST["persistformvar"]) == 0) {
	
		$msg = '';
	
		//check for all required data
		if (strlen($school_bell_name) == 0) { 
			$msg .= $text['label-school_bell_name']."<br>\n"; 
		}
		if (strlen($school_bell_leg_a_data) == 0) {
			$msg .= $text['label-school_bell_leg_a_data']."<br>\n"; 
		}

		if (strlen($msg) > 0 && strlen($_POST["persistformvar"]) == 0) {
			require_once "resources/header.php";
			require_once "resources/persist_form_var.php";
			echo "<div align='center'>\n";
			echo "<table><tr><td>\n";
			echo $msg."<br />";
			echo "</td></tr></table>\n";
			persistformvar($_POST);
			echo "</div>\n";
			require_once "resources/footer.php";
			return;
		}

	//add or update the database
		if (($_POST["persistformvar"] != "true") > 0) {

			if ($action == "add") {

				$school_bell_uuid = uuid();

				$sql = "INSERT INTO v_school_bells";
				$sql .= " (";
				$sql .= "domain_uuid, ";
				$sql .= "school_bell_uuid, ";
				$sql .= "school_bell_name, ";
				$sql .= "school_bell_leg_a_type, ";
				$sql .= "school_bell_leg_a_data, ";
				$sql .= "school_bell_leg_b_type, ";
				$sql .= "school_bell_leg_b_data, ";
				$sql .= "school_bell_ring_timeout, ";
				$sql .= "school_bell_min, ";
				$sql .= "school_bell_hour, ";
				$sql .= "school_bell_dom, ";
				$sql .= "school_bell_mon, ";
				$sql .= "school_bell_dow, ";
				$sql .= "school_bell_timezone, ";
				$sql .= "school_bell_enabled, ";
				$sql .= "school_bell_description ";
				$sql .= ") ";
				$sql .= "VALUES ";
				$sql .= "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

				$insert_array = array(
					'domain_uuid' => $domain_uuid,
					'school_bell_uuid' => $school_bell_uuid,
					'school_bell_name' => $school_bell_name,
					'school_bell_leg_a_type' => $school_bell_leg_a_type,
					'school_bell_leg_a_data' => $school_bell_leg_a_data,
					'school_bell_leg_b_type' => $school_bell_leg_b_type,
					'school_bell_leg_b_data' => $school_bell_leg_b_data,
					'school_bell_ring_timeout' => $school_bell_ring_timeout,
					'school_bell_min' => $school_bell_min,
					'school_bell_hour' => $school_bell_hour,
					'school_bell_dom' => $school_bell_dom,
					'school_bell_mon' => $school_bell_mon,
					'school_bell_dow' => $school_bell_dow,
					'school_bell_timezone' => $school_bell_timezone,
					'school_bell_enabled' => $school_bell_enabled,
					'school_bell_description' => $school_bell_description
				);

				$prep_statement = $db->prepare(check_sql($sql));
				$prep_statement->execute(array_values($insert_array));

				unset($sql, $prep_statement, $insert_array);

				message::add($text['label-add-complete']);
				header("Location: school_bells.php");
				return;
			} //if ($action == "add")

			if ($action == "update") {

				$sql = "UPDATE v_school_bells SET ";
				$sql .= "school_bell_name = :school_bell_name, ";
				$sql .= "school_bell_leg_a_type = :school_bell_leg_a_type, ";
				$sql .= "school_bell_leg_a_data = :school_bell_leg_a_data, ";
				$sql .= "school_bell_leg_b_type = :school_bell_leg_b_type, ";
				$sql .= "school_bell_leg_b_data = :school_bell_leg_b_data, ";
				$sql .= "school_bell_ring_timeout = :school_bell_ring_timeout, ";
				$sql .= "school_bell_min = :school_bell_min, ";
				$sql .= "school_bell_hour = :school_bell_hour, ";
				$sql .= "school_bell_dom = :school_bell_dom, ";
				$sql .= "school_bell_mon = :school_bell_mon, ";
				$sql .= "school_bell_dow = :school_bell_dow, ";
				$sql .= "school_bell_timezone = :school_bell_timezone, ";
				$sql .= "school_bell_enabled = :school_bell_enabled, ";
				$sql .= "school_bell_description = :school_bell_description";
				$sql .= " WHERE domain_uuid = :domain_uuid";
				$sql .= " AND school_bell_uuid = :school_bell_uuid";

				$prep_statement = $db->prepare($sql);

				$prep_statement->bindValue('school_bell_name', $school_bell_name);
				$prep_statement->bindValue('school_bell_leg_a_type', $school_bell_leg_a_type);
				$prep_statement->bindValue('school_bell_leg_a_data', $school_bell_leg_a_data);
				$prep_statement->bindValue('school_bell_leg_b_type', $school_bell_leg_b_type);
				$prep_statement->bindValue('school_bell_leg_b_data', $school_bell_leg_b_data);
				$prep_statement->bindValue('school_bell_ring_timeout', $school_bell_ring_timeout);
				$prep_statement->bindValue('school_bell_min', $school_bell_min);
				$prep_statement->bindValue('school_bell_hour', $school_bell_hour);
				$prep_statement->bindValue('school_bell_dom', $school_bell_dom);
				$prep_statement->bindValue('school_bell_mon', $school_bell_mon);
				$prep_statement->bindValue('school_bell_dow', $school_bell_dow);
				$prep_statement->bindValue('school_bell_timezone', $school_bell_timezone);
				$prep_statement->bindValue('school_bell_enabled', $school_bell_enabled);
				$prep_statement->bindValue('school_bell_description', $school_bell_description);
				$prep_statement->bindValue('domain_uuid', $domain_uuid);
				$prep_statement->bindValue('school_bell_uuid', $school_bell_uuid);
				
				if ($prep_statement) {
					$prep_statement->execute();
				}
				unset($sql, $prep_statement);

				message::add($text['label-update-complete']);
				header("Location: school_bells.php");
				return;
			} //if ($action == "update")
		} //if ($_POST["persistformvar"] != "true")
	} //(count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

//pre-populate the form
	if (count($_GET) > 0 && $_POST["persistformvar"] != "true") {
		$school_bell_uuid = $_GET["id"];
		$sql = "SELECT * FROM v_school_bells";
		$sql .= " WHERE domain_uuid = :domain_uuid";
		$sql .= " AND school_bell_uuid = :school_bell_uuid";
		$sql .= " LIMIT 1";

		$prep_statement = $db->prepare($sql);

		$prep_statement->bindValue('domain_uuid', $domain_uuid);
		$prep_statement->bindValue('school_bell_uuid', $school_bell_uuid);

		$prep_statement->execute();
		$result = $prep_statement->fetchAll();

		foreach ($result as &$row) {
			$school_bell_name = $row["school_bell_name"];
			$school_bell_leg_a_type = $row["school_bell_leg_a_type"];
			$school_bell_leg_a_data = $row["school_bell_leg_a_data"];
			$school_bell_leg_b_type = $row["school_bell_leg_b_type"];
			$school_bell_leg_b_data = $row["school_bell_leg_b_data"];
			$school_bell_min = $row["school_bell_min"];
			$school_bell_hour = $row["school_bell_hour"];
			$school_bell_dom = $row["school_bell_dom"];
			$school_bell_mon = $row["school_bell_mon"];
			$school_bell_dow = $row["school_bell_dow"];
			$school_bell_timezone = $row["school_bell_timezone"];
			$school_bell_enabled = $row["school_bell_enabled"];
			$school_bell_description = $row["school_bell_description"];
			break; //limit to 1 row ? Should be only 1 result at all
		}
		unset ($prep_statement, $sql);
	}

	//get the recordings
	$sql = "SELECT recording_name, recording_filename FROM v_recordings";
	$sql .= " WHERE domain_uuid = :domain_uuid";
	$sql .= " ORDER BY recording_name ASC";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->bindValue('domain_uuid', $domain_uuid);
	$prep_statement->execute();
	$recordings = $prep_statement->fetchAll(PDO::FETCH_ASSOC);

	//get the phrases
	// $sql = "SELECT * FROM v_phrases ";
	// $sql .= " WHERE (domain_uuid = :domain_uuid OR domain_uuid IS NULL) ";
	// $prep_statement = $db->prepare(check_sql($sql));
	// $prep_statement->bindValue('domain_uuid', $domain_uuid);
	// $prep_statement->execute();
	// $phrases = $prep_statement->fetchAll(PDO::FETCH_NAMED);

	//get the sound files
	$file = new file;
	$sound_files = $file->sounds();

	$school_bell_selector = new school_bell_selector($_SESSION['domain']['time_format']['text'] == '12h');

	// One of defaults
	if (strlen($school_bell_timezone) == 0) {
		$school_bell_timezone = date_default_timezone_get();
	}

//show the header
	require_once "resources/header.php";

//show the content

	echo "<form method='post' name='frm' action=''>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	if ($action == "add") {
		echo "<td align='left' width='30%' nowrap='nowrap'><b>".$text['label-school_bells-add']."</b></td>\n";
	}
	if ($action == "update") {
		echo "<td align='left' width='30%' nowrap='nowrap'><b>".$text['label-school_bells-edit']."</b></td>\n";
	}
	echo "<td width='70%' align='right'>";
	echo "	<input type='button' class='btn' name='' alt='".$text['button-back']."' onclick=\"window.location='school_bells.php'\" value='".$text['button-back']."'>";
	echo "	<input type='submit' name='submit' class='btn' value='".$text['button-save']."'>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td align='left' colspan='2'>\n";
	if ($action == "add") {
		echo $text['label-school_bells-add-note']."<br /><br />\n";
	}
	if ($action == "update") {
		echo $text['label-school_bells-edit-note']."<br /><br />\n";
	}
	echo "</td>\n";
	echo "</tr>\n";

	// Show name
	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-school_bell_name']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='school_bell_name' maxlength='255' value=\"".escape($school_bell_name)."\" required='required'>\n";
	echo "<br />\n";
	echo $text['description-school_bell_name']."\n";
	echo "</td>\n";
	echo "</tr>\n";

	// Show school_bell_leg_a_data
	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-school_bell_leg_a_data']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='school_bell_leg_a_data' maxlength='255' value=\"".escape($school_bell_leg_a_data)."\" required='required'>\n";
	echo "<br />\n";
	echo $text['description-school_bell_leg_a_data']."\n";
	echo "</td>\n";
	echo "</tr>\n";

	// Show ring timeout
	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-school_bell_ring_timeout']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='number' name='school_bell_ring_timeout' min='0' max='3600' value=\"".escape($school_bell_ring_timeout)."\">\n";
	echo "<br />\n";
	echo $text['description-school_bell_ring_timeout']."\n";
	echo "</td>\n";
	echo "</tr>\n";

	// Show school_bell_leg_b_data
	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "	".$text['label-school_bell_leg_b_data']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "<select name='school_bell_leg_b_data' id='school_bell_leg_b_data' class='formfld'>\n";
	echo "	<option></option>\n";
	//recordings
	$tmp_selected = false;
	if (is_array($recordings)) {
		echo "<optgroup label='Recordings'>\n";
		foreach ($recordings as &$row) {
			$recording_name = $row["recording_name"];
			$recording_filename = $row["recording_filename"];
			if ($school_bell_leg_b_data == $_SESSION['switch']['recordings']['dir']."/".$_SESSION['domain_name']."/".$recording_filename && strlen($school_bell_leg_b_data) > 0) {
				$tmp_selected = true;
				echo "	<option value='".escape($_SESSION['switch']['recordings']['dir'])."/".escape($_SESSION['domain_name'])."/".escape($recording_filename)."' selected='selected'>".escape($recording_name)."</option>\n";
			}
			else if ($school_bell_leg_b_data == $recording_filename && strlen($school_bell_leg_b_data) > 0) {
				$tmp_selected = true;
				echo "	<option value='".escape($recording_filename)."' selected='selected'>".escape($recording_name)."</option>\n";
			}
			else {
				echo "	<option value='".escape($recording_filename)."'>".escape($recording_name)."</option>\n";
			}
		}
		echo "</optgroup>\n";
	}

	if (if_group("superadmin")) {
		if (!$tmp_selected && strlen($school_bell_leg_b_data) > 0) {
			echo "<optgroup label='Selected'>\n";
			if (file_exists($_SESSION['switch']['recordings']['dir']."/".$_SESSION['domain_name']."/".$school_bell_leg_b_data)) {
				echo "	<option value='".escape($_SESSION['switch']['recordings']['dir'])."/".escape($_SESSION['domain_name'])."/".escape($school_bell_leg_b_data)."' selected='selected'>".escape($school_bell_leg_b_data)."</option>\n";
			}
			else if (substr($school_bell_leg_b_data, -3) == "wav" || substr($school_bell_leg_b_data, -3) == "mp3") {
				echo "	<option value='".escape($school_bell_leg_b_data)."' selected='selected'>".escape($school_bell_leg_b_data)."</option>\n";
			}
			else {
				echo "	<option value='".escape($school_bell_leg_b_data)."' selected='selected'>".escape($school_bell_leg_b_data)."</option>\n";
			}
			echo "</optgroup>\n";
		}
		unset($tmp_selected);
	}

	echo "	</select>\n";
	if (if_group("superadmin")) {
		echo "<input type='button' id='btn_select_to_input_".escape($destination_id)."' class='btn' name='' alt='back' onclick='changeToInput".escape($destination_id)."(document.getElementById(\"".escape($destination_id)."\"));this.style.visibility = \"hidden\";' value='&#9665;'>";
		unset($destination_id);
	}
	echo "	<br />\n";
	echo $text['description-school_bell_leg_b_data']."\n";
	echo "</td>\n";
	echo "</tr>\n";

	// End school_bell_leg_b_data
	// Show divider
	echo "<tr><td colspan='2'><br /></td></tr>\n";

	// Show Min
	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-school_bell_min']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo $school_bell_selector->draw_min('school_bell_min', $school_bell_min);
	echo "<br />\n";
	echo $text['description-school_bell_min']."\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	// Show Hour
	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-school_bell_hour']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo $school_bell_selector->draw_hou('school_bell_hour', $school_bell_hour);
	echo "<br />\n";
	echo $text['description-school_bell_hour']."\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	// Show Day of the Month
	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-school_bell_dom']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo $school_bell_selector->draw_dom('school_bell_dom', $school_bell_dom);
	echo "<br />\n";
	echo $text['description-school_bell_dom']."\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	// Show Month
	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-school_bell_mon']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo $school_bell_selector->draw_mon('school_bell_mon', $school_bell_mon);
	echo "<br />\n";
	echo $text['description-school_bell_mon']."\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	// Show Day of the Week
	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-school_bell_dow']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo $school_bell_selector->draw_dow('school_bell_dow', $school_bell_dow);
	echo "<br />\n";
	echo $text['description-school_bell_dow']."\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	// Show timezone
	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-school_bell_timezone']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<select class='formfld' name='school_bell_timezone'>\n";
	foreach ($timezone_identifiers_list as $timezone_identifier) {
		echo "		<option value='$timezone_identifier' ".(($school_bell_timezone == $timezone_identifier) ? "selected" : null).">".escape($timezone_identifier)."</option>\n";
	}
	echo "	</select>\n";
	echo "<br />\n";
	echo $text['description-school_bell_timezone']."\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	// Show divider
	echo "<tr><td colspan='2'><br /></td></tr>\n";

	// Show enabled
	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-school_bell_enabled']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<select class='formfld' name='school_bell_enabled'>\n";
	echo "		<option value='true' ".(($school_bell_enabled == "true") ? "selected" : null).">".$text['label-true']."</option>\n";
	echo "		<option value='false' ".(($school_bell_enabled == "false") ? "selected" : null).">".$text['label-false']."</option>\n";
	echo "	</select>\n";
	echo "<br />\n";
	echo $text['description-school_bell_enabled']."\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	// Show description
	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-school_bell_description']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='school_bell_description' maxlength='255' value=\"".escape($school_bell_description)."\">\n";
	echo "<br />\n";
	echo $text['description-school_bell_description']."\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	if ($action == "update") {
		echo "		<input type='hidden' name='id' value='".escape($school_bell_uuid)."'>\n";
	}
	echo "			<br>";
	echo "			<input type='submit' name='submit' class='btn' value='".$text['button-save']."'>\n";
	echo "		</td>\n";
	echo "	</tr>";
	echo "</table>";
	echo "<br><br>";
	echo "</form>";

	echo "<table>";
	echo "<tr>";
	echo "<td>";
	echo $text['description-school_bells_schedule_templates'];
	echo "</td>";
	echo "</tr>";
	echo "</table>";

//include the footer
	require_once "resources/footer.php";

?>
