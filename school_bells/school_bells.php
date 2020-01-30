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
	Portions created by the Initial Developer are Copyright (C) 2008-2016
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
	Igor Olhovskiy <igorolhovskiy@gmail.com>
*/
require_once "root.php";
require_once "resources/require.php";
require_once "resources/check_auth.php";

if (permission_exists('school_bell_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//add multi-lingual support
	$language = new text;
	$text = $language->get();

require_once "resources/header.php";
	$document['title'] = $text['title-school_bells'];

require_once "resources/paging.php";

//get variables used to control the order
	$order_by = $_GET["order_by"];
	$order = $_GET["order"];

	//prepare to page the results
	$sql = "SELECT count(school_bell_uuid) AS num_rows FROM v_school_bells";
	$sql .= " WHERE domain_uuid = :domain_uuid";
	$prep_statement = $db->prepare($sql);
	$prep_statement->bindValue('domain_uuid', $domain_uuid);
	if ($prep_statement) {
		$prep_statement->execute();
		$row = $prep_statement->fetch(PDO::FETCH_ASSOC);
		$num_rows = ($row['num_rows'] > 0) ? $row['num_rows'] : '0';
	}

	//prepare to page the results
	$rows_per_page = ($_SESSION['domain']['paging']['numeric'] != '') ? $_SESSION['domain']['paging']['numeric'] : 50;
	$param = "";
	$page = $_GET['page'];
	if (strlen($page) == 0) { 
		$page = 0; 
		$_GET['page'] = 0; 
	}
	list($paging_controls, $rows_per_page, $var3) = paging($num_rows, $param, $rows_per_page);
	$offset = $rows_per_page * $page;

	//get the list
	if ($num_rows > 0) {
		$sql = "SELECT * FROM v_school_bells";
		$sql .= " WHERE domain_uuid = :domain_uuid";
		if (strlen($order_by)> 0) { 
			$sql .= " ORDER BY $order_by $order"; 
		}
		$sql .= " LIMIT $rows_per_page OFFSET $offset";

		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->bindValue('domain_uuid', $domain_uuid);

		$prep_statement->execute();
		$result = $prep_statement->fetchAll();
		$result_count = count($result);
		unset ($prep_statement, $sql);
	}

	//show the content
	echo "<table width='100%' cellpadding='0' cellspacing='0 border='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='50%' align='left' nowrap='nowrap'><b>".$text['header-school_bells']." (".$num_rows.")</b></td>\n";
	echo "		<td width='50%' align='right'>&nbsp;</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td align='left' colspan='2'>\n";
	echo "			".$text['description-school_bells']."<br /><br />\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";

	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";

	echo "<table class='tr_hover' width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo th_order_by('school_bell_name', $text['label-school_bell_name'], $order_by, $order);
	echo th_order_by('school_bell_leg_a_data', $text['label-school_bell_leg_a_data'], $order_by, $order);
	echo th_order_by('school_bell_leg_b_data', $text['label-school_bell_leg_b_data'], $order_by, $order);
	echo "<th>".$text['label-school_bell_schedule_time']."</th>\n";
	echo th_order_by('school_bell_enabled', $text['label-school_bell_enabled'], $order_by, $order);
	echo th_order_by('school_bell_description', $text['label-school_bell_description'], $order_by, $order);
	echo "<td class='list_control_icons'>";
	if (permission_exists('school_bell_add')) {
		echo "<a href='school_bell_edit.php' alt='".$text['button-add']."'>$v_link_label_add</a>";
	}
	echo "</td>\n";
	echo "</tr>\n";

	if ($result_count > 0) {
		foreach($result as $row) {
			$row = array_map('escape', $row);

			$tr_link = (permission_exists('school_bell_edit')) ? "href='school_bell_edit.php?id=".$row['school_bell_uuid']."'" : null;
			echo "<tr ".$tr_link.">\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row['school_bell_name']."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row['school_bell_leg_a_data']."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row['school_bell_leg_b_data']."&nbsp;</td>\n";

			$school_bell_schedule_time = ($row['school_bell_min'] == -1) ? '* ': $row['school_bell_min'] . " ";
			$school_bell_schedule_time .= ($row['school_bell_hour'] == -1) ? '* ': $row['school_bell_hour'] . " ";
			$school_bell_schedule_time .= ($row['school_bell_dom'] == -1) ? '* ': $row['school_bell_dom'] . " ";
			$school_bell_schedule_time .= ($row['school_bell_mon'] == -1) ? '* ': $row['school_bell_mon'] . " ";
			$school_bell_schedule_time .= ($row['school_bell_dow'] == -1) ? '* ': $row['school_bell_dow'] . " ";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$school_bell_schedule_time."&nbsp;</td>\n";

			echo "	<td valign='top' class='".$row_style[$c]."'>".$row['school_bell_enabled']."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row['school_bell_description']."&nbsp;</td>\n";

			echo "	<td class='list_control_icons'>";
			if (permission_exists('school_bell_edit')) {
				echo "<a href='school_bell_edit.php?id=".$row['school_bell_uuid']."' alt='".$text['button-edit']."'>$v_link_label_edit</a>";
			}
			if (permission_exists('school_bell_delete')) {
				echo "<a href='school_bell_delete.php?id=".$row['school_bell_uuid']."' alt='".$text['button-delete']."' onclick=\"return confirm('".$text['confirm-delete']."')\">$v_link_label_delete</a>";
			}
			echo "	</td>\n";
			echo "</tr>\n";
			$c = 1 - $c; // Switch 1/0/1/0...
		} //end foreach
		unset($sql, $result, $row_count);
	} //end if results

	echo "<tr>\n";
	echo "<td colspan='10' align='left'>\n";
	echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap>$paging_controls</td>\n";
	echo "		<td class='list_control_icons'>";
	if (permission_exists('school_bell_add')) {
		echo 		"<a href='school_bell_edit.php' alt='".$text['button-add']."'>$v_link_label_add</a>";
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
