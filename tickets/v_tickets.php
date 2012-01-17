<?php
/* $Id$ */
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
	Ken Rice     <krice@tollfreegateway.com>
	Mark J Crane <markjcrane@fusionpbx.com>
*/
include "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (permission_exists('ticket_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}
require_once "includes/header.php";
require_once "includes/paging.php";

if (isset($_REQUEST['show_closed'])) { 
	$show_closed = true; 
}

//get a list of assigned extensions for this user
$sql = "";
$sql .= "select * from v_tickets ";
$sql .= "where domain_uuid = '$domain_uuid' ";
if (!$show_closed) {
	$sql .= "and ticket_status < 6 ";
}
if (!ifgroup("superadmin") && !ifgroup("admin")){
	$sql .= "and user_id = " . $_SESSION['user_id'] . " ";
}
$sql .= "order by ticket_status, queue_id ";
$prepstatement = $db->prepare(check_sql($sql));
$prepstatement->execute();
$x = 0;
$result = $prepstatement->fetchAll();
foreach ($result as &$row) {
	$tickets[$x] = $row;
	$x++;
}
unset ($prepstatement);

$sql = "";
$sql .= "select * from v_ticket_statuses ";
$prepstatement = $db->prepare(check_sql($sql));
$prepstatement->execute();
$x = 0;
$result = $prepstatement->fetchAll();
foreach ($result as &$row) {
	$statuses[$row['status_id']] = $row['status_name'];
}
unset ($prepstatement);

$sql = "";
$sql .= "select * from v_ticket_queues ";
$sql .= "where domain_uuid = $domain_uuid ";
$prepstatement = $db->prepare(check_sql($sql));
$prepstatement->execute();
$x = 0;
$result = $prepstatement->fetchAll();
foreach ($result as &$row) {
	$queues[$row['queue_id']] = $row['queue_name'];
}
unset ($prepstatement);

//include the view
include "ticket_list.php";

//include the footer
require_once "includes/footer.php";

?>
