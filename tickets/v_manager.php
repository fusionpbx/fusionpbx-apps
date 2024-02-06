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
	Portions created by the Initial Developer are Copyright (C) 2008-2012
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Ken Rice     <krice@tollfreegateway.com>
	Mark J Crane <markjcrane@fusionpbx.com>
*/
include "root.php";
require_once "resources/require.php";
require_once "resources/check_auth.php";
if (permission_exists('xmpp_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}
require_once "resources/header.php";
require_once "resources/paging.php";

if (isset($_REQUEST)) {
	foreach ($_REQUEST as $field => $data){
        	$request[$field] = check_str($data);
	}
}

$sql = "";
if (isset($_REQUEST['queue_name'])) {
	if (isset($_REQUEST['queue_id'])){
		//do Queue Update
		$sql .= "UPDATE v_ticket_queues SET ";
		$sql .= "queue_name = '" . $request['queue_name'] . "', ";
		$sql .= "queue_email = '" . $request['queue_email'] . "', ";
		$sql .= "WHERE queue_id = " . $request['queue_id'] . " ";
	} else {
		//do Queue Create
		$sql .= "INSERT into v_ticket_queues (queue_name, queue_email, domain_uuid) values ";
		$sql .= "('" . $request['queue_name'] . "', '" . $request['queue_email'] . "', $domain_uuid) ";
	}
	$db->exec($sql);
}

if (isset($_REQUEST['status_name'])) {
	if (isset($_REQUEST['status_id'])){
		//do Status Update
		$sql .= "UPDATE v_ticket_statuses SET ";
		$sql .= "status_name = '" . $request['status_name'] . "' ";
		$sql .= "WHERE status_id = " . $request['status_id'] . " ";
	} else {
		//do Status Create
		$sql .= "INSERT into v_ticket_statuses (status_name, domain_uuid) values ";
		$sql .= "('" . $request['status_name'] . "', $domain_uuid) ";
	}
	$db->exec($sql);
}

// Get a List of the Ticket Statuses
$sql = "";
$sql .= "select * from v_ticket_statuses ";
$sql .= "where domain_uuid = $domain_uuid ";
$sql .= "order by status_id ";
$prep_statement = $db->prepare(check_sql($sql));
$prep_statement->execute();
$x = 0;
$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
foreach ($result as &$row) {
	$statuses[$row['status_id']] = $row;
}
unset ($prep_statement);

$sql = "";
$sql .= "select * from v_ticket_queues ";
$sql .= "where domain_uuid = $domain_uuid ";
$sql .= "order by queue_id ";
$prep_statement = $db->prepare(check_sql($sql));
$prep_statement->execute();
$x = 0;
$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
foreach ($result as &$row) {
	$queues[$row['queue_id']] = $row;
}
unset ($prep_statement);

//include the view
include "ticket_manager.php";

//include the footer
require_once "resources/footer.php";

?>
