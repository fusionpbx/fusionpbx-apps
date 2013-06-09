<?php
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

if (count($_GET)>0) {
	$id = check_str($_GET["id"]);
	$contact_uuid = check_str($_GET["contact_uuid"]);
}

if (strlen($id)>0) {
	$sql = "";
	$sql .= "delete from v_invoices ";
	$sql .= "where invoice_uuid = '$id' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	unset($sql);
}

require_once "includes/header.php";
echo "<meta http-equiv=\"refresh\" content=\"2;url=v_invoices.php?id=$contact_uuid\">\n";
echo "<div align='center'>\n";
echo "Delete Complete\n";
echo "</div>\n";

require_once "includes/footer.php";
return;

?>