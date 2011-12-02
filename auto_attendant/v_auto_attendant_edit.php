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
	$aaextension = check_str($_POST["aaextension"]);
	$aaname = check_str($_POST["aaname"]);
	$recordingidaction = check_str($_POST["recordingidaction"]);
	$recordingidantiaction = check_str($_POST["recordingidantiaction"]);
	$aatimeout = check_str($_POST["aatimeout"]);
	$aacalltimeout = check_str($_POST["aacalltimeout"]);
	$aacontext = check_str($_POST["aacontext"]);
	$aadirectdial = check_str($_POST["aadirectdial"]);
	$aaringback = check_str($_POST["aaringback"]);
	$aacidnameprefix = check_str($_POST["aacidnameprefix"]);
	$aaconditionjs = check_str($_POST["aaconditionjs"]);
	$aadescr = check_str($_POST["aadescr"]);
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
		if (strlen($aaextension) == 0) { $msg .= "Please provide: Extension<br>\n"; }
		if (strlen($aaname) == 0) { $msg .= "Please provide: Name<br>\n"; }
		if (strlen($recordingidaction) == 0) { $msg .= "Please provide: Recording Action<br>\n"; }
		if (strlen($recordingidantiaction) == 0) { $msg .= "Please provide: Recording Anti-Action<br>\n"; }
		if (strlen($aatimeout) == 0) { $msg .= "Please provide: Timeout<br>\n"; }
		if (strlen($aacalltimeout) == 0) { $msg .= "Please provide: Call Timeout<br>\n"; }
		if (strlen($aacontext) == 0) { $msg .= "Please provide: Context<br>\n"; }
		if (strlen($aadirectdial) == 0) { $msg .= "Please provide: Direct Dial<br>\n"; }
		if (strlen($aaringback) == 0) { $msg .= "Please provide: Ring Back<br>\n"; }
		//if (strlen($aacidnameprefix) == 0) { $msg .= "Please provide: CID Prefix<br>\n"; }
		//if (strlen($aaconditionjs) == 0) { $msg .= "Please provide: Javascript Condition<br>\n"; }
		//if (strlen($aadescr) == 0) { $msg .= "Please provide: Description<br>\n"; }
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
	$tmp .= "Extension: $aaextension\n";
	$tmp .= "Name: $aaname\n";
	$tmp .= "Recording Action: $recordingidaction\n";
	$tmp .= "Recording Anti-Action: $recordingidantiaction\n";
	$tmp .= "Timeout: $aatimeout\n";
	$tmp .= "Call Timeout: $aacalltimeout\n";
	$tmp .= "Context: $aacontext\n";
	$tmp .= "Direct Dial: $aadirectdial\n";
	$tmp .= "Ring Back: $aaringback\n";
	$tmp .= "CID Prefix: $aacidnameprefix\n";
	$tmp .= "Javascript Condition: $aaconditionjs\n";
	$tmp .= "Description: $aadescr\n";

	//Add or update the database
	if ($_POST["persistformvar"] != "true") {
		if ($action == "add") {
			$sql = "insert into v_auto_attendant ";
			$sql .= "(";
			$sql .= "v_id, ";
			$sql .= "aaextension, ";
			$sql .= "aaname, ";
			$sql .= "recordingidaction, ";
			$sql .= "recordingidantiaction, ";
			$sql .= "aatimeout, ";
			$sql .= "aacalltimeout, ";
			$sql .= "aacontext, ";
			$sql .= "aadirectdial, ";
			$sql .= "aaringback, ";
			$sql .= "aacidnameprefix, ";
			$sql .= "aaconditionjs, ";
			$sql .= "aadescr ";
			$sql .= ")";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$v_id', ";
			$sql .= "'$aaextension', ";
			$sql .= "'$aaname', ";
			$sql .= "'$recordingidaction', ";
			$sql .= "'$recordingidantiaction', ";
			$sql .= "'$aatimeout', ";
			$sql .= "'$aacalltimeout', ";
			$sql .= "'$aacontext', ";
			$sql .= "'$aadirectdial', ";
			$sql .= "'$aaringback', ";
			$sql .= "'$aacidnameprefix', ";
			$sql .= "'$aaconditionjs', ";
			$sql .= "'$aadescr' ";
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
			$sql .= "aaextension = '$aaextension', ";
			$sql .= "aaname = '$aaname', ";
			$sql .= "recordingidaction = '$recordingidaction', ";
			$sql .= "recordingidantiaction = '$recordingidantiaction', ";
			$sql .= "aatimeout = '$aatimeout', ";
			$sql .= "aacalltimeout = '$aacalltimeout', ";
			$sql .= "aacontext = '$aacontext', ";
			$sql .= "aadirectdial = '$aadirectdial', ";
			$sql .= "aaringback = '$aaringback', ";
			$sql .= "aacidnameprefix = '$aacidnameprefix', ";
			$sql .= "aaconditionjs = '$aaconditionjs', ";
			$sql .= "aadescr = '$aadescr' ";
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

//set default $aaconditionjs
	$aaconditionjs_default = "function isholiday( Month, Date ) {\n";
	$aaconditionjs_default .= "    var Holiday = 0; //default false\n";
	$aaconditionjs_default .= "    if (Month == \"12\" && Date == \"25\") {\n";
	$aaconditionjs_default .= "      Holiday = 1; //true\n";
	$aaconditionjs_default .= "    }\n";
	$aaconditionjs_default .= "    if (Month == \"7\" && Date == \"4\") {\n";
	$aaconditionjs_default .= "      Holiday = 1; //true\n";
	$aaconditionjs_default .= "    }\n";
	$aaconditionjs_default .= "    if (Month == \"1\" && Date == \"1\") {\n";
	$aaconditionjs_default .= "      Holiday = 1; //true\n";
	$aaconditionjs_default .= "    }\n";
	$aaconditionjs_default .= "    if (Holiday == 1) {\n";
	$aaconditionjs_default .= "      return true;\n";
	$aaconditionjs_default .= "    }\n";
	$aaconditionjs_default .= "    else {\n";
	$aaconditionjs_default .= "      return false;\n";
	$aaconditionjs_default .= "    }\n";
	$aaconditionjs_default .= "}\n";
	$aaconditionjs_default .= "\n";
	$aaconditionjs_default .= "function isweekday( Day ) {\n";
	$aaconditionjs_default .= "    if (Day > 1 && Day < 7) {\n";
	$aaconditionjs_default .= "        return true;\n";
	$aaconditionjs_default .= "    }\n";
	$aaconditionjs_default .= "    else {\n";
	$aaconditionjs_default .= "        return false;\n";
	$aaconditionjs_default .= "    }\n";
	$aaconditionjs_default .= "}\n";
	$aaconditionjs_default .= "\n";
	$aaconditionjs_default .= "function isweekend( Day ) {\n";
	$aaconditionjs_default .= "    if (Day > 1 && Day < 7) {\n";
	$aaconditionjs_default .= "        return false;\n";
	$aaconditionjs_default .= "    }\n";
	$aaconditionjs_default .= "    else {\n";
	$aaconditionjs_default .= "        return true;\n";
	$aaconditionjs_default .= "    }\n";
	$aaconditionjs_default .= "}\n";
	$aaconditionjs_default .= "\n";
	$aaconditionjs_default .= "function isofficehours( Hours ) {\n";
	$aaconditionjs_default .= "    if (Hours >= 9 && Hours < 17) {\n";
	$aaconditionjs_default .= "        return true;\n";
	$aaconditionjs_default .= "    }\n";
	$aaconditionjs_default .= "    else {\n";
	$aaconditionjs_default .= "        return false;\n";
	$aaconditionjs_default .= "    }\n";
	$aaconditionjs_default .= "}\n";
	$aaconditionjs_default .= "\n";
	$aaconditionjs_default .= "function isafterhours( Hours ) {\n";
	$aaconditionjs_default .= "    if (Hours >= 9 && Hours < 17) {\n";
	$aaconditionjs_default .= "        return false;\n";
	$aaconditionjs_default .= "    }\n";
	$aaconditionjs_default .= "    else {\n";
	$aaconditionjs_default .= "        return true;\n";
	$aaconditionjs_default .= "    }\n";
	$aaconditionjs_default .= "}\n";
	$aaconditionjs_default .= "\n";
	$aaconditionjs_default .= "//set default\n";
	$aaconditionjs_default .= "condition = true;\n";
	$aaconditionjs_default .= "\n";
	$aaconditionjs_default .= "//Holiday?\n";
	$aaconditionjs_default .= "if (isholiday( Month, Date )) {\n";
	//$aaconditionjs_default .= "    console_log( \"info\", \"holiday\\n\" );\n";
	$aaconditionjs_default .= "    condition = false;\n";
	$aaconditionjs_default .= "}\n";
	$aaconditionjs_default .= "\n";
	$aaconditionjs_default .= "//Weekend?\n";
	$aaconditionjs_default .= "if (isweekend( Day )) {\n";
	//$aaconditionjs_default .= "    console_log( \"info\", \"weekend\\n\" );\n";
	$aaconditionjs_default .= "    condition = false;\n";
	$aaconditionjs_default .= "}\n";
	$aaconditionjs_default .= "\n";
	$aaconditionjs_default .= "// After Hours?\n";
	$aaconditionjs_default .= "if (isafterhours( Hours )) {\n";
	//$aaconditionjs_default .= "    console_log( \"info\", \"after hours\\n\" );\n";
	$aaconditionjs_default .= "    condition = false;\n";
	$aaconditionjs_default .= "}\n";
	$aaconditionjs_default .= "\n";

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
		$aaextension = $row["aaextension"];
		$aaname = $row["aaname"];
		$recordingidaction = $row["recordingidaction"];
		$recordingidantiaction = $row["recordingidantiaction"];
		$aatimeout = $row["aatimeout"];
		$aacalltimeout = $row["aacalltimeout"];
		$aacontext = $row["aacontext"];
		$aadirectdial = $row["aadirectdial"];
		$aaringback = $row["aaringback"];
		$aacidnameprefix = $row["aacidnameprefix"];
		$aaconditionjs = $row["aaconditionjs"];
		if (strlen($aaconditionjs) == 0) {
			$aaconditionjs = $aaconditionjs_default;
		}
		
		$aadescr = $row["aadescr"];
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
	echo "		id: \"aaconditionjs\"	// id of the textarea to transform\n";
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
	echo "    <input class='formfld' type='text' name='aaextension' maxlength='255' value=\"$aaextension\">\n";
	echo "<br />\n";
	echo "example: 5002\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Name:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='aaname' maxlength='255' value=\"$aaname\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "	Recording Action:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "		<select name='recordingidaction' class='formfld'>\n";
	echo "		<option></option>\n";

	$sql = "";
	$sql .= "select * from v_recordings ";
	$sql .= "where v_id = '$v_id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		if ($recordingidaction == $row['recording_id']) {
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
	//echo "    <input class='formfld' type='text' name='recordingidantiaction' maxlength='255' value=\"$recordingidantiaction\">\n";
	echo "              <select name='recordingidantiaction' class='formfld'>\n";
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
		if ($recordingidantiaction == $row['recording_id']) {
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
	echo "    <input class='formfld' type='text' name='aatimeout' maxlength='255' value=\"$aatimeout\">\n";
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
	echo "    <input class='formfld' type='text' name='aacalltimeout' maxlength='255' value=\"$aacalltimeout\">\n";
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
	if (strlen(aacontext) == 0) {
		echo "    <input class='formfld' type='text' name='aacontext' maxlength='255' value=\"default\">\n";
	}
	else {
		echo "    <input class='formfld' type='text' name='aacontext' maxlength='255' value=\"$aacontext\">\n";
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
	//echo "    <input class='formfld' type='text' name='aadirectdial' maxlength='255' value=\"$aadirectdial\">\n";
	echo "                <select name='aadirectdial' class='formfld'>\n";
	echo "                <option></option>\n";
	if (strlen($aadirectdial) == 0) { //set default
		echo "                <option value='true'>enable</option>\n";
		echo "                <option selected='yes' value='false'>disabled</option>\n";
	}
	else {
		if ($aadirectdial == "true") {
			echo "                <option selected='yes' value='true'>enabled</option>\n";
		}
		else {
			echo "                <option value='true'>enable</option>\n";
		}
		if ($aadirectdial == "false") {
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
	echo "                <select name='aaringback' class='formfld'>\n";
	echo "                <option></option>\n";
	if ($aaringback == "ring") {
		echo "                <option selected='yes'>ring</option>\n";
	}
	else {
		echo "                <option>ring</option>\n";
	}
	if ($aaringback == "music") {
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
	echo "    <input class='formfld' type='text' name='aacidnameprefix' maxlength='255' value=\"$aacidnameprefix\">\n";
	echo "<br />\n";
	echo "Set a prefix on the caller ID name. (optional)\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Javascript Condition:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	if (strlen($aaconditionjs) == 0) {
		echo "<textarea name=\"aaconditionjs\" id=\"aaconditionjs\" cols=\"60\" rows=\"10\" wrap=\"off\">".$aaconditionjs_default."</textarea>\n";
	}
	else {
		echo "<textarea name=\"aaconditionjs\" id=\"aaconditionjs\" cols=\"60\" rows=\"10\" wrap=\"off\">".$aaconditionjs."</textarea>\n";
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
	echo "    <input class='formfld' type='text' name='aadescr' maxlength='255' value=\"$aadescr\">\n";
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
	$sql .= " and optionaction = 'action' ";
	$sql .= " order by optionnumber asc";
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
	echo "	<a href='v_auto_attendant_options_edit.php?id2=".$auto_attendant_id."&optionaction=action' alt='add'>$v_link_label_add</a>\n";
	echo "</td>\n";
	echo "<tr>\n";

	//echo "<tr><td colspan='5'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\n";

	if ($resultcount == 0) { //no results
	}
	else { //received results
		foreach($result as $row) {
			//print_r( $row );
			echo "<tr >\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[optionnumber]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[optiontype]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[optionprofile]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[optiondata]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[optiondescr]."</td>\n";
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
	echo "			<a href='v_auto_attendant_options_edit.php?id2=".$auto_attendant_id."&optionaction=action' alt='add'>$v_link_label_add</a>\n";
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
	$sql .= " and optionaction = 'anti-action' ";
	$sql .= " order by optionnumber asc";
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
	echo "	<a href='v_auto_attendant_options_edit.php?id2=".$auto_attendant_id."&optionaction=action' alt='add'>$v_link_label_add</a>\n";
	echo "</td>\n";
	echo "<tr>\n";
	//echo "<tr><td colspan='5'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\n";

	if ($resultcount == 0) { //no results
	}
	else { //received results
		foreach($result as $row) {
			//print_r( $row );
			echo "<tr >\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[optionnumber]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[optiontype]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[optionprofile]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[optiondata]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[optiondescr]."</td>\n";
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
	echo "			<a href='v_auto_attendant_options_edit.php?id2=".$auto_attendant_id."&optionaction=anti-action' alt='add'>$v_link_label_add</a>\n";
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
