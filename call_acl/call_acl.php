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
	Igor Olhovskiy <igorolhovskiy@gmail.com>

	Call ACL is written on Call Block base by Gerrit Visser <gerrit308@gmail.com>
*/
require_once "root.php";
require_once "resources/require.php";

//check permissions
	require_once "resources/check_auth.php";
	if (permission_exists('call_acl_view')) {
		//access granted
	} else {
		echo "access denied";
		exit;
	}

//add multi-lingual support
	$language = new text;
	$text = $language->get();

//additional includes
	require_once "resources/header.php";
	require_once "resources/paging.php";

//get variables used to control the order
	$order_by = $_GET["order_by"];
	$order = $_GET["order"];

//show the content
	echo "<table width='100%' cellpadding='0' cellspacing='0' border='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='50%' align='left' nowrap='nowrap'><b>".$text['title-call_acl']."</b></td>\n";
	echo "		<td width='50%' align='right'>&nbsp;</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td align='left' colspan='2'>\n";
	echo "			".$text['description-call_acl']."<br /><br />\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";

//prepare to page the results
	$sql = "SELECT count(*) AS num_rows FROM v_call_acl";
	$sql .= " WHERE domain_uuid = '".$_SESSION['domain_uuid']."' ";
	if (strlen($order_by)> 0) { 
		$sql .= "ORDER BY $order_by $order "; 
	}
	$prep_statement = $db->prepare($sql);

	if ($prep_statement) {
		$prep_statement->execute();
		$row = $prep_statement->fetch(PDO::FETCH_ASSOC);
		$num_rows = $row['num_rows'] > 0 ? $row['num_rows'] : "0";
	}

//prepare to page the results
	$rows_per_page = ($_SESSION['domain']['paging']['numeric'] != '') ? $_SESSION['domain']['paging']['numeric'] : 50;
	$param = "";
	$page = $_GET['page'];
	if (strlen($page) == 0) { $page = 0; $_GET['page'] = 0; }
	list($paging_controls, $rows_per_page, $var3) = paging($num_rows, $param, $rows_per_page);
	$offset = $rows_per_page * $page;

//get the  list
	$sql = "SELECT * FROM v_call_acl";
	$sql .= " WHERE domain_uuid = '".$_SESSION['domain_uuid']."' ";
	if (strlen($order_by)> 0) { 
		$sql .= "ORDER BY $order_by $order ";
	} else {
		$sql .= "ORDER BY call_acl_order ASC "; 
	}
	$sql .= " LIMIT $rows_per_page OFFSET $offset ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll();
	$result_count = count($result);
	unset ($prep_statement, $sql);

//table headers
	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";
	echo "<table class='tr_hover' width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo th_order_by('call_acl_order', $text['label-call_acl_order'], $order_by, $order);
	echo th_order_by('call_acl_name', $text['label-call_acl_name'], $order_by, $order);
	echo th_order_by('call_acl_source', $text['label-call_acl_source'], $order_by, $order);
	echo th_order_by('call_acl_destination', $text['label-call_acl_destination'], $order_by, $order);
	echo th_order_by('call_acl_action', $text['label-call_acl_action'], $order_by, $order);
	echo th_order_by('call_acl_enabled', $text['label-call_acl_enabled'], $order_by, $order);
	echo "<td class='list_control_icons'>";
	if (permission_exists('call_acl_add')) {
		echo "<a href='call_acl_edit.php' alt='".$text['button-add']."'>$v_link_label_add</a>";
	}
	echo "</td>\n";
	echo "</tr>\n";

//show the results
	if ($result_count > 0) {
		foreach($result as $row) {
			$tr_link = (permission_exists('call_acl_edit')) ? "href='call_acl_edit.php?id=".$row['call_acl_uuid']."'" : null;
			echo "<tr ".$tr_link.">\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>";
			if (permission_exists('call_acl_edit')) {
				echo "<a href='call_acl_edit.php?id=".escape($row['call_acl_uuid'])."'>".escape($row['call_acl_order'])."</a>";
			} else {
				echo escape($row['call_acl_order']);
			}
			echo "	</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".escape($row['call_acl_name'])."</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".escape($row['call_acl_source'])."</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".escape($row['call_acl_destination'])."</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>". (($row['call_acl_action'] == "reject") ? $text['label-reject'] : $text['label-allow']) ."</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$text['label-'.escape($row['call_acl_enabled'])]."</td>\n";
			echo "	<td class='list_control_icons'>";
			if (permission_exists('call_acl_edit')) {
				echo "<a href='call_acl_edit.php?id=".escape($row['call_acl_uuid'])."' alt='".$text['button-edit']."'>$v_link_label_edit</a>";
			}
			if (permission_exists('call_acl_delete')) {
				echo "<a href='call_acl_delete.php?id=".escape($row['call_acl_uuid'])."' alt='".$text['button-delete']."' onclick=\"return confirm('".$text['confirm-delete']."')\">$v_link_label_delete</a>";
			};
			echo "  </td>";
			echo "</tr>\n";
			$c = 1 - $c;  // Switch $c = 0/1/0...
		} //end foreach
		unset($sql, $result, $row_count);
	} //end if results

//complete the content
	echo "<tr>\n";
	echo "<td colspan='11' align='left'>\n";
	echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap>$paging_controls</td>\n";
	echo "		<td class='list_control_icons'>";
	if (permission_exists('call_acl_add')) {
		echo "<a href='call_acl_edit.php' alt='".$text['button-add']."'>$v_link_label_add</a>";
	}
	echo "		</td>\n";
	echo "	</tr>\n";
 	echo "	</table>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>";
	echo "<br /><br />";

//include the footer
	require_once "resources/footer.php";

?>
