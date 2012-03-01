<?php
require_once "root.php";
require_once "includes/require.php";

//action add or update
	if (isset($_REQUEST["id"])) {
		$action = "update";
		$invoice_item_uuid = check_str($_REQUEST["id"]);
	}
	else {
		$action = "add";
	}

if (strlen(count($_REQUEST)) > 0) {
	$contact_uuid = check_str($_REQUEST["contact_uuid"]);
	$invoice_uuid = check_str($_REQUEST["invoice_uuid"]);
}

//get http post variables and set them to php variables
	if (count($_POST)>0) {
		$item_qty = check_str($_POST["item_qty"]);
		$item_desc = check_str($_POST["item_desc"]);
		$item_unit_price = check_str($_POST["item_unit_price"]);
	}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';
	if ($action == "update") {
		$invoice_item_uuid = check_str($_POST["invoice_item_uuid"]);
	}

	//check for all required data
		//if (strlen($domain_uuid) == 0) { $msg .= "Please provide: domain_uuid<br>\n"; }
		//if (strlen($item_qty) == 0) { $msg .= "Please provide: Quantity<br>\n"; }
		//if (strlen($item_desc) == 0) { $msg .= "Please provide: Description<br>\n"; }
		//if (strlen($item_unit_price) == 0) { $msg .= "Please provide: Price<br>\n"; }
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

			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_invoices_edit.php?id=$invoice_uuid&contact_uuid=$contact_uuid\">\n";
			echo "<div align='center'>\n";
			echo "Add Complete $sql2\n";
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		} //if ($action == "add")

		if ($action == "update") {
			$sql = "update v_invoice_items set ";
			$sql .= "invoice_uuid = '$invoice_uuid', ";
			$sql .= "domain_uuid = '$domain_uuid', ";
			$sql .= "item_qty = '$item_qty', ";
			$sql .= "item_desc = '$item_desc', ";
			$sql .= "item_unit_price = '$item_unit_price' ";
			$sql .= "where invoice_item_uuid = '$invoice_item_uuid'";
			$db->exec(check_sql($sql));
			unset($sql);

			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_invoices_edit.php?id=$invoice_uuid&contact_uuid=$contact_uuid\">\n";
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
		$invoice_item_uuid = $_GET["id"];
		$sql = "";
		$sql .= "select * from v_invoice_items ";
		$sql .= "where invoice_item_uuid = '$invoice_item_uuid' ";
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
	require_once "includes/header.php";

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
		echo "<td align='left' width='30%' nowrap='nowrap'><b>Invoice Item Add</b></td>\n";
	}
	if ($action == "update") {
		echo "<td align='left' width='30%' nowrap='nowrap'><b>Invoice Item Edit</b></td>\n";
	}
	echo "<td width='70%' align='right'>\n";
	echo "	<input type='button' class='btn' name='' alt='back' onclick=\"history.go(-1);\" value='Back'>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	Quantity:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "  <input class='formfld' type='text' name='item_qty' maxlength='255' value='$item_qty'>\n";
	echo "<br />\n";
	echo "Enter the Quantity\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	Unit Price:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "  <input class='formfld' type='text' name='item_unit_price' maxlength='255' value='$item_unit_price'>\n";
	echo "<br />\n";
	echo "Enter the unit price.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	Description:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "  <textarea class='formfld' type='text' rows='15' name='item_desc'>$item_desc</textarea>\n";
	echo "<br />\n";
	echo "Enter the description.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	echo "				<input type='hidden' name='invoice_uuid' value='$invoice_uuid'>\n";
	echo "				<input type='hidden' name='contact_uuid' value='$contact_uuid'>\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='invoice_item_uuid' value='$invoice_item_uuid'>\n";
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

//include the footer
	require_once "includes/footer.php";
?>