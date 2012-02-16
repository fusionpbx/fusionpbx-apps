<?php
require_once "root.php";
require_once "includes/require.php";

if (count($_GET)>0) {
	$id = check_str($_GET["id"]);
	$invoice_uuid = check_str($_GET["invoice_uuid"]);
	$contact_uuid = check_str($_GET["contact_uuid"]);
}

if (strlen($id)>0) {
	$sql = "";
	$sql .= "delete from v_invoice_items ";
	$sql .= "where invoice_item_uuid = '$id' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	unset($sql);
}

require_once "includes/header.php";
echo "<meta http-equiv=\"refresh\" content=\"2;url=v_invoices_edit.php?id=$invoice_uuid&contact_uuid=$contact_uuid\">\n";
echo "<div align='center'>\n";
echo "Delete Complete\n";
echo "</div>\n";

require_once "includes/footer.php";
return;

?>

