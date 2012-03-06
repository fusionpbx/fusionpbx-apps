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
require_once "root.php";
require_once "includes/require.php";
require_once "includes/checkauth.php";
if (if_group("admin") || if_group("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//action add or update
	if (isset($_REQUEST["id"])) {
		$action = "update";
		$invoice_uuid = check_str($_REQUEST["id"]);
	}
	else {
		$action = "add";
	}

//get http post variables and set them to php variables
	if (count($_POST)>0) {
		$invoice_number = check_str($_POST["invoice_number"]);
		$contact_uuid_from = check_str($_POST["contact_uuid_from"]);
		$contact_uuid_to = check_str($_POST["contact_uuid_to"]);
		$invoice_notes = check_str($_POST["invoice_notes"]);
	}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';
	if ($action == "update") {
		$invoice_uuid = check_str($_POST["invoice_uuid"]);
	}

	//check for all required data
		//if (strlen($invoice_number) == 0) { $msg .= "Please provide: Invoice Number<br>\n"; }
		//if (strlen($invoice_date) == 0) { $msg .= "Please provide: Date<br>\n"; }
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

	//add or update the database
		if ($_POST["persistformvar"] != "true") {
			if ($action == "add") {
				$invoice_uuid = uuid();
				$sql = "insert into v_invoices ";
				$sql .= "(";
				$sql .= "domain_uuid, ";
				$sql .= "invoice_uuid, ";
				$sql .= "invoice_number, ";
				$sql .= "contact_uuid_from, ";
				$sql .= "contact_uuid_to, ";
				$sql .= "invoice_notes, ";
				$sql .= "invoice_date ";
				$sql .= ")";
				$sql .= "values ";
				$sql .= "(";
				$sql .= "'$domain_uuid', ";
				$sql .= "'$invoice_uuid', ";
				$sql .= "'$invoice_number', ";
				$sql .= "'$contact_uuid_from', ";
				$sql .= "'$contact_uuid_to', ";
				$sql .= "'$invoice_notes', ";
				$sql .= "now() ";
				$sql .= ")";
				$db->exec(check_sql($sql));
				unset($sql);

				//require_once "includes/header.php";
				echo "<meta http-equiv=\"refresh\" content=\"2;url=v_invoices.php\">\n";
				//echo "<meta http-equiv=\"refresh\" content=\"2;url=v_invoices.php?id=$contact_uuid\">\n";
				echo "<div align='center'>\n";
				echo "Add Complete\n";
				echo "</div>\n";
				require_once "includes/footer.php";
				return;
			} //if ($action == "add")

			if ($action == "update") {
				$sql = "update v_invoices set ";
				$sql .= "invoice_number = '$invoice_number', ";
				$sql .= "contact_uuid_from = '$contact_uuid_from', ";
				$sql .= "contact_uuid_to = '$contact_uuid_to', ";
				$sql .= "invoice_notes = '$invoice_notes' ";
				$sql .= "where domain_uuid = '$domain_uuid' ";
				$sql .= "and invoice_uuid = '$invoice_uuid' ";
				$db->exec(check_sql($sql));
				unset($sql);

				require_once "includes/header.php";
				echo "<meta http-equiv=\"refresh\" content=\"2;url=v_invoices.php\">\n";
				echo "<div align='center'>\n";
				echo "Update Complete\n";
				echo "</div>\n";
				require_once "includes/footer.php";
				return;
			} //if ($action == "update")
		} //if ($_POST["persistformvar"] != "true") 
} //(count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

//pre-populate the form
	if (count($_GET)>0 && $_POST["persistformvar"] != "true") {
		$invoice_uuid = $_GET["id"];
		$sql = "";
		$sql .= "select * from v_invoices ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and invoice_uuid = '$invoice_uuid' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
		foreach ($result as &$row) {
			$invoice_number = $row["invoice_number"];
			$invoice_date = $row["invoice_date"];
			$contact_uuid_from = $row["contact_uuid_from"];
			$contact_uuid_to = $row["contact_uuid_to"];
			$invoice_notes = $row["invoice_notes"];
			break; //limit to 1 row
		}
		unset ($prep_statement);
	}

//show the header
	require_once "includes/header.php";

//get the default invoice number and contact_uuid_from
	if ($action == "add") {
		$sql = "";
		$sql .= "select * from v_invoices ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "order by invoice_uuid desc ";
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

//show the content
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing=''>\n";
	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "	  <br>";

	echo "<form method='post' name='frm' action=''>\n";
	echo "<div align='center'>\n";
	echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";
	echo "<tr>\n";
	if ($action == "add") {
		echo "<td align='left' width='30%' nowrap='nowrap'><b>Invoice Add</b></td>\n";
	}
	if ($action == "update") {
		echo "<td align='left' width='30%' nowrap='nowrap'><b>Invoice Edit</b></td>\n";
	}
	echo "<td width='70%' align='right'>\n";
	echo "	<input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_invoice_pdf.php?id=".$_GET["id"]."'\" value='PDF'>\n";
	echo "	<input type='button' class='btn' name='' alt='back' onclick=\"history.go(-1);\" value='Back'>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	Invoice Number:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "  <input class='formfld' type='text' name='invoice_number' maxlength='255' value='$invoice_number'>\n";
	echo "<br />\n";
	echo "Enter the invoice number.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	From:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	$sql = "";
	$sql .= " select contact_uuid, contact_organization, contact_name_given, contact_name_family from v_contacts ";
	$sql .= " where domain_uuid = '$domain_uuid' ";
	$sql .= " order by contact_organization asc ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
	unset ($prep_statement, $sql);
	echo "<select name=\"contact_uuid_from\" id=\"contact_uuid_from\" class=\"formfld\">\n";
	echo "<option value=\"\"></option>\n";
	foreach($result as $row) {
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
			echo "<option value=\"".$row['contact_uuid']."\" selected=\"selected\">".$contact_name." $contact_uuid</option>\n";
		}
		else {
			echo "<option value=\"".$row['contact_uuid']."\">".$contact_name."</option>\n";
		}
	}
	unset($sql, $result, $row_count);
	echo "</select>\n";
	echo "<br />\n";
	echo "Select the Contact to send the send the invoice from. \n";
	echo "<a href='".PROJECT_PATH."/app/contacts/contacts_edit.php?id=".$contact_uuid_from."'>View</a>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	To:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	$sql = "";
	$sql .= " select contact_uuid, contact_organization, contact_name_given, contact_name_family from v_contacts ";
	$sql .= " where domain_uuid = '$domain_uuid' ";
	$sql .= " order by contact_organization asc ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
	unset ($prep_statement, $sql);
	echo "<select name=\"contact_uuid_to\" id=\"contact_uuid_to\" class=\"formfld\">\n";
	echo "<option value=\"\"></option>\n";
	foreach($result as $row) {
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
			echo "<option value=\"".$row['contact_uuid']."\" selected=\"selected\">".$contact_name." $contact_uuid</option>\n";
		}
		else {
			echo "<option value=\"".$row['contact_uuid']."\">".$contact_name."</option>\n";
		}
	}
	unset($sql, $result, $row_count);
	echo "</select>\n";
	echo "<br />\n";
	echo "Select the Contact to send the send the invoice to. \n";
	echo "<a href='".PROJECT_PATH."/app/contacts/contacts_edit.php?id=".$contact_uuid_to."'>View</a>\n";
	echo "</td>\n";
	echo "</tr>\n";

	if ($action == "update") {
		//prepare the invoice date
			$invoice_date = date("d", strtotime($invoice_date)).' '.date("M", strtotime($invoice_date)).' '.date("Y", strtotime($invoice_date));
		//show the formatted date
			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
			echo "	Date:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "  $invoice_date\n";
			echo "</td>\n";
			echo "</tr>\n";
	}

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	Notes:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "  <textarea class='formfld' type='text' name='invoice_notes'>$invoice_notes</textarea>\n";
	echo "<br />\n";
	echo "Enter the invoice note.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='invoice_uuid' value='$invoice_uuid'>\n";
	}
	echo "				<input type='submit' name='submit' class='btn' value='Save'>\n";
	echo "		</td>\n";
	echo "	</tr>";
	echo "</table>";
	echo "</form>";

	if ($action == "update") {
		require "v_invoice_items.php";
	}

	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";

//include the footer
	require_once "includes/footer.php";
?>