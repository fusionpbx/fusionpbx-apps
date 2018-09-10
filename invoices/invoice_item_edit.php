<?php
/* $Id$ */
/*
	Copyright (C) 2008-2013 Mark J Crane
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice,
	   this list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGE.
*/
//includes
	require_once "root.php";
	require_once "resources/require.php";
	require_once "resources/check_auth.php";
	
//check permissionss
	if (permission_exists('invoice_item_add') || permission_exists('invoice_item_edit')) {
		//access granted
	}
	else {
		echo "access denied";
		exit;
	}

//add multi-lingual support
	$language = new text;
	$text = $language->get();

//action add or update
	if (isset($_REQUEST["id"])) {
		$action = "update";
		$invoice_item_uuid = check_str($_REQUEST["id"]);
		$back = check_str($_REQUEST['back']);
	}
	else {
		$action = "add";
		$back = check_str($_REQUEST['back']);
	}

//get http post variables and set them to php variables
	if (strlen(count($_REQUEST)) > 0) {
		$contact_uuid = check_str($_REQUEST["contact_uuid"]);
		$invoice_uuid = check_str($_REQUEST["invoice_uuid"]);
	}	
	if (count($_POST) > 0) {
		$item_qty = check_str($_POST["item_qty"]);
		$item_desc = check_str($_POST["item_desc"]);
		$item_unit_price = check_str($_POST["item_unit_price"]);
	}

//process the data from the http post
	if (count($_POST) > 0 && strlen($_POST["persistformvar"]) == 0) {

		//get the id
			if ($action == "update") {
				$invoice_item_uuid = check_str($_POST["invoice_item_uuid"]);
			}

		//check for all required data
			$msg = '';
			//if (strlen($invoice_item_uuid) == 0) { $msg .= $text['message-required']." ".$text['label-invoice_item_uuid']."<br>\n"; }
			//if (strlen($domain_uuid) == 0) { $msg .= $text['message-required']." ".$text['label-domain_uuid']."<br>\n"; }
			//if (strlen($invoice_uuid) == 0) { $msg .= $text['message-required']." ".$text['label-invoice_uuid']."<br>\n"; }
			if (strlen($item_qty) == 0) { $msg .= $text['message-required']." ".$text['label-item_qty']."<br>\n"; }
			//if (strlen($item_desc) == 0) { $msg .= $text['message-required']." ".$text['label-item_desc']."<br>\n"; }
			//if (strlen($item_unit_price) == 0) { $msg .= $text['message-required']." ".$text['label-item_unit_price']."<br>\n"; }
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
			if ($_POST["persistformvar"] != "true") {
				if ($action == "add" && permission_exists('invoice_item_add')) {
					$invoice_item_uuid = uuid();
					$sql = "insert into v_invoice_items ";
					$sql .= "(";
					$sql .= "domain_uuid, ";
					$sql .= "invoice_uuid, ";
					$sql .= "invoice_item_uuid, ";
					$sql .= "item_qty, ";
					$sql .= "item_desc, ";
					$sql .= "item_unit_price ";
					$sql .= ")";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'$domain_uuid', ";
					$sql .= "'$invoice_uuid', ";
					$sql .= "'$invoice_item_uuid', ";
					$sql .= "'$item_qty', ";
					$sql .= "'$item_desc', ";
					$sql .= "'$item_unit_price' ";
					$sql .= ")";
					$db->exec(check_sql($sql));
					unset($sql);

					//set redirect
					$_SESSION['message'] = $text['message-add'];
					$back = ($back != '') ? "&back=".$back : null;
					header("Location: invoice_edit.php?id=".$invoice_uuid."&contact_uuid=".$contact_uuid.$back);
					return;
	
				} //if ($action == "add")

				if ($action == "update" && permission_exists('invoice_item_edit')) {
					$sql = "update v_invoice_items set ";
					$sql .= "item_qty = '$item_qty', ";
					$sql .= "item_desc = '$item_desc', ";
					$sql .= "item_unit_price = '$item_unit_price' ";
					$sql .= "where domain_uuid = '$domain_uuid' ";
					$sql .= "and invoice_item_uuid = '$invoice_item_uuid'";
					$db->exec(check_sql($sql));
					unset($sql);

					//set redirect
					$_SESSION['message'] = $text['message-update'];
					$back = ($back != '') ? "&back=".$back : null;
					header("Location: invoice_edit.php?id=".$invoice_uuid."&contact_uuid=".$contact_uuid.$back);
					return;

				} //if ($action == "update")
			} //if ($_POST["persistformvar"] != "true")
	} //(count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

//pre-populate the form
	if (count($_GET) > 0 && $_POST["persistformvar"] != "true") {
		$invoice_item_uuid = check_str($_GET["id"]);
		$sql = "select * from v_invoice_items ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and invoice_item_uuid = '$invoice_item_uuid' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
		foreach ($result as &$row) {
			$item_qty = $row["item_qty"];
			$item_desc = $row["item_desc"];
			$item_unit_price = $row["item_unit_price"];
			break; //limit to 1 row
		}
		unset ($prep_statement);
	}

//show the header
	require_once "resources/header.php";

//show the content
	echo "<form method='post' name='frm' action=''>\n";
	echo "<table width='100%'  border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<td align='left' width='30%' valign='top' nowrap='nowrap'><b>".$text['title-invoice_item']."</b></td>\n";
	echo "<td width='70%' align='right' valign='top'>\n";
	echo "	<input type='button' class='btn' name='' alt='".$text['button-back']."' onclick=\"history.go(-1);\" value='".$text['button-back']."'>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-item_qty']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "  <input class='formfld' type='text' name='item_qty' maxlength='255' value='$item_qty'>\n";
	echo "<br />\n";
	echo $text['description-item_qty']."\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-item_unit_price']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "  <input class='formfld' type='text' name='item_unit_price' maxlength='255' value='$item_unit_price'>\n";
	echo "<br />\n";
	echo $text['description-item_unit_price']."\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-item_desc']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "  <textarea class='formfld' type='text' rows='15' name='item_desc'>$item_desc</textarea>\n";
	echo "<br />\n";
	//echo $text['description-item_desc']."\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	echo "			<input type='hidden' name='invoice_uuid' value='$invoice_uuid'>\n";
	echo "			<input type='hidden' name='contact_uuid' value='$contact_uuid'>\n";
	if ($action == "update") {
		if ($back != '') {
			echo "		<input type='hidden' name='back' value='".$back."'>";
		}
		echo "		<input type='hidden' name='invoice_item_uuid' value='$invoice_item_uuid'>\n";
	}
	echo "			<br><input type='submit' name='submit' class='btn' value='".$text['button-save']."'>\n";
	echo "		</td>\n";
	echo "	</tr>";
	echo "</table>";
	echo "<br><br>";
	echo "</form>";

//include the footer
	require_once "resources/footer.php";

?>
