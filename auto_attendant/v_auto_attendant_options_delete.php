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
if (ifgroup("admin") || ifgroup("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

if (count($_GET)>0) {
    $id = $_GET["id"];
    $auto_attendant_id = check_str($_REQUEST["id2"]);
}

if (strlen($id)>0) {
    $sql = "";
    $sql .= "delete from v_auto_attendant_options ";
	$sql .= "where v_id = '$v_id' ";
    $sql .= "and auto_attendant_option_id = '$id' ";
    $sql .= "and auto_attendant_id = '$auto_attendant_id' ";
    $prepstatement = $db->prepare(check_sql($sql));
    $prepstatement->execute();
    unset($sql);
	
    //synchronize the xml config
    sync_package_v_auto_attendant();
}

require_once "includes/header.php";
echo "<meta http-equiv=\"refresh\" content=\"2;url=v_auto_attendant_edit.php?id=".$auto_attendant_id."\">\n";
echo "<div align='center'>\n";
echo "Delete Complete\n";
echo "</div>\n";

require_once "includes/footer.php";
return;

?>