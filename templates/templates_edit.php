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
if (!ifgroup("superadmin")) {
	echo "access denied";
	exit;
}

//action add or update
if (isset($_REQUEST["id"])) {
	$action = "update";
	$template_id = check_str($_REQUEST["id"]);
}
else {
	$action = "add";
}

//POST to PHP variables
if (count($_POST)>0) {
	$templatename = check_str($_POST["templatename"]);
	$templatedesc = check_str($_POST["templatedesc"]);
	$template = $_POST["template"];
	$templatemenutype = check_str($_POST["templatemenutype"]);
	$templatemenucss = $_POST["templatemenucss"];
}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';

	////recommend moving this to the config.php file
	$uploadtempdir = $_ENV["TEMP"]."\\";
	ini_set('upload_tmp_dir', $uploadtempdir);
	////$imagedir = $_ENV["TEMP"]."\\";
	////$filedir = $_ENV["TEMP"]."\\";

	if ($action == "update") {
		$template_id = check_str($_POST["template_id"]);
	}

	//check for all required data
		//if (strlen($templatename) == 0) { $msg .= "Please provide: Name<br>\n"; }
		//if (strlen($templatedesc) == 0) { $msg .= "Please provide: Description<br>\n"; }
		//if (strlen($template) == 0) { $msg .= "Please provide: template<br>\n"; }
		//if (strlen($templatemenutype) == 0) { $msg .= "Please provide: Menu Type<br>\n"; }
		//if (strlen($templatemenucss) == 0) { $msg .= "Please provide: Menu CSS<br>\n"; }
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

	//$tmp = "\n";
	//$tmp .= "Name: $templatename\n";
	//$tmp .= "Description: $templatedesc\n";
	//$tmp .= "template: $template\n";
	//$tmp .= "Menu Type: $templatemenutype\n";
	//$tmp .= "Menu CSS: $templatemenucss\n";


//Add or update the database
if ($_POST["persistformvar"] != "true") {
	if ($action == "add") {
		$sql = "insert into v_templates ";
		$sql .= "(";
		$sql .= "v_id, ";
		$sql .= "templatename, ";
		$sql .= "templatedesc, ";
		$sql .= "template, ";
		$sql .= "templatemenutype, ";
		$sql .= "templatemenucss ";
		$sql .= ")";
		$sql .= "values ";
		$sql .= "(";
		$sql .= "'$v_id', ";
		$sql .= "'$templatename', ";
		$sql .= "'$templatedesc', ";
		$sql .= "'".base64_encode($template)."', ";
		$sql .= "'$templatemenutype', ";
		$sql .= "'".base64_encode($templatemenucss)."' ";
		$sql .= ")";
		$db->exec(check_sql($sql));
		unset($sql);

		require_once "includes/header.php";
		echo "<meta http-equiv=\"refresh\" content=\"2;url=templates.php\">\n";
		echo "<div align='center'>\n";
		echo "Add Complete\n";
		echo "</div>\n";
		require_once "includes/footer.php";
		return;
	} //if ($action == "add")

	if ($action == "update") {
		$sql = "update v_templates set ";
		$sql .= "templatename = '$templatename', ";
		$sql .= "templatedesc = '$templatedesc', ";
		$sql .= "template = '".base64_encode($template)."', ";
		$sql .= "templatemenutype = '$templatemenutype', ";
		$sql .= "templatemenucss = '".base64_encode($templatemenucss)."' ";
		$sql .= "where v_id = '$v_id'";
		$sql .= "and templateid = '$template_id'";
		$db->exec(check_sql($sql));
		unset($sql);

		require_once "includes/header.php";
		echo "<meta http-equiv=\"refresh\" content=\"2;url=templates.php\">\n";
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
	$template_id = $_GET["id"];
	$sql = "";
	$sql .= "select * from v_templates ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and templateid = '$template_id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		$templatename = $row["templatename"];
		$templatedesc = $row["templatedesc"];
		$template = base64_decode($row["template"]);
		$templatemenutype = $row["templatemenutype"];
		$templatemenucss = base64_decode($row["templatemenucss"]);
		break; //limit to 1 row
	}
	unset ($prepstatement);
}


	require_once "includes/header.php";


	//--- Begin: Edit Area -----------------------------------------------------
		echo "    <script language=\"javascript\" type=\"text/javascript\" src=\"".PROJECT_PATH."/includes/edit_area/edit_area_full.js\"></script>\n";
		echo "    <!-- -->\n";

		echo "	<script language=\"Javascript\" type=\"text/javascript\">\n";
		echo "		editAreaLoader.init({\n";
		echo "			id: \"template\" // id of the textarea to transform //, |, help\n";
		echo "			,start_highlight: true\n";
		echo "			,font_size: \"8\"\n";
		echo "			,allow_toggle: false\n";
		echo "			,language: \"en\"\n";
		echo "			,syntax: \"html\"\n";
		echo "			,toolbar: \"search, go_to_line,|, fullscreen, |, undo, redo, |, select_font, |, syntax_selection, |, change_smooth_selection, highlight, reset_highlight, |, help\" //new_document,\n";
		echo "			,plugins: \"charmap\"\n";
		echo "			,charmap_default: \"arrows\"\n";
		echo "\n";
		echo "    });\n";
		echo "\n";
		echo "\n";
		echo "		editAreaLoader.init({\n";
		echo "			id: \"templatemenucss\"	// id of the textarea to transform //, |, help\n";
		echo "			,start_highlight: true\n";
		echo "			,font_size: \"8\"\n";
		echo "			,allow_toggle: false\n";
		echo "			,language: \"en\"\n";
		echo "			,syntax: \"css\"\n";
		echo "			,toolbar: \"search, go_to_line,|, fullscreen, |, undo, redo, |, select_font, |, syntax_selection, |, change_smooth_selection, highlight, reset_highlight, |, help\" //new_document,\n";
		echo "			,plugins: \"charmap\"\n";
		echo "			,charmap_default: \"arrows\"\n";
		echo "\n";
		echo "    });\n";
		echo "    </script>";
	//--- End: Edit Area -------------------------------------------------------

	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "	  <br>";



	echo "<form method='post' name='frm' action=''>\n";

	echo "<div align='center'>\n";
	echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";

	echo "<tr>\n";
	if ($action == "add") {
		echo "<td width='30%' align='left' nowrap><b>Template Add</b></td>\n";
	}
	if ($action == "update") {
		echo "<td width='30%' align='left' nowrap><b>Template Edit</b></td>\n";
	}
	echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='templates.php'\" value='Back'></td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Name:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='templatename' maxlength='255' value=\"$templatename\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Description:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<textarea class='formfld' name='templatedesc' rows='4'>$templatedesc</textarea>\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Template:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<textarea name='template' id='template' rows='17' class='txt' wrap='off' >$template</textarea>\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	//echo "<tr>\n";
	//echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	//echo "	Menu Type:\n";
	//echo "</td>\n";
	//echo "<td class='vtable' align='left'>\n";
	//echo "	<input class='formfld' type='text' name='templatemenutype' maxlength='255' value=\"$templatemenutype\">\n";
	//echo "            <select name=\"templatemenutype\" class='txt'>\n";
	//echo "            <option value=\"\"></option>\n";
	//if ($templatemenutype == "none") {
	//	echo "            <option value=\"none\" selected>none</option>\n";
	//}
	//else {
	//	echo "            <option value=\"none\">none</option>\n";
	//}

	//if ($templatemenutype == "horizontal") {
	//	echo "            <option value=\"horizontal\" selected>horizontal</option>\n";
	//}
	//else {
	//	echo "            <option value=\"horizontal\" selected>horizontal</option>\n";
	//}

	//if ($templatemenutype == "list") {
	//	echo "            <option value=\"list\" >list</option>\n";
	//}
	//else {
	//	echo "            <option value=\"list\">list</option>\n";
	//}
	//echo "            </select>";
	//echo "<br />\n";
	//echo "\n";
	//echo "</td>\n";
	//echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Menu CSS:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<textarea name='templatemenucss' id='templatemenucss' rows='17' class='txt' wrap='off'>$templatemenucss</textarea>\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='template_id' value='$template_id'>\n";
	}
	echo "				<input type='submit' name='submit' class='btn' value='Save'>\n";
	echo "		</td>\n";
	echo "	</tr>";

	echo "	<tr>\n";
	echo "	<td colspan='2' align='center'>\n";
		echo "<table width='75%'><tr><td align='left'>\n";
		echo "Each template should include the following tags. <br />\n";
		echo "<br />";    

		echo htmlentities("<!--{title}-->")."<br />Should be placed in between \n";
		echo "the html &lt;title&gt;&lt;/title&gt; tags. Used to set a title on each page. \n";
		echo "The title is defined in from the content manager title. <br />\n";
		echo "<br />";		

		echo htmlentities("<!--{head}-->")."<br />Should be placed in between \n";
		echo "the html &lt;head&gt;&lt;/head&gt; tags. Used to place additional code when \n";
		echo "necessary in the html head tags. For example rss feeds in some browsers are \n";
		echo "required to be inside the html head tags.<br />\n";
		echo "<br />";

		echo htmlentities("<!--{body}-->")." &nbsp; &nbsp; <br />\n";
		echo "Inidicates where the content should be placed inside the template. <br />\n";
		echo "<br />";

		echo htmlentities("<!--{menu}-->")." &nbsp; &nbsp; <br />\n";
		echo "Indicates where the menu should go inside the template.<br />";
		echo "<br />";

		echo "</td></tr></table>\n";
	echo "		</td>";
	echo "	</tr>";

	echo "</table>";
	echo "</form>";


	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";


require_once "includes/footer.php";
?>
