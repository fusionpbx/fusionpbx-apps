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
if (permission_exists('contacts_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

require_once "includes/header.php";
require_once "includes/paging.php";

//get the http values and set them as php variables
	$orderby = $_GET["orderby"];
	$order = $_GET["order"];

//show the content
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"center\">\n";
	echo "		<br>";

	echo "<table width='100%' border='0'><tr>\n";
	echo "<td width='50%' nowrap='nowrap' align='left'><b>Contact List</b></td>\n";
	echo "<td width='50%' align='right'>&nbsp;</td>\n";
	echo "</tr></table>\n";

	$sql = "";
	$sql .= "select * from v_users ";
	$sql .= "where v_id = '$v_id' ";
	if (strlen($orderby)> 0) { $sql .= "order by $orderby $order "; }
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$numrows = count($result);
	unset ($prepstatement, $result, $sql);

	$rowsperpage = 150;
	$param = "";
	$page = $_GET['page'];
	if (strlen($page) == 0) { $page = 0; $_GET['page'] = 0; } 
	list($pagingcontrols, $rowsperpage, $var3) = paging($numrows, $param, $rowsperpage); 
	$offset = $rowsperpage * $page; 

	$sql = "";
	$sql .= "select * from v_users ";
	if (strlen($orderby)> 0) { $sql .= "order by $orderby $order "; }
	$sql .= "where v_id = '$v_id' ";
	$sql .= "limit $rowsperpage offset $offset ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$resultcount = count($result);
	unset ($prepstatement, $sql);

	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";

	echo "<div align='center'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";

	echo "<tr>\n";
	echo thorderby('username', 'Username', $orderby, $order);
	echo thorderby('user_type', 'Type', $orderby, $order);
	echo thorderby('user_category', 'Category', $orderby, $order);
	echo thorderby('user_first_name', 'First Name', $orderby, $order);
	echo thorderby('user_last_name', 'Last Name', $orderby, $order);
	echo thorderby('user_company_name', 'Organization', $orderby, $order);
	echo thorderby('user_phone_1', 'Phone', $orderby, $order);
	echo "<td align='right' width='42'>\n";
	if (permission_exists('contacts_add')) {
		echo "	<a href='users_edit.php' alt='add'>$v_link_label_add</a>\n";
	}
	echo "</td>\n";
	echo "<tr>\n";

	if ($resultcount == 0) {
		//no results
	}
	else { //received results
		foreach($result as $row) {
			echo "<tr >\n";
			if (strlen($row[username]) == 0) {
				echo "	<td valign='top' class='".$rowstyle[$c]."'>contact&nbsp;</td>\n";
			}
			else {
				echo "	<td valign='top' class='".$rowstyle[$c]."'>".$row[username]."&nbsp;</td>\n";
			}
			echo "	<td valign='top' class='".$rowstyle[$c]."'>".$row[user_type]."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."'>".$row[user_category]."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."'>".$row[user_first_name]."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."'>".$row[user_last_name]."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."'>".$row[user_company_name]."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."'>".$row[user_phone_1]."&nbsp;</td>\n";
			echo "	<td valign='top' align='right'>\n";
			if (permission_exists('contacts_edit')) {
				echo "		<a href='users_edit.php?id=".$row[id]."' alt='edit'>$v_link_label_edit</a>\n";
			}
			if (permission_exists('contacts_delete')) {
				echo "		<a href='users_delete.php?id=".$row[id]."' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\">$v_link_label_delete</a>\n";
			}
			echo "	</td>\n";
			echo "</tr>\n";
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $rowcount);
	} //end if results

	echo "<tr>\n";
	echo "<td colspan='8' align='left'>\n";
	echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap>$pagingcontrols</td>\n";
	echo "		<td width='33.3%' align='right'>\n";
	if (permission_exists('contacts_add')) {
		echo "			<a href='users_edit.php' alt='add'>$v_link_label_add</a>\n";
	}
	echo "		</td>\n";
	echo "	</tr>\n";
 	echo "	</table>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>";
	echo "</div>";
	echo "<br><br>";
	echo "<br><br>";

	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	echo "<br><br>";

//show the footer
	require_once "includes/footer.php";
?>
