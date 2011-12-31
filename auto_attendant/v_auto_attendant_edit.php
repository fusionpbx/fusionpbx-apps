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
require_once "includes/paging.php";
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
	$auto_attendant_id = check_str($_REQUEST["id"]);
}
else {
	$action = "add";
}

//POST to PHP variables
if (count($_POST)>0) {
	//$v_id = check_str($_POST["v_id"]);
	$aa_extension = check_str($_POST["aa_extension"]);
	$aa_name = check_str($_POST["aa_name"]);
	$recording_id_action = check_str($_POST["recording_id_action"]);
	$recording_id_anti_action = check_str($_POST["recording_id_anti_action"]);
	$aa_timeout = check_str($_POST["aa_timeout"]);
	$aa_call_timeout = check_str($_POST["aa_call_timeout"]);
	$aa_context = check_str($_POST["aa_context"]);
	$aa_direct_dial = check_str($_POST["aa_direct_dial"]);
	$aa_ringback = check_str($_POST["aa_ringback"]);
	$aa_cid_name_prefix = check_str($_POST["aa_cid_name_prefix"]);
	$aa_condition_js = check_str($_POST["aa_condition_js"]);
	$aa_descr = check_str($_POST["aa_descr"]);
}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';

	////recommend moving this to the config.php file
	$uploadtempdir = $_ENV["TEMP"]."\\";
	ini_set('upload_tmp_dir', $uploadtempdir);
	////$imagedir = $_ENV["TEMP"]."\\";
	////$filedir = $_ENV["TEMP"]."\\";

	if ($action == "update") {
		$auto_attendant_id = check_str($_POST["auto_attendant_id"]);
	}

	//check for all required data
		if (strlen($v_id) == 0) { $msg .= "Please provide: v_id<br>\n"; }
		if (strlen($aa_extension) == 0) { $msg .= "Please provide: Extension<br>\n"; }
		if (strlen($aa_name) == 0) { $msg .= "Please provide: Name<br>\n"; }
		if (strlen($recording_id_action) == 0) { $msg .= "Please provide: Recording Action<br>\n"; }
		if (strlen($recording_id_anti_action) == 0) { $msg .= "Please provide: Recording Anti-Action<br>\n"; }
		if (strlen($aa_timeout) == 0) { $msg .= "Please provide: Timeout<br>\n"; }
		if (strlen($aa_call_timeout) == 0) { $msg .= "Please provide: Call Timeout<br>\n"; }
		if (strlen($aa_context) == 0) { $msg .= "Please provide: Context<br>\n"; }
		if (strlen($aa_direct_dial) == 0) { $msg .= "Please provide: Direct Dial<br>\n"; }
		if (strlen($aa_ringback) == 0) { $msg .= "Please provide: Ring Back<br>\n"; }
		//if (strlen($aa_cid_name_prefix) == 0) { $msg .= "Please provide: CID Prefix<br>\n"; }
		//if (strlen($aa_condition_js) == 0) { $msg .= "Please provide: Javascript Condition<br>\n"; }
		//if (strlen($aa_descr) == 0) { $msg .= "Please provide: Description<br>\n"; }
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

	$tmp = "\n";
	//$tmp .= "v_id: $v_id\n";
	$tmp .= "Extension: $aa_extension\n";
	$tmp .= "Name: $aa_name\n";
	$tmp .= "Recording Action: $recording_id_action\n";
	$tmp .= "Recording Anti-Action: $recording_id_anti_action\n";
	$tmp .= "Timeout: $aa_timeout\n";
	$tmp .= "Call Timeout: $aa_call_timeout\n";
	$tmp .= "Context: $aa_context\n";
	$tmp .= "Direct Dial: $aa_direct_dial\n";
	$tmp .= "Ring Back: $aa_ringback\n";
	$tmp .= "CID Prefix: $aa_cid_name_prefix\n";
	$tmp .= "Javascript Condition: $aa_condition_js\n";
	$tmp .= "Description: $aa_descr\n";

	//Add or update the database
	if ($_POST["persistformvar"] != "true") {
		if ($action == "add") {
			$sql = "insert into v_auto_attendant ";
			$sql .= "(";
			$sql .= "v_id, ";
			$sql .= "aa_extension, ";
			$sql .= "aa_name, ";
			$sql .= "recording_id_action, ";
			$sql .= "recording_id_anti_action, ";
			$sql .= "aa_timeout, ";
			$sql .= "aa_call_timeout, ";
			$sql .= "aa_context, ";
			$sql .= "aa_direct_dial, ";
			$sql .= "aa_ringback, ";
			$sql .= "aa_cid_name_prefix, ";
			$sql .= "aa_condition_js, ";
			$sql .= "aa_descr ";
			$sql .= ")";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$v_id', ";
			$sql .= "'$aa_extension', ";
			$sql .= "'$aa_name', ";
			$sql .= "'$recording_id_action', ";
			$sql .= "'$recording_id_anti_action', ";
			$sql .= "'$aa_timeout', ";
			$sql .= "'$aa_call_timeout', ";
			$sql .= "'$aa_context', ";
			$sql .= "'$aa_direct_dial', ";
			$sql .= "'$aa_ringback', ";
			$sql .= "'$aa_cid_name_prefix', ";
			$sql .= "'$aa_condition_js', ";
			$sql .= "'$aa_descr' ";
			$sql .= ")";
			$db->exec(check_sql($sql));
			unset($sql);

			//synchronize the xml config
			sync_package_v_auto_attendant();

			//synchronize the xml config
			sync_package_v_dialplan_includes();

			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_auto_attendant.php\">\n";
			echo "<div align='center'>\n";
			echo "Add Complete\n";
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		} //if ($action == "add")

		if ($action == "update") {
			$sql = "update v_auto_attendant set ";
			$sql .= "v_id = '$v_id', ";
			$sql .= "aa_extension = '$aa_extension', ";
			$sql .= "aa_name = '$aa_name', ";
			$sql .= "recording_id_action = '$recording_id_action', ";
			$sql .= "recording_id_anti_action = '$recording_id_anti_action', ";
			$sql .= "aa_timeout = '$aa_timeout', ";
			$sql .= "aa_call_timeout = '$aa_call_timeout', ";
			$sql .= "aa_context = '$aa_context', ";
			$sql .= "aa_direct_dial = '$aa_direct_dial', ";
			$sql .= "aa_ringback = '$aa_ringback', ";
			$sql .= "aa_cid_name_prefix = '$aa_cid_name_prefix', ";
			$sql .= "aa_condition_js = '$aa_condition_js', ";
			$sql .= "aa_descr = '$aa_descr' ";
			$sql .= "where v_id = '$v_id' ";
			$sql .= "and auto_attendant_id = '$auto_attendant_id'";
			$db->exec(check_sql($sql));
			unset($sql);

			//synchronize the xml config
			sync_package_v_auto_attendant();

			//synchronize the xml config
			sync_package_v_dialplan_includes();

			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_auto_attendant.php\">\n";
			echo "<div align='center'>\n";
			echo "Update Complete\n";
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		} //if ($action == "update")
	} //if ($_POST["persistformvar"] != "true") { 

} //(count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

//set default $aa_condition_js
	$aa_condition_js_default = "function isholiday( Month, Date ) {\n";
	$aa_condition_js_default .= "    var Holiday = 0; //default false\n";
	$aa_condition_js_default .= "    if (Month == \"12\" && Date == \"25\") {\n";
	$aa_condition_js_default .= "      Holiday = 1; //true\n";
	$aa_condition_js_default .= "    }\n";
	$aa_condition_js_default .= "    if (Month == \"7\" && Date == \"4\") {\n";
	$aa_condition_js_default .= "      Holiday = 1; //true\n";
	$aa_condition_js_default .= "    }\n";
	$aa_condition_js_default .= "    if (Month == \"1\" && Date == \"1\") {\n";
	$aa_condition_js_default .= "      Holiday = 1; //true\n";
	$aa_condition_js_default .= "    }\n";
	$aa_condition_js_default .= "    if (Holiday == 1) {\n";
	$aa_condition_js_default .= "      return true;\n";
	$aa_condition_js_default .= "    }\n";
	$aa_condition_js_default .= "    else {\n";
	$aa_condition_js_default .= "      return false;\n";
	$aa_condition_js_default .= "    }\n";
	$aa_condition_js_default .= "}\n";
	$aa_condition_js_default .= "\n";
	$aa_condition_js_default .= "function isweekday( Day ) {\n";
	$aa_condition_js_default .= "    if (Day > 1 && Day < 7) {\n";
	$aa_condition_js_default .= "        return true;\n";
	$aa_condition_js_default .= "    }\n";
	$aa_condition_js_default .= "    else {\n";
	$aa_condition_js_default .= "        return false;\n";
	$aa_condition_js_default .= "    }\n";
	$aa_condition_js_default .= "}\n";
	$aa_condition_js_default .= "\n";
	$aa_condition_js_default .= "function isweekend( Day ) {\n";
	$aa_condition_js_default .= "    if (Day > 1 && Day < 7) {\n";
	$aa_condition_js_default .= "        return false;\n";
	$aa_condition_js_default .= "    }\n";
	$aa_condition_js_default .= "    else {\n";
	$aa_condition_js_default .= "        return true;\n";
	$aa_condition_js_default .= "    }\n";
	$aa_condition_js_default .= "}\n";
	$aa_condition_js_default .= "\n";
	$aa_condition_js_default .= "function isofficehours( Hours ) {\n";
	$aa_condition_js_default .= "    if (Hours >= 9 && Hours < 17) {\n";
	$aa_condition_js_default .= "        return true;\n";
	$aa_condition_js_default .= "    }\n";
	$aa_condition_js_default .= "    else {\n";
	$aa_condition_js_default .= "        return false;\n";
	$aa_condition_js_default .= "    }\n";
	$aa_condition_js_default .= "}\n";
	$aa_condition_js_default .= "\n";
	$aa_condition_js_default .= "function isafterhours( Hours ) {\n";
	$aa_condition_js_default .= "    if (Hours >= 9 && Hours < 17) {\n";
	$aa_condition_js_default .= "        return false;\n";
	$aa_condition_js_default .= "    }\n";
	$aa_condition_js_default .= "    else {\n";
	$aa_condition_js_default .= "        return true;\n";
	$aa_condition_js_default .= "    }\n";
	$aa_condition_js_default .= "}\n";
	$aa_condition_js_default .= "\n";
	$aa_condition_js_default .= "//set default\n";
	$aa_condition_js_default .= "condition = true;\n";
	$aa_condition_js_default .= "\n";
	$aa_condition_js_default .= "//Holiday?\n";
	$aa_condition_js_default .= "if (isholiday( Month, Date )) {\n";
	//$aa_condition_js_default .= "    console_log( \"info\", \"holiday\\n\" );\n";
	$aa_condition_js_default .= "    condition = false;\n";
	$aa_condition_js_default .= "}\n";
	$aa_condition_js_default .= "\n";
	$aa_condition_js_default .= "//Weekend?\n";
	$aa_condition_js_default .= "if (isweekend( Day )) {\n";
	//$aa_condition_js_default .= "    console_log( \"info\", \"weekend\\n\" );\n";
	$aa_condition_js_default .= "    condition = false;\n";
	$aa_condition_js_default .= "}\n";
	$aa_condition_js_default .= "\n";
	$aa_condition_js_default .= "// After Hours?\n";
	$aa_condition_js_default .= "if (isafterhours( Hours )) {\n";
	//$aa_condition_js_default .= "    console_log( \"info\", \"after hours\\n\" );\n";
	$aa_condition_js_default .= "    condition = false;\n";
	$aa_condition_js_default .= "}\n";
	$aa_condition_js_default .= "\n";

//Pre-populate the form
if (count($_GET)>0 && $_POST["persistformvar"] != "true") {
	$auto_attendant_id = $_GET["id"];
	$sql = "";
	$sql .= "select * from v_auto_attendant ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and auto_attendant_id = '$auto_attendant_id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		$v_id = $row["v_id"];
		$aa_extension = $row["aa_extension"];
		$aa_name = $row["aa_name"];
		$recording_id_action = $row["recording_id_action"];
		$recording_id_anti_action = $row["recording_id_anti_action"];
		$aa_timeout = $row["aa_timeout"];
		$aa_call_timeout = $row["aa_call_timeout"];
		$aa_context = $row["aa_context"];
		$aa_direct_dial = $row["aa_direct_dial"];
		$aa_ringback = $row["aa_ringback"];
		$aa_cid_name_prefix = $row["aa_cid_name_prefix"];
		$aa_condition_js = $row["aa_condition_js"];
		if (strlen($aa_condition_js) == 0) {
			$aa_condition_js = $aa_condition_js_default;
		}
		
		$aa_descr = $row["aa_descr"];
		break; //limit to 1 row
	}
	unset ($prepstatement);
}


	require_once "includes/header.php";


	echo "<script language=\"Javascript\">\n";
	echo "function sf() { document.forms[0].savetopath.focus(); }\n";
	echo "</script>\n";
	echo "<script language=\"Javascript\" type=\"text/javascript\" src=\"/edit_area/edit_area_full.js\"></script>\n";
	echo "<script language=\"Javascript\" type=\"text/javascript\">\n";
	echo "	// initialisation\n";
	echo "	editAreaLoader.init({\n";
	echo "		id: \"aa_condition_js\"	// id of the textarea to transform\n";
	echo "		,start_highlight: true\n";
	echo "		,allow_toggle: false\n";
	echo "		,language: \"en\"\n";
	echo "		,syntax: \"js\"\n";
	echo "		,toolbar: \"search, go_to_line,|, fullscreen, |, undo, redo, |, select_font, |, syntax_selection, |, change_smooth_selection, highlight, reset_highlight, |, help\"\n";
	echo "		,syntax_selection_allow: \"css,html,js,php,xml,c,cpp,sql\"\n";
	echo "		,show_line_colors: true\n";
	echo "	});\n";
	echo "</script>";

	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "      <br>";



	echo "<form method='post' name='frm' action=''>\n";

	echo "<div align='center'>\n";

	echo "<table width=\"100%\" border=\"0\" cellpadding=\"7\" cellspacing=\"0\">\n";
	echo "<tr>\n";
	if ($action == "add") {
		echo "<td align='left' width='30%' nowrap><b>Auto Attendant Add</b></td>\n";
	}
	if ($action == "update") {
		echo "<td align='left' width='30%' nowrap><b>Auto Attendant Edit</b></td>\n";
	}
	echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_auto_attendant.php'\" value='Back'></td>\n";
	echo "</tr>\n";


	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Extension:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='aa_extension' maxlength='255' value=\"$aa_extension\">\n";
	echo "<br />\n";
	echo "example: 5002\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Name:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='aa_name' maxlength='255' value=\"$aa_name\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "	Recording Action:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "		<select name='recording_id_action' class='formfld'>\n";
	echo "		<option></option>\n";

	$sql = "";
	$sql .= "select * from v_recordings ";
	$sql .= "where v_id = '$v_id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		if ($recording_id_action == $row['recording_id']) {
			echo "		<option value='".$row['recording_id']."' selected='yes'>".$row['recordingname']."</option>\n";
		}
		else {
			echo "		<option value='".$row['recording_id']."'>".$row['recordingname']."</option>\n";
		}
	}
	unset ($prepstatement);

	echo "		</select>\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Recording Anti-Action:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	//echo "    <input class='formfld' type='text' name='recording_id_anti_action' maxlength='255' value=\"$recording_id_anti_action\">\n";
	echo "              <select name='recording_id_anti_action' class='formfld'>\n";
	echo "                <option></option>\n";
	$sql = "";
	$sql .= "select * from v_recordings ";
	$sql .= "where v_id = '$v_id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		//$v_id = $row["v_id"];
		//$filename = $row["filename"];
		//$recordingname = $row["recordingname"];
		//$recording_id = $row["recording_id"];
		//$descr = $row["descr"];
		if ($recording_id_anti_action == $row['recording_id']) {
			echo "              <option value='".$row['recording_id']."' selected='yes'>".$row['recordingname']."</option>\n";
		}
		else {
			echo "              <option value='".$row['recording_id']."'>".$row['recordingname']."</option>\n";
		}
	}
	unset ($prepstatement);
	
	echo "              </select>\n";

	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Timeout:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='aa_timeout' maxlength='255' value=\"$aa_timeout\">\n";
	echo "<br />\n";
	echo "After the recording concludes the timeout sets the time in seconds to continue to wait for DTMF. If the DTMF is \n";
	echo "not detected during that time the 't' timeout option is executed. \n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Call Timeout:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='aa_call_timeout' maxlength='255' value=\"$aa_call_timeout\">\n";
	echo "<br />\n";
	echo "Call timeout is the time in seconds to ring the destination. After this time is exceeded calls to extensions \n";
	echo "will be sent to voicemail. default: 30 seconds \n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Context:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	if (strlen(aa_context) == 0) {
		echo "    <input class='formfld' type='text' name='aa_context' maxlength='255' value=\"default\">\n";
	}
	else {
		echo "    <input class='formfld' type='text' name='aa_context' maxlength='255' value=\"$aa_context\">\n";
	}
	echo "<br />\n";
	echo "example: default\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Direct Dial:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	//echo "    <input class='formfld' type='text' name='aa_direct_dial' maxlength='255' value=\"$aa_direct_dial\">\n";
	echo "                <select name='aa_direct_dial' class='formfld'>\n";
	echo "                <option></option>\n";
	if (strlen($aa_direct_dial) == 0) { //set default
		echo "                <option value='true'>enable</option>\n";
		echo "                <option selected='yes' value='false'>disabled</option>\n";
	}
	else {
		if ($aa_direct_dial == "true") {
			echo "                <option selected='yes' value='true'>enabled</option>\n";
		}
		else {
			echo "                <option value='true'>enable</option>\n";
		}
		if ($aa_direct_dial == "false") {
			echo "                <option selected='yes' value='false'>disabled</option>\n";
		}
		else {
			echo "                <option value='false'>disable</option>\n";
		}
	}
	echo "                </select>\n";
	echo "<br />\n";
	echo "Allows callers to dial directly to extensions and feature codes that are up to 5 digits in length.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Ring Back:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "                <select name='aa_ringback' class='formfld'>\n";
	echo "                <option></option>\n";
	if ($aa_ringback == "ring") {
		echo "                <option selected='yes'>ring</option>\n";
	}
	else {
		echo "                <option>ring</option>\n";
	}
	if ($aa_ringback == "music") {
		echo "                <option selected='yes'>music</option>\n";
	}
	else {
		echo "                <option>music</option>\n";
	}
	echo "                </select>\n";

	echo "<br />\n";
	echo "Defines what the caller will hear while destination is being called. The choices are music (music on hold) ring (ring tone.) default: music\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    CID Prefix:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='aa_cid_name_prefix' maxlength='255' value=\"$aa_cid_name_prefix\">\n";
	echo "<br />\n";
	echo "Set a prefix on the caller ID name. (optional)\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Javascript Condition:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	if (strlen($aa_condition_js) == 0) {
		echo "<textarea name=\"aa_condition_js\" id=\"aa_condition_js\" cols=\"60\" rows=\"10\" wrap=\"off\">".$aa_condition_js_default."</textarea>\n";
	}
	else {
		echo "<textarea name=\"aa_condition_js\" id=\"aa_condition_js\" cols=\"60\" rows=\"10\" wrap=\"off\">".$aa_condition_js."</textarea>\n";
	}
	echo "<br />\n";
	echo "A simple valid condition is: condition=true; To re-populate the default simply empty the textarea and click on save. The following javascript variables have been defined: Hours, Mins, Seconds, Month, Date, Year, and Day.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Description:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='aa_descr' maxlength='255' value=\"$aa_descr\">\n";
	echo "<br />\n";
	echo "You may enter a description here for your reference (not parsed). \n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='auto_attendant_id' value='$auto_attendant_id'>\n";
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

//---- begin: v_auto_attendant ---------------------------
if ($action == "update") {

	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"center\">\n";

	echo "      <br />";
	
	echo "    <table width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">\n";
	echo "      <tr>\n";
	echo "        <td align='left'><p><span class=\"vexpl\"><span class=\"red\"><strong><br>\n";
	echo "            </strong></span>\n";
	echo "            Options are the choices that are available to the caller when they\n";
	echo "            are calling the auto attendant. If the caller presses 2 then the call\n";
	echo "            is directed to the corresponding destination.\n";
	echo "            </span></p></td>\n";
	echo "      </tr>\n";
	echo "    </table>";

	echo "      <br />";


	echo "<table width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">\n";
	echo "  <tr>\n";
	echo "    <td align='left'><p><span class=\"vexpl\"><span class=\"red\"><strong>Action<br />\n";
	echo "        </strong></span>\n";
	echo "        The options that are executed when the <b>condition matches.</b>\n";
	echo "        </span></p></td>\n";
	echo "  </tr>\n";
	echo "</table>\n";
	echo "<br />";

	$sql = "";
	$sql .= " select * from v_auto_attendant_options ";
	$sql .= " where v_id = '$v_id' ";
	$sql .= " and auto_attendant_id = '$auto_attendant_id' ";
	$sql .= " and option_action = 'action' ";
	$sql .= " order by option_number asc";
	//if (strlen($orderby)> 0) { $sql .= "order by $orderby $order "; }
	//$sql .= " limit $rowsperpage offset $offset ";
	//echo $sql;
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$resultcount = count($result);
	unset ($prepstatement, $sql);


	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";

	echo "<div align='center'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	//echo "<tr><td colspan='5'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>";

	echo "<tr>\n";
	echo "<th align='left'>&nbsp;&nbsp;Option</th>\n";
	echo "<th align='left'>&nbsp;&nbsp;Type</th>\n";
	echo "<th align='left'>&nbsp;&nbsp;Profile</th>\n";
	echo "<th align='left'>&nbsp;&nbsp;Destination</th>\n";
	echo "<th align='left'>&nbsp;&nbsp;Description</th>\n";
	echo "<td align='right' width='42'>\n";
	echo "	<a href='v_auto_attendant_options_edit.php?id2=".$auto_attendant_id."&option_action=action' alt='add'>$v_link_label_add</a>\n";
	echo "</td>\n";
	echo "<tr>\n";

	//echo "<tr><td colspan='5'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\n";

	if ($resultcount == 0) { //no results
	}
	else { //received results
		foreach($result as $row) {
			//print_r( $row );
			echo "<tr >\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[option_number]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[option_type]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[option_profile]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[option_data]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[option_descr]."</td>\n";
			echo "   <td valign='top' align='right'>\n";
			echo "		<a href='v_auto_attendant_options_edit.php?id=".$row[auto_attendant_option_id]."&id2=".$auto_attendant_id."' alt='edit'>$v_link_label_edit</a>\n";
			echo "		<a href='v_auto_attendant_options_delete.php?id=".$row[auto_attendant_option_id]."&id2=".$auto_attendant_id."' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\">$v_link_label_delete</a>\n";
			echo "   </td>\n";
			echo "</tr>\n";
			//echo "<tr><td colspan='5'><img src='/images/spacer.gif' width='100%'' height='1' style='background-color: #BBBBBB;'></td></tr>\n";
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $rowcount);
	} //end if results

	echo "<tr>\n";
	echo "<td colspan='6'>\n";
	echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap>$pagingcontrols</td>\n";
	echo "		<td width='33.3%' align='right'>\n";
	echo "			<a href='v_auto_attendant_options_edit.php?id2=".$auto_attendant_id."&option_action=action' alt='add'>$v_link_label_add</a>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	</table>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>";
	echo "</div>";

	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";


	//--------------------------------------------------------------------------

	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"center\">\n";
	echo "      <br />";


	echo "  	<table width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">\n";
	echo "      <tr>\n";
	echo "        <td align='left'><p><span class=\"vexpl\"><span class=\"red\"><strong>Anti-Action<br />\n";
	echo "            </strong></span>\n";
	echo "              The options that are executed when the <b>condition does NOT match.</b>\n";
	echo "            </span></p></td>\n";
	echo "      </tr>\n";
	echo "    </table>";

	echo "      <br />";


	$sql = "";
	$sql .= " select * from v_auto_attendant_options ";
	$sql .= " where auto_attendant_id = '$auto_attendant_id' ";
	$sql .= " and v_id = $v_id ";
	$sql .= " and option_action = 'anti-action' ";
	$sql .= " order by option_number asc";
	//if (strlen($orderby)> 0) { $sql .= "order by $orderby $order "; }

	//$sql .= " limit $rowsperpage offset $offset ";
	//echo $sql;
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$resultcount = count($result);
	unset ($prepstatement, $sql);


	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";

	echo "<div align='center'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	//echo "<tr><td colspan='5'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>";

	echo "<tr>\n";
	echo "<th align='left'>&nbsp;&nbsp;Option</th>\n";
	echo "<th align='left'>&nbsp;&nbsp;Type</th>\n";
	echo "<th align='left'>&nbsp;&nbsp;Profile</th>\n";
	echo "<th align='left'>&nbsp;&nbsp;Destination</th>\n";
	echo "<th align='left'>&nbsp;&nbsp;Description</th>\n";
	echo "<td align='right' width='42'>\n";
	echo "	<a href='v_auto_attendant_options_edit.php?id2=".$auto_attendant_id."&option_action=action' alt='add'>$v_link_label_add</a>\n";
	echo "</td>\n";
	echo "<tr>\n";
	//echo "<tr><td colspan='5'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\n";

	if ($resultcount == 0) { //no results
	}
	else { //received results
		foreach($result as $row) {
			//print_r( $row );
			echo "<tr >\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[option_number]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[option_type]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[option_profile]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[option_data]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[option_descr]."</td>\n";
			echo "   <td valign='top' align='right'>\n";
			echo "		<a href='v_auto_attendant_options_edit.php?id=".$row[auto_attendant_option_id]."&id2=".$auto_attendant_id."' alt='edit'>$v_link_label_edit</a>\n";
			echo "		<a href='v_auto_attendant_options_delete.php?id=".$row[auto_attendant_option_id]."&id2=".$auto_attendant_id."' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\">$v_link_label_delete</a>\n";
			echo "   </td>\n";
			echo "</tr>\n";
			//echo "<tr><td colspan='5'><img src='/images/spacer.gif' width='100%'' height='1' style='background-color: #BBBBBB;'></td></tr>\n";
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $rowcount);
	} //end if results

	echo "<tr>\n";
	echo "<td colspan='6'>\n";
	echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap>$pagingcontrols</td>\n";
	echo "		<td width='33.3%' align='right'>\n";
	echo "			<a href='v_auto_attendant_options_edit.php?id2=".$auto_attendant_id."&option_action=anti-action' alt='add'>$v_link_label_add</a>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	</table>\n";
	echo "</td>\n";
	echo "</tr>\n";


	echo "</table>";
	echo "</div>";
	echo "<br><br>";
	echo "<br><br>";

	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	echo "<br><br>";

} //end if update
//---- end: v_auto_attendant ---------------------------
require_once "includes/footer.php";
?>
