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
	Portions created by the Initial Developer are Copyright (C) 2008-2023
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/

//includes files
	require_once dirname(__DIR__, 2) . "/resources/require.php";
	require_once "resources/check_auth.php";

//check permissions
	if (permission_exists('invoice_add') || permission_exists('invoice_edit')) {
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
		$invoice_uuid = check_str($_REQUEST["id"]);
		$back = check_str($_REQUEST['back']);
	}
	else {
		$action = "add";
	}

//get http post variables and set them to php variables
	if (count($_POST) > 0) {
		$invoice_number = check_str($_POST["invoice_number"]);
		$invoice_type = check_str($_POST["invoice_type"]);
		$contact_uuid_from = check_str($_POST["contact_uuid_from"]);
		$contact_uuid_to = check_str($_POST["contact_uuid_to"]);
		$invoice_purchase_order_number = check_str($_POST["invoice_purchase_order_number"]);
		$invoice_currency = check_str($_POST["invoice_currency"]);
		$invoice_notes = check_str($_POST["invoice_notes"]);
	}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';
	if ($action == "update") {
		$invoice_uuid = check_str($_POST["invoice_uuid"]);
	}

	//check for all required data
		//if (strlen($invoice_uuid) == 0) { $msg .= $text['message-required']." ".$text['label-invoice_uuid']."<br>\n"; }
		//if (strlen($domain_uuid) == 0) { $msg .= $text['message-required']." ".$text['label-domain_uuid']."<br>\n"; }
		//if (strlen($contact_uuid_from) == 0) { $msg .= $text['message-required']." ".$text['label-contact_uuid_from']."<br>\n"; }
		//if (strlen($contact_uuid_to) == 0) { $msg .= $text['message-required']." ".$text['label-contact_uuid_to']."<br>\n"; }
		//if (strlen($invoice_number) == 0) { $msg .= $text['message-required']." ".$text['label-invoice_number']."<br>\n"; }
		//if (strlen($invoice_date) == 0) { $msg .= $text['message-required']." ".$text['label-invoice_date']."<br>\n"; }
		//if (strlen($invoice_notes) == 0) { $msg .= $text['message-required']." ".$text['label-invoice_notes']."<br>\n"; }
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
			if ($action == "add" && permission_exists('invoice_add')) {
				$invoice_uuid = uuid();
				$sql = "insert into v_invoices ";
				$sql .= "(";
				$sql .= "domain_uuid, ";
				$sql .= "invoice_uuid, ";
				$sql .= "invoice_number, ";
				$sql .= "invoice_type, ";
				$sql .= "contact_uuid_from, ";
				$sql .= "contact_uuid_to, ";
				$sql .= "invoice_purchase_order_number, ";
				$sql .= "invoice_currency, ";
				$sql .= "invoice_notes, ";
				$sql .= "invoice_date ";
				$sql .= ")";
				$sql .= "values ";
				$sql .= "(";
				$sql .= "'$domain_uuid', ";
				$sql .= "'$invoice_uuid', ";
				$sql .= "'$invoice_number', ";
				$sql .= "'$invoice_type', ";
				$sql .= "'$contact_uuid_from', ";
				$sql .= "'$contact_uuid_to', ";
				$sql .= "'$invoice_purchase_order_number', ";
				$sql .= "'$invoice_currency', ";
				$sql .= "'$invoice_notes', ";
				$sql .= "now() ";
				$sql .= ")";
				$db->exec(check_sql($sql));
				unset($sql);

				//set redirect
				$_SESSION['message'] = $text['message-add'];
				header("Location: invoices.php");
				return;

			} //if ($action == "add")

			if ($action == "update" && permission_exists('invoice_edit')) {
				$invoice_paid = check_str($_POST["invoice_paid"]);
				if ($invoice_paid == '1') {
					$invoice_paid_date = check_str($_POST["invoice_paid_date"]);
					$invoice_paid_method = check_str($_POST["invoice_paid_method"]);
					$invoice_paid_method_ref = check_str($_POST["invoice_paid_method_ref"]);
				}

				//set defaults
				$invoice_paid = ($invoice_paid != '1') ? 'null' : $invoice_paid;
				$invoice_paid_date = ($invoice_paid_date == '') ? 'null' : "'".$invoice_paid_date."'";
				$invoice_paid_method = ($invoice_paid_method == '') ? 'null' : "'".$invoice_paid_method."'";
				$invoice_paid_method_ref = ($invoice_paid_method_ref == '') ? 'null' : "'".$invoice_paid_method_ref."'";

				$sql = "update v_invoices set ";
				$sql .= "invoice_number = '$invoice_number', ";
				$sql .= "invoice_type = '$invoice_type', ";
				$sql .= "contact_uuid_from = '$contact_uuid_from', ";
				$sql .= "contact_uuid_to = '$contact_uuid_to', ";
				$sql .= "invoice_purchase_order_number = '$invoice_purchase_order_number', ";
				$sql .= "invoice_currency = '$invoice_currency', ";
				$sql .= "invoice_paid = $invoice_paid, ";
				$sql .= "invoice_paid_date = $invoice_paid_date, ";
				$sql .= "invoice_paid_method = $invoice_paid_method, ";
				$sql .= "invoice_paid_method_ref = $invoice_paid_method_ref, ";
				$sql .= "invoice_notes = '$invoice_notes' ";
				$sql .= "where domain_uuid = '$domain_uuid' ";
				$sql .= "and invoice_uuid = '$invoice_uuid' ";
				$db->exec(check_sql($sql));
				unset($sql);

				//set redirect
				$_SESSION['message'] = $text['message-update'];
				header("Location: ".(($back != '') ? $back : "invoices.php"));
				return;

			} //if ($action == "update")
		} //if ($_POST["persistformvar"] != "true")
} //(count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

//pre-populate the form
	if (count($_GET)>0 && $_POST["persistformvar"] != "true") {
		$invoice_uuid = check_str($_GET["id"]);
		$sql = "select * from v_invoices ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and invoice_uuid = '$invoice_uuid' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
		foreach ($result as &$row) {
			$invoice_number = $row["invoice_number"];
			$invoice_type = $row["invoice_type"];
			$invoice_date = $row["invoice_date"];
			$invoice_paid = $row["invoice_paid"];
			$invoice_paid_date = $row["invoice_paid_date"];
			$invoice_paid_method = $row["invoice_paid_method"];
			$invoice_paid_method_ref = $row["invoice_paid_method_ref"];
			$contact_uuid_from = $row["contact_uuid_from"];
			$contact_uuid_to = $row["contact_uuid_to"];
			$invoice_purchase_order_number = $row["invoice_purchase_order_number"];
			$invoice_currency = $row["invoice_currency"];
			$invoice_notes = $row["invoice_notes"];
			break; //limit to 1 row
		}
		unset ($prep_statement);

		//format paid date (if any)
		if ($invoice_paid_date != '') {
			$tmp = explode(' ',$invoice_paid_date);
			$invoice_paid_date = $tmp[0];
		}
	}

//set the default currency
	if (strlen($invoice_currency) == 0) {
		$invoice_currency = 'USD';
	}

//get the list of contacts
	$sql = "select contact_uuid, contact_organization, contact_name_given, contact_name_family from v_contacts ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "order by contact_organization asc ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$contacts = $prep_statement->fetchAll(PDO::FETCH_NAMED);
	unset ($prep_statement, $sql);

//get the default invoice number and contact_uuid_from
	if ($action == "add") {
		$sql = "select * from v_invoices ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "order by invoice_number desc ";
		$sql .= "limit 1 ";
		$prep_statement = $db->prepare(check_sql($sql));
		if ($prep_statement) {
			$prep_statement->execute();
			$row = $prep_statement->fetch();
			$invoice_number = $row['invoice_number'] + 1;
			$contact_uuid_from = $row['contact_uuid_from'];
			unset ($prep_statement);
		}
	}

//set the contact 'to' when adding an invoice
	if ($action == "add") {
		$contact_uuid_to = $_REQUEST['contact_uuid'];
	}

//show the header
	require_once "resources/header.php";

//show the content
	echo "<form method='post' name='frm' action=''>\n";
	echo "<table width='100%'  border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<td align='left' width='30%' valign='top' nowrap='nowrap'><b>".$text['title-invoice']."</b></td>\n";
	echo "<td width='70%' align='right' valign='top'>\n";
	echo "	<input type='button' class='btn' name='' alt='".$text['button-back']."' onclick=\"window.location='".(($back != '') ? $back : "invoices.php")."'\" value='".$text['button-back']."'>\n";
	if ($action == "update") {
		echo "	<input type='button' class='btn' name='' alt='".$text['button-pdf']."' onclick=\"window.open('invoice_pdf.php?id=".$_GET["id"]."&type=' + document.getElementById('invoice_type').options[document.getElementById('invoice_type').selectedIndex].value);\" value='".$text['button-pdf']."'>\n";
	}
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-invoice_number']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "  <input class='formfld' type='text' name='invoice_number' maxlength='255' value='".escape($invoice_number)."'>\n";
	echo "<br />\n";
	echo $text['description-invoice_number']."\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-invoice_type']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<select name='invoice_type' id='invoice_type' class='formfld'>\n";
	echo "		<option value='invoice' ".(($invoice_type == 'invoice') ? "selected" : null).">".$text['label-invoice_type_invoice']."</option>";
	echo "		<option value='quote' ".(($invoice_type == 'quote') ? "selected" : null).">".$text['label-invoice_type_quote']."</option>";
	echo "	</select>";
	echo "<br />\n";
	echo $text['description-invoice_type']."\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-contact_uuid_from']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "<select name=\"contact_uuid_from\" id=\"contact_uuid_from\" class=\"formfld\">\n";
	echo "<option value=\"\"></option>\n";
	foreach($contacts as $row) {
		$contact_name = '';
		if (strlen($row['contact_organization']) > 0) {
			$contact_name = $row['contact_organization'];
		}
		if (strlen($row['contact_name_family']) > 0) {
			if (strlen($contact_name) > 0) { $contact_name .= ", "; }
			$contact_name .= $row['contact_name_family'];
		}
		if (strlen($row['contact_name_given']) > 0) {
			if (strlen($contact_name) > 0) { $contact_name .= ", "; }
			$contact_name .= $row['contact_name_given'];
		}
		if ($row['contact_uuid'] == $contact_uuid_from) {
			echo "<option value=\"".escape($row['contact_uuid'])."\" selected=\"selected\">".escape($contact_name)." ".escape($contact_uuid)."</option>\n";
		}
		else {
			echo "<option value=\"".escape($row['contact_uuid'])."\">".escape($contact_name)."</option>\n";
		}
	}
	unset($sql, $result, $row_count);
	echo "</select>\n";
	echo "<br />\n";
	echo $text['description-contact_uuid_from']." \n";
	echo "<a href='".PROJECT_PATH."/app/contacts/contact_edit.php?id=".escape($contact_uuid_from)."'>".$text['button-view']."</a>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-contact_uuid_to']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "<select name=\"contact_uuid_to\" id=\"contact_uuid_to\" class=\"formfld\">\n";
	echo "<option value=\"\"></option>\n";
	foreach($contacts as $row) {
		$contact_name = '';
		if (strlen($row['contact_organization']) > 0) {
			$contact_name = $row['contact_organization'];
		}
		if (strlen($row['contact_name_family']) > 0) {
			if (strlen($contact_name) > 0) { $contact_name .= ", "; }
			$contact_name .= $row['contact_name_family'];
		}
		if (strlen($row['contact_name_given']) > 0) {
			if (strlen($contact_name) > 0) { $contact_name .= ", "; }
			$contact_name .= $row['contact_name_given'];
		}
		if ($row['contact_uuid'] == $contact_uuid_to) {
			echo "<option value=\"".escape($row['contact_uuid'])."\" selected=\"selected\">".escape($contact_name)." ".escape($contact_uuid)."</option>\n";
		}
		else {
			echo "<option value=\"".escape($row['contact_uuid'])."\">".escape($contact_name)."</option>\n";
		}
	}
	unset($sql, $result, $row_count);
	echo "</select>\n";
	echo "<br />\n";
	echo $text['description-contact_uuid_to']." \n";
	echo "<a href='".PROJECT_PATH."/app/contacts/contact_edit.php?id=".escape($contact_uuid_to)."'>".$text['button-view']."</a>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-invoice_purchase_order_number']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "  <input class='formfld' type='text' name='invoice_purchase_order_number' maxlength='255' value='".escape($invoice_purchase_order_number)."'>\n";
	echo "<br />\n";
	echo $text['description-invoice_purchase_order_number']."\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-invoice_currency']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "  <input class='formfld' type='text' name='invoice_currency' maxlength='255' value='".escape($invoice_currency)."'>\n";
	echo "<br />\n";
	echo $text['description-invoice_currency']."\n";
	echo "</td>\n";
	echo "</tr>\n";

	if ($action == "update") {
		//prepare the invoice date
			$invoice_date = date("d", strtotime($invoice_date)).' '.date("M", strtotime($invoice_date)).' '.date("Y", strtotime($invoice_date));
		//show the formatted date
			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
			echo "	".$text['label-invoice_created']."\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "  ".$invoice_date."\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
			echo "	".$text['label-invoice_paid']."\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<table cellpadding='0' cellspacing='0' border='0'>";
			echo "		<tr>";
			echo "			<td><input type='checkbox' class='formfld' name='invoice_paid' id='invoice_paid' value='1' ".(($invoice_paid) ? "checked='checked'" : null)." onchange=\"$('#td_paid_details').fadeToggle('fast');\"></td>";
			echo "			<td id='td_paid_details' style='".((!$invoice_paid) ? "display: none;" : null)." padding: 0px 3px 0px 8px;'>";
			echo "				<input type='text' class='formfld' style='min-width: 85px; max-width: 85px;' name='invoice_paid_date' data-calendar=\"{format: '%Y-%m-%d', listYears: true, hideOnPick: true, fxName: null, showButtons: true}\" placeholder='Date' value='".$invoice_paid_date."'>";
			echo "				<select name='invoice_paid_method' id='invoice_paid_method' class='formfld' onchange=\"document.getElementById('invoice_paid_method_ref').focus();\">\n";
			echo "					<option value=''></option>";
			echo "					<option value='pp' ".(($invoice_paid_method == 'pp') ? "selected" : null).">".$text['label-invoice_method_paypal']."</option>";
			echo "					<option value='chk' ".(($invoice_paid_method == 'chk') ? "selected" : null).">".$text['label-invoice_method_check']."</option>";
			echo "					<option value='cc' ".(($invoice_paid_method == 'cc') ? "selected" : null).">".$text['label-invoice_method_credit_card']."</option>";
			echo "					<option value='csh' ".(($invoice_paid_method == 'csh') ? "selected" : null).">".$text['label-invoice_method_cash']."</option>";
			echo "				</select>";
			echo "				<input type='text' class='formfld' style='min-width: 85px;' name='invoice_paid_method_ref' id='invoice_paid_method_ref' placeholder='Ref #' value='".escape($invoice_paid_method_ref)."'>";
			echo "			</td>";
			echo "		</tr>";
			echo "	</table>";
			echo "</td>\n";
			echo "</tr>\n";
	}

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-invoice_notes']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<textarea class='formfld' type='text' name='invoice_notes'>".escape($invoice_notes)."</textarea>\n";
	echo "<br />\n";
	echo $text['description-invoice_notes']."\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	if ($action == "update") {
		if ($back != '') {
			echo "		<input type='hidden' name='back' value='".escape($back)."'>";
		}
		echo "		<input type='hidden' name='invoice_uuid' value='".escape($invoice_uuid)."'>\n";
	}
	echo "			<br><input type='submit' name='submit' class='btn' value='".$text['button-save']."'>\n";
	echo "		</td>\n";
	echo "	</tr>";
	echo "</table>";
	echo "</form>";

	if ($action == "update") {
		require "invoice_items.php";
	}

//include the footer
	require_once "resources/footer.php";
?>
