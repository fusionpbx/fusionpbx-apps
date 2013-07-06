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
include "root.php";
require_once "includes/require.php";
require_once "resources/check_auth.php";
if (permission_exists('cdr_csv_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

require_once "includes/lib_cdr.php";

if (count($_REQUEST)>0) {
	$cdr_id = $_REQUEST["cdr_id"];
	$caller_id_name = $_REQUEST["caller_id_name"];
	$caller_id_number = $_REQUEST["caller_id_number"];
	$destination_number = $_REQUEST["destination_number"];
	$context = $_REQUEST["context"];
	$start_stamp = $_REQUEST["start_stamp"];
	$answer_stamp = $_REQUEST["answer_stamp"];
	$end_stamp = $_REQUEST["end_stamp"];
	$duration = $_REQUEST["duration"];
	$billsec = $_REQUEST["billsec"];
	$hangup_cause = $_REQUEST["hangup_cause"];
	$uuid = $_REQUEST["uuid"];
	$bleg_uuid = $_REQUEST["bleg_uuid"];
	$accountcode = $_REQUEST["accountcode"];
	$read_codec = $_REQUEST["read_codec"];
	$write_codec = $_REQUEST["write_codec"];
	$remote_media_ip = $_REQUEST["remote_media_ip"];
	$network_addr = $_REQUEST["network_addr"];
}

//get a list of assigned extensions for this user
	$sql = "";
	$sql .= " select * from v_extensions ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and user_list like '%|".$_SESSION["username"]."|%' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	//$v_mailboxes = '';
	$x = 0;
	$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
	foreach ($result as &$row) {
		//$v_mailboxes = $v_mailboxes.$row["mailbox"].'|';
		//$extension_uuid = $row["extension_uuid"];
		//$mailbox = $row["mailbox"];
		$extension_array[$x]['extension_uuid'] = $row["extension_uuid"];
		$extension_array[$x]['extension'] = $row["extension"];
		$x++;
	}
	unset ($prep_statement, $x);


if (if_group("admin") || if_group("superadmin")) {
	$sql_where = "where ";
}
if (strlen($domain_uuid) > 0) { $sql_where .= "and domain_uuid like '$domain_uuid' "; }
if (strlen($cdr_id) > 0) { $sql_where .= "and cdr_id like '%$cdr_id%' "; }
if (strlen($caller_id_name) > 0) { $sql_where .= "and caller_id_name like '%$caller_id_name%' "; }
if (strlen($caller_id_number) > 0) { $sql_where .= "and caller_id_number like '%$caller_id_number%' "; }
if (strlen($destination_number) > 0) { $sql_where .= "and destination_number like '%$destination_number%' "; }
if (strlen($context) > 0) { $sql_where .= "and context like '%$context%' "; }
if (strlen($start_stamp) > 0) { $sql_where .= "and start_stamp like '%$start_stamp%' "; }
if (strlen($answer_stamp) > 0) { $sql_where .= "and answer_stamp like '%$answer_stamp%' "; }
if (strlen($end_stamp) > 0) { $sql_where .= "and end_stamp like '%$end_stamp%' "; }
if (strlen($duration) > 0) { $sql_where .= "and duration like '%$duration%' "; }
if (strlen($billsec) > 0) { $sql_where .= "and billsec like '%$billsec%' "; }
if (strlen($hangup_cause) > 0) { $sql_where .= "and hangup_cause like '%$hangup_cause%' "; }
if (strlen($uuid) > 0) { $sql_where .= "and uuid like '%$uuid%' "; }
if (strlen($bleg_uuid) > 0) { $sql_where .= "and bleg_uuid like '%$bleg_uuid%' "; }
if (strlen($accountcode) > 0) { $sql_where .= "and accountcode like '%$accountcode%' "; }
if (strlen($read_codec) > 0) { $sql_where .= "and read_codec like '%$read_codec%' "; }
if (strlen($write_codec) > 0) { $sql_where .= "and write_codec like '%$write_codec%' "; }
if (strlen($remote_media_ip) > 0) { $sql_where .= "and remote_media_ip like '%$remote_media_ip%' "; }
if (strlen($network_addr) > 0) { $sql_where .= "and network_addr like '%$network_addr%' "; }
if (!if_group("admin") && !if_group("superadmin")) {
	if (trim($sql_where) == "where") { $sql_where = ""; }
	//disable member search
	//$sql_where_orig = $sql_where;
	$sql_where = "where ";
	if (count($_SESSION['user']['extension']) > 0) {
		foreach ($_SESSION['user']['extension'] as &$row) {
			if ($row['user'] > 0) { $sql_where .= "or caller_id_number = '".$row['user']."' ". $sql_where_orig; } //source
			if ($row['user'] > 0) { $sql_where .= "or destination_number = '".$row['user']."' ".$sql_where_orig; } //destination
			if ($row['user'] > 0) { $sql_where .= "or destination_number = '*99".$row['user']."' ".$sql_where_orig; } //destination
		}
	}
}
$sql_where = str_replace ("where or", "where", $sql_where);
$sql_where = str_replace ("where and", "where", $sql_where);

$sql = "";
$sql .= "select * from v_cdr ";
$sql .= $sql_where;
if (strlen($order_by)> 0) { $sql .= "order by $order_by $order "; }
$prep_statement = $db->prepare(check_sql($sql));
$prep_statement->execute();
$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
$result_count = count($result);


header('Content-type: application/octet-binary');
header('Content-Disposition: attachment; filename=cdr.csv');

$z = 0;
foreach($result[0] as $key => $val) {

    if ($z == 0) {
        echo '"'.$key.'"';
    }
    else {
        echo ',"'.$key.'"';
    }
    $z++;
}
echo "\n";


$x=0;
while(true) {

    $z = 0;
    foreach($result[0] as $key => $val) {
        if ($z == 0) {
            echo '"'.$result[$x][$key].'"';
        }
        else {
            echo ',"'.$result[$x][$key].'"';
        }
        $z++;
    }
    echo "\n";

    ++$x;
    if ($x > ($result_count-1)) {
        break;
    }
    //$row++;
}

unset ($result_count);
unset ($resulttype);
unset ($result);
unset ($key);
unset ($val);
unset ($msg);
unset ($errormsg);
unset ($sql);
unset ($x);
unset ($z);
?>
