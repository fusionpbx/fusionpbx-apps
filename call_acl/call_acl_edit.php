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
	Luis Daniel Lucio Quiroz <dlucio@okay.com.mx>
	Igor Olhovskiy <igorolhovskiy@gmail.com>

	Call ACL is written on Call Block base by Gerrit Visser <gerrit308@gmail.com>
*/
//includes
	require_once "root.php";
	require_once "resources/require.php";
	require_once "resources/check_auth.php";

//check permissions
	if (permission_exists('call_acl_edit') || permission_exists('call_acl_add')) {
		//access granted
	} else {
		echo "access denied";
		exit;
	}

//add multi-lingual support
	$language = new text;
	$text = $language->get();

//action add or update
	if (isset($_REQUEST["id"])) {
		$action = "update";
		$call_acl_uuid = check_str($_REQUEST["id"]);
	} else {
		$action = "add";
	}

//get http post variables and set them to php variables
	if (count($_POST) > 0) {
		$call_acl_order = check_str($_POST["call_acl_order"]);
		$call_acl_name = check_str($_POST["call_acl_name"]);
		$call_acl_source = check_str($_POST["call_acl_source"]);
		$call_acl_destination = check_str($_POST["call_acl_destination"]);
		$call_acl_action = check_str($_POST["call_acl_action"]);
		$call_acl_enabled = check_str($_POST["call_acl_enabled"]);
	}

//handle the http post
	if (count($_POST) > 0 && strlen($_POST["persistformvar"]) == 0) {
	
		$msg = '';
	
		//check for all required data
		if (strlen($call_acl_name) == 0) { 
			$msg .= $text['label-call_acl_name']."<br>\n"; 
		}
		if (strlen($call_acl_source) == 0) {
			$msg .= $text['label-call_acl_source']."<br>\n"; 
		}
		if (strlen($call_acl_destination) == 0) { 
			$msg .= $text['label-call_acl_destination']."<br>\n"; 
		}
		if (strlen($call_acl_order) == 0) { 
			$msg .= $text['label-call_acl_order']."<br>\n"; 
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

				$call_acl_uuid = uuid();

				$sql = "INSERT INTO v_call_acl";
				$sql .= " (";
				$sql .= "domain_uuid, ";
				$sql .= "call_acl_uuid, ";
				$sql .= "call_acl_order, ";
				$sql .= "call_acl_name, ";
				$sql .= "call_acl_source, ";
				$sql .= "call_acl_destination, ";
				$sql .= "call_acl_action, ";
				$sql .= "call_acl_enabled ";
				$sql .= ") ";
				$sql .= "VALUES ";
				$sql .= "(";
				$sql .= "'".$_SESSION['domain_uuid']."', ";
				$sql .= "'$call_acl_uuid', ";
				$sql .= "'$call_acl_order', ";
				$sql .= "'$call_acl_name', ";
				$sql .= "'$call_acl_source', ";
				$sql .= "'$call_acl_destination', ";
				$sql .= "'$call_acl_action', ";
				$sql .= "'$call_acl_enabled'";
				$sql .= ")";
				$db->exec(check_sql($sql));
				unset($sql);

				messages::add($text['label-add-complete']);
				header("Location: call_acl.php");
				return;
			} //if ($action == "add")

			if ($action == "update") {

				$sql = "UPDATE v_call_acl SET ";
				$sql .= "call_acl_order = '$call_acl_order', ";
				$sql .= "call_acl_name = '$call_acl_name', ";
				$sql .= "call_acl_source = '$call_acl_source', ";
				$sql .= "call_acl_destination = '$call_acl_destination', ";
				$sql .= "call_acl_action = '$call_acl_action', ";
				$sql .= "call_acl_enabled = '$call_acl_enabled' ";
				$sql .= "WHERE domain_uuid = '".$_SESSION['domain_uuid']."' ";
				$sql .= "AND call_acl_uuid = '$call_acl_uuid'";
				$db->exec(check_sql($sql));
				unset($sql);

				messages::add($text['label-update-complete']);
				header("Location: call_acl.php");
				return;
			} //if ($action == "update")
		} //if ($_POST["persistformvar"] != "true")
	} //(count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

//pre-populate the form
	if (count($_GET)>0 && $_POST["persistformvar"] != "true") {
		$call_acl_uuid = $_GET["id"];
		$sql = "SELECT * FROM v_call_acl ";
		$sql .= "WHERE domain_uuid = '".$_SESSION['domain_uuid']."' ";
		$sql .= "AND call_acl_uuid = '$call_acl_uuid' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll();

		foreach ($result as &$row) {
			$call_acl_order = $row["call_acl_order"];
			$call_acl_name = $row["call_acl_name"];
			$call_acl_source = $row["call_acl_source"];
			$call_acl_destination = $row["call_acl_destination"];
			$call_acl_action = $row["call_acl_action"];
			$call_acl_enabled = $row["call_acl_enabled"];
			break; //limit to 1 row ? Should be only 1 result at all
		}
		unset ($prep_statement, $sql);
	}

// Get maximum order number

	$call_acl_order_max = isset($_SESSION['call_acl']['max_order']['numeric']) ? (int)$_SESSION['call_acl']['max_order']['numeric'] : 20;
	if (!$call_acl_order_max) {
		$call_acl_order_max = 20;
	}

//show the header
	require_once "resources/header.php";

//show the content

	echo "<form method='post' name='frm' action=''>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	if ($action == "add") {
		echo "<td align='left' width='30%' nowrap='nowrap'><b>".$text['label-edit-add']."</b></td>\n";
	}
	if ($action == "update") {
		echo "<td align='left' width='30%' nowrap='nowrap'><b>".$text['label-edit-edit']."</b></td>\n";
	}
	echo "<td width='70%' align='right'>";
	echo "	<input type='button' class='btn' name='' alt='".$text['button-back']."' onclick=\"window.location='call_acl.php'\" value='".$text['button-back']."'>";
	echo "	<input type='submit' name='submit' class='btn' value='".$text['button-save']."'>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td align='left' colspan='2'>\n";
	if ($action == "add") {
		echo $text['label-add-note']."<br /><br />\n";
	}
	if ($action == "update") {
		echo $text['label-edit-note']."<br /><br />\n";
	}
	echo "</td>\n";
	echo "</tr>\n";

	// Show order TODO
	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-call_acl_order']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "<select name='call_acl_order' class='formfld'>\n";
	for ($i = 0; $i <= $call_acl_order_max; $i++) {
		$selected = ($i == $call_acl_order) ? "selected" : "";
		echo "<option value='$i' ".$selected.">$i</option>\n";
	}
	echo "		</select>\n";
	echo "<br />\n";
	echo $text['description-call_acl_order']."\n";
	echo "<br />\n";
	echo "</td>\n";
	echo "</tr>\n";

	// Show name
	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-call_acl_name']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='call_acl_name' maxlength='255' value=\"".escape($call_acl_name)."\" required='required'>\n";
	echo "<br />\n";
	echo $text['description-call_acl_name']."\n";
	echo "</td>\n";
	echo "</tr>\n";

	// Show source
	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-call_acl_source']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='call_acl_source' maxlength='255' value=\"".escape($call_acl_source)."\" required='required'>\n";
	echo "<br />\n";
	echo $text['description-call_acl_source']."\n";
	echo "</td>\n";
	echo "</tr>\n";

	// Show destination
	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-call_acl_destination']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='call_acl_destination' maxlength='255' value=\"".escape($call_acl_destination)."\" required='required'>\n";
	echo "<br />\n";
	echo $text['description-call_acl_destination']."\n";
	echo "</td>\n";
	echo "</tr>\n";



	// Show action
	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-call_acl_action']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<select class='formfld' name='call_acl_action'>\n";
	if ($call_acl_action == "reject") {
		echo "	<option value='allow'>".$text['label-allow']."</option>\n";
		echo "	<option value='reject' selected='selected'>".$text['label-reject']."</option>\n";
	} else {
		echo "	<option value='allow' selected='selected'>".$text['label-allow']."</option>\n";
		echo "	<option value='reject'>".$text['label-reject']."</option>\n";
	}
	echo "	</select>\n";
	echo "<br />\n";
	echo $text['description-call_acl_action']."\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	// Show enabled
	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-call_acl_enabled']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<select class='formfld' name='call_acl_enabled'>\n";
	echo "		<option value='true' ".(($call_acl_enabled == "true") ? "selected" : null).">".$text['label-true']."</option>\n";
	echo "		<option value='false' ".(($call_acl_enabled == "false") ? "selected" : null).">".$text['label-false']."</option>\n";
	echo "	</select>\n";
	echo "<br />\n";
	echo $text['description-call_acl_enabled']."\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	if ($action == "update") {
		echo "		<input type='hidden' name='id' value='".escape($call_acl_uuid)."'>\n";
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
	echo $text['description-call_acl_templates'];
	echo "</td>";
	echo "</tr>";
	echo "</table>";

//include the footer
	require_once "resources/footer.php";
?>
