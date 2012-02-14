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
	Ken Rice <krice@tollfreegateway.com>
	Mark J Crane <markjcrane@fusionpbx.com>
*/

include "root.php";
require_once "includes/require.php";
require_once "includes/checkauth.php";

if (permission_exists('ticket_view') || permission_exists('ticket_update')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

require_once "includes/header.php";

$v_domain = $_SESSION['domains'][$domain_uuid]['domain'];

// Check to see if we're an admin and if we are set the $isadmin to true for use in the template and sql query building
if (if_group("superadmin") || if_group("admin")){
	$isadmin = true;
}

//add or update the database
if (isset($_REQUEST['id']) || isset($_REQUEST['uuid'])) {
	$action = "update";
	if (isset($_REQUEST["id"])) { 
		$ticket_id = check_str($_REQUEST["id"]);
	}
	if (isset($_REQUEST["uuid"])) { 
		$ticket_uuid = check_str($_REQUEST["uuid"]);
	}
} else {  
	$action = "add";
	//Redirect back outta here probably
}

if ($action == "update") {

	//get the ticket
	$sql = "";
	$sql .= "select a.ticket_id, a.queue_id, a.domain_uuid, a.user_uuid, a.customer_id, a.subject, ";
	$sql .= "to_char(a.create_stamp, 'MM-DD-YY HH24-MI-SS') as create_stamp, a.create_user_id, ";
	$sql .= "a.ticket_status, to_char(a.last_update_stamp, 'MM-DD-YY HH24-MI-SS') as last_update_stamp, ";
	$sql .= "a.last_update_user_uuid, a.ticket_uuid, a.ticket_number, a.ticket_owner, a.customer_ticket_number, ";
	$sql .= "b.username, c.username as create_username, d.username as last_update_username ";
	$sql .= "from v_tickets as a, v_users as b, v_users as c, v_users as d ";
	$sql .= "where a.user_uuid = b.id and a.create_user_id = c.id and a.last_update_user_uuid = d.id ";
	$sql .= "and a.domain_uuid = '$domain_uuid' ";
	if (isset($_REQUEST["id"])) { 
		$sql .= "and a.ticket_id = '$ticket_id' ";
	}
	if (isset($_REQUEST["uuid"])) { 
		$sql .= "and a.ticket_uuid = '$ticket_uuid' ";
	}
	if (!$isadmin) {
		$sql .= "and a.user_uuid = " . $_SESSION['user_uuid'] . " ";
	}

	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$x = 0;
	$result = $prep_statement->fetchAll();
	foreach ($result as &$row) {
		$ticket_header = $row;
		$x++;
		break;
	}
	unset ($prep_statement);

	if ($x < 1) {
		include "bad_ticket_id.php";
		goto end;
	}
	
	$sql = "";
	$sql .= "SELECT * from v_ticket_notes ";
	$sql .= "where ticket_id = " . $ticket_header['ticket_id'] . " ";
	$sql .= "order by create_stamp ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$x = 0;
	$result = $prep_statement->fetchAll();
	foreach ($result as &$row) {
		$ticket_notes[$x] = $row;
		$x++;
	}
	unset ($prep_statement);

	$sql = "";
	$sql .= "select a.*, c.username from v_ticket_queue_members as a, v_users as c ";
	$sql .= "where a.user_uuid = c.id ";
	$sql .= "and a.queue_id = " . $ticket_header['queue_id'] . " ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$x = 0;
	$result = $prep_statement->fetchAll();
	foreach ($result as &$row) {
		$queue_members[$x] = $row;
		$x++;
	}
	unset ($prep_statement);

	$sql = "";
	$sql .= "SELECT * from v_ticket_statuses ";
	$sql .= "where domain_uuid = $domain_uuid ";
	$sql .= "ORDER by status_id ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$x = 0;
	$result = $prep_statement->fetchAll();
	foreach ($result as &$row) {
		$ticket_statuses[$x] = $row;
		$x++;
	}
	unset ($prep_statement);

	$sql = "";
	$sql .= "SELECT * from v_ticket_queues ";
	$sql .= "where domain_uuid = $domain_uuid ";
	$sql .= "ORDER by queue_name ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$x = 0;
	$result = $prep_statement->fetchAll();
	foreach ($result as &$row) {
		$ticket_queues[$x] = $row;
		$x++;
	}
	unset ($prep_statement);
} 

if ((!isset($_REQUEST['submit'])) || ($_REQUEST['submit'] != 'Save')) {
	// If we arent saving a Profile Display the form.
	include "ticket_update.php";	
	goto end;
}

foreach ($_REQUEST as $field => $data){
	$request[$field] = check_str($data);
}

if ($action == "update" && permission_exists('ticket_update')) {
	if (strlen($request['new_note']) > 0) {
		$sql = "";
		$sql .= "INSERT into v_ticket_notes (";
		$sql .= "ticket_id, ";
		$sql .= "create_stamp, ";
		$sql .= "create_user_id, ";
		$sql .= "ticket_note ";
		$sql .= ") values ( ";
		$sql .= $ticket_header['ticket_id'] . ", ";
		$sql .= "now(), ";
		$sql .= $_SESSION['user_uuid'] . ", ";
		$sql .= "'" . base64_encode($request['new_note']) . "' ";
		$sql .= ")";
		$db->exec(check_sql($sql));
		$note_added = true;
	}

	$sql = "";
	$sql .= "UPDATE v_tickets set ";
	if ($ticket_header['ticket_owner'] != $request['ticket_owner']) {
		$sql .= "ticket_owner = " . $request['ticket_owner'] . ", ";
		if ($_SESSION['user_uuid'] != $request['ticket_owner']) {
			$alert_new_owner = true;
		}
	}
	if ($ticket_header['ticket_status'] != $request['ticket_status']) {
		$sql .= "ticket_status = " . $request['ticket_status'] . ", ";
	}
	if ($ticket_header['queue_id'] != $request['queue_id']) {
		$sql .= "queue_id = " . $request['queue_id'] . ", ";
	}
	$sql .= "last_update_user_uuid = " . $_SESSION['user_uuid'] . ", ";
	$sql .= "last_update_stamp = now() ";
	$sql .= "where ticket_id = " . $ticket_header['ticket_id'] . " ";
	$db->exec(check_sql($sql));

	if ($note_added && $request['alert_user']) {
		$sql = "select user_email from v_users where id = . " . $ticket_header['user_uuid'];
	        $prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$x = 0;
		$result = $prep_statement->fetchAll();
		foreach ($result as &$row) {
			$user_email = $row['user_email'];
			break;
		}
		unset ($prep_statement);

		if (strlen($user_email) > 1) {
			$subject = sprintf("[%s] Ticket %s Updated", $queue['queue_name'], $ticket_header['ticket_number']);
			$to = $user_email;
			$message = "";
			$message .= "Ticket Number $ticketnumber has been update\n";
			$message .= "Ticket Link: http://" . $_SESSION['v_domain'] . PROJECT_PATH . "/mod/tickets/v_ticket_update.php?uuid=" . urlencode($ticket_uuid). "\n";
			$message .= "Ticket update: \n";
			$message .= $request['new_notes'] . "\n";
			$from = "From: " . $_SESSION['support_email'];
			mail($to, $subject, $message, $from);
		}
	} 
	
	if ($alert_new_owner) {
		$sql = "select user_email from v_users where id = . " . $request['ticket_owner'];
	        $prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$x = 0;
		$result = $prep_statement->fetchAll();
		foreach ($result as &$row) {
			$user_email = $row['user_email'];
			break;
		}
		unset ($prep_statement);

		if (strlen($user_email) > 1) {
			$subject = sprintf("[%s] Ticket %s Updated", $queue['queue_name'], $ticket_header['ticket_number']);
			$to = $user_email;
			$message = "";
			$message .= "Ticket Number $ticketnumber has been update\n";
			$message .= "Ticket Link: http://" . $_SESSION['v_domain'] . PROJECT_PATH . "/mod/tickets/v_ticket_update.php?uuid=" . urlencode($ticket_uuid). "\n";
			$from = "From: " . $_SESSION['support_email'];
			mail($to, $subject, $message, $from);
		}
		
	}
	
	goto writeout;
} 

writeout:
include "update_complete.php";

end:
//show the footer
require_once "includes/footer.php";

?>
