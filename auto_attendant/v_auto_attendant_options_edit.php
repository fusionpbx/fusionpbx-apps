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
	Portions created by the Initial Developer are Copyright (C) 2008-2010
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
include "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (ifgroup("admin") || ifgroup("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}


//Action add or update
if (isset($_REQUEST["id"])) {
	$action = "update";
	$auto_attendant_option_id = check_str($_REQUEST["id"]);
}
else {
	$action = "add";
	$auto_attendant_id = check_str($_REQUEST["id2"]);
}

if (isset($_REQUEST["id2"])) {
	$auto_attendant_id = check_str($_REQUEST["id2"]);
}
if (isset($_REQUEST["optionaction"])) {
	$optionaction = $_REQUEST["optionaction"];
}

//POST to PHP variables
if (count($_POST)>0) {
	//$v_id = check_str($_POST["v_id"]);
	if (isset($_REQUEST["dialplan_include_id"])) {
		$auto_attendant_id = check_str($_POST["auto_attendant_id"]);
	}
	$optionaction = check_str($_POST["optionaction"]);
	$optionnumber = check_str($_POST["optionnumber"]);
	$optiontype = check_str($_POST["optiontype"]);
	$optionprofile = check_str($_POST["optionprofile"]);
	$optionrecording = check_str($_POST["optionrecording"]);
	$optiondata = check_str($_POST["optiondata"]);
	$optiondescr = check_str($_POST["optiondescr"]);
}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';

	////recommend moving this to the config.php file
	$uploadtempdir = $_ENV["TEMP"]."\\";
	ini_set('upload_tmp_dir', $uploadtempdir);
	////$imagedir = $_ENV["TEMP"]."\\";
	////$filedir = $_ENV["TEMP"]."\\";

	if ($action == "update") {
		$auto_attendant_option_id = check_str($_POST["auto_attendant_option_id"]);
	}

	//check for all required data
		if (strlen($v_id) == 0) { $msg .= "Please provide: v_id<br>\n"; }
		if (strlen($auto_attendant_id) == 0) { $msg .= "Please provide: auto_attendant_id<br>\n"; }
		//if (strlen($optionaction) == 0) { $msg .= "Please provide: Action<br>\n"; }
		if (strlen($optionnumber) == 0) { $msg .= "Please provide: Option Number<br>\n"; }
		if (strlen($optiontype) == 0) { $msg .= "Please provide: Type<br>\n"; }
		if (strlen($optionprofile) == 0) { $msg .= "Please provide: Profile<br>\n"; }
		if (strlen($optiondata) == 0) { $msg .= "Please provide: Destination<br>\n"; }
		//if (strlen($optionrecording) == 0) { $msg .= "Please provide: Recording<br>\n"; }
		//if (strlen($optiondescr) == 0) { $msg .= "Please provide: Description<br>\n"; }
		if (strlen($msg) > 0 && strlen($_POST["persistformvar"]) == 0) {
			require_once "includes/header.php";
			require_once "includes/persistformvar.php";
			echo "<div align='center'>\n";
			echo "<table><tr><td>\n";
			echo $msg."<br />";
			echo "</td></tr></table>\n";
			persistformvar($_POST);
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		}

	//Add or update the database
	if ($_POST["persistformvar"] != "true") {
		if ($action == "add") {
			$sql = "insert into v_auto_attendant_options ";
			$sql .= "(";
			$sql .= "v_id, ";
			$sql .= "auto_attendant_id, ";
			$sql .= "optionaction, ";
			$sql .= "optionnumber, ";
			$sql .= "optiontype, ";
			$sql .= "optionprofile, ";
			$sql .= "optiondata, ";
			$sql .= "optionrecording, ";
			$sql .= "optiondescr ";
			$sql .= ")";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$v_id', ";
			$sql .= "'$auto_attendant_id', ";
			$sql .= "'$optionaction', ";
			$sql .= "'$optionnumber', ";
			$sql .= "'$optiontype', ";
			$sql .= "'$optionprofile', ";
			$sql .= "'$optiondata', ";
			$sql .= "'$optionrecording', ";
			$sql .= "'$optiondescr' ";
			$sql .= ")";
			$db->exec(check_sql($sql));
			unset($sql);

			//synchronize the xml config
			sync_package_v_auto_attendant();

			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_auto_attendant_edit.php?id=".$auto_attendant_id."\">\n";
			echo "<div align='center'>\n";
			echo "Add Complete\n";
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		} //if ($action == "add")

		if ($action == "update") {
			$sql = "update v_auto_attendant_options set ";
			$sql .= "auto_attendant_id = '$auto_attendant_id', ";
			$sql .= "optionaction = '$optionaction', ";
			$sql .= "optionnumber = '$optionnumber', ";
			$sql .= "optiontype = '$optiontype', ";
			$sql .= "optionprofile = '$optionprofile', ";
			$sql .= "optiondata = '$optiondata', ";
			$sql .= "optionrecording = '$optionrecording', ";
			$sql .= "optiondescr = '$optiondescr' ";
			$sql .= "where v_id = '$v_id' ";
			$sql .= "and auto_attendant_option_id = '$auto_attendant_option_id'";
			$db->exec(check_sql($sql));
			unset($sql);

			//synchronize the xml config
			sync_package_v_auto_attendant();

			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_auto_attendant_edit.php?id=".$auto_attendant_id."\">\n";
			echo "<div align='center'>\n";
			echo "Update Complete\n";
			echo "</div>\n";
			require_once "includes/footer.php";

			return;
		} //if ($action == "update")
	} //if ($_POST["persistformvar"] != "true") { 

} //(count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

//Pre-populate the form
if (count($_GET)>0 && $_POST["persistformvar"] != "true") {
	$auto_attendant_option_id = $_GET["id"];
	$sql = "";
	$sql .= "select * from v_auto_attendant_options ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and auto_attendant_option_id = '$auto_attendant_option_id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		$auto_attendant_id = $row["auto_attendant_id"];
		$optionaction = $row["optionaction"];
		$optionnumber = $row["optionnumber"];
		$optiontype = $row["optiontype"];
		$optionprofile = $row["optionprofile"];
		$optiondata = $row["optiondata"];
		$optionrecording = $row["optionrecording"];
		$optiondescr = $row["optiondescr"];
		break; //limit to 1 row
	}
	unset ($prepstatement);
}


	require_once "includes/header.php";


	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "		<br>";


//Write the HTML form
	echo "<form method='post' name='frm' action=''>\n";

	echo "<div align='center'>\n";
	echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";

	echo "<tr>\n";
	if ($action == "add") {
		echo "<td align='left' width='30%' nowrap><b>Option Add</b></td>\n";
	}
	if ($action == "update") {
		echo "<td align='left' width='30%' nowrap><b>Options Edit</b></td>\n";
	}
	echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_auto_attendant_edit.php?id=".$auto_attendant_id."'\" value='Back'></td>\n";
	echo "</tr>\n";


	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Option Number:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='optionnumber' maxlength='255' value=\"$optionnumber\">\n";
	echo "<br />\n";
	echo "Any number 1-5 digits. The following are special options:<br />\n";
	echo "'n' now (don't wait for dtmf perform the action now) <br />\n";
	echo "'d' default (if the caller presses an incorrect number the default action is used) <br />\n";
	echo "'t' timeout (the action to perform after the call timeout passes) <br />\n";
	echo "</td>\n";
	echo "</tr>\n";

/*
	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Type:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "                <select name='optiontype' class='formfld'>\n";
	echo "                <option></option>\n";
	if ($optiontype == "extension") {
		echo "                <option selected='yes'>extension</option>\n";
	}
	else {
		echo "                <option>extension</option>\n";
	}
	if ($optiontype == "voicemail") {
		echo "                <option selected='yes'>voicemail</option>\n";
	}
	else {
		echo "                <option>voicemail</option>\n";
	}
	if ($optiontype == "sip uri") {
		echo "                <option selected='yes'>sip uri</option>\n";
	}
	else {
		echo "                <option>sip uri</option>\n";
	}
	echo "                </select>\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";
*/

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Type:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='optiontype' maxlength='255' value=\"$optiontype\"><br />\n";
	echo "bridge, transfer, voicemail, conference, fifo, etc.<br />\n";
	echo "</td>\n";
	echo "</tr>\n";


	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Destination:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='optiondata' maxlength='255' value=\"$optiondata\">\n";
	echo "<br />\n";
	echo "type: transfer data: 1001 XML default<br />\n";
	echo "type: voicemail data: default \${domain} 1001<br />\n";
	echo "type: bridge data: (voicemail): sofia/internal/*98@\${domain}<br />\n";
	echo "type: bridge data: (external number): sofia/gateway/gatewayname/12081231234<br />\n";
	echo "type: bridge data: (auto attendant): sofia/internal/5002@\${domain}<br />\n";
	echo "type: bridge data: (user): /user/1001@\${domain}<br />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Profile:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "                <select name='optionprofile' class='formfld'>\n";
	echo "                <option></option>\n";
	if ($optionprofile == "auto") {
		echo "                <option selected='yes'>auto</option>\n";
	}
	else {
		echo "                <option>auto</option>\n";
	}

	foreach (ListFiles($v_conf_dir.'/sip_profiles') as $key=>$sip_profile_file){
		$sip_profile_name = str_replace(".xml", "", $sip_profile_file);

		if ($optionprofile == $sip_profile_name) {
			echo "                <option selected='yes'>$sip_profile_name</option>\n";
		}
		else {
			echo "                <option>$sip_profile_name</option>\n";
		}
	}
	echo "                </select>\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Recording:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "		<select name='optionrecording' class='formfld'>\n";
	echo "		<option></option>\n";

	$sql = "";
	$sql .= "select * from v_recordings ";
	$sql .= "where v_id = '$v_id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		if ($optionrecording == $row['recording_id']) {
			echo "		<option value='".$row['recording_id']."' selected='yes'>".$row['recordingname']."</option>\n";
		}
		else {
			echo "		<option value='".$row['recording_id']."'>".$row['recordingname']."</option>\n";
		}
	}
	unset ($prepstatement);

	echo "		</select><br />\n";
	echo "Optional, the recording to play before the call is sent to the destination.<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Description:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='optiondescr' maxlength='255' value=\"$optiondescr\">\n";
	echo "<br />\n";
	echo "You may enter a description here for your reference (not parsed).\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	echo "				<input type='hidden' name='auto_attendant_id' value='$auto_attendant_id'>\n";
	echo "				<input class='formfld' type='hidden' name='optionaction' maxlength='255' value=\"$optionaction\">\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='auto_attendant_option_id' value='$auto_attendant_option_id'>\n";
	}
	echo "				<input type='submit' name='submit' class='btn' value='Save'>\n";
	echo "		</td>\n";
	echo "	</tr>";
	echo "</table>";
	echo "</form>";


	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";


require_once "includes/footer.php";
?>
