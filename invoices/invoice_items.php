<?php
/* $Id$ */
/*
	Copyright (C) 2008-2013 Mark J Crane
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice,
	   this list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGE.
*/
require_once "root.php";
require_once "resources/require.php";
require_once "resources/check_auth.php";
if (permission_exists('invoice_item_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//get variables used to control the order
	$order_by = $_GET["order_by"];
	$order = $_GET["order"];

//show the content
	echo "<table width='100%' cellpadding='0' cellspacing='0' border='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='50%' align='left' nowrap='nowrap'><b>".$text['title-invoice_items']."</b><br><br></td>\n";
	echo "		<td width='50%' align='right'>&nbsp;</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";

	//prepare to page the results
		$sql = "select count(*) as num_rows from v_invoice_items ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= " and invoice_uuid = '$invoice_uuid' ";
		if (strlen($order_by)> 0) { $sql .= "order by $order_by $order "; }
		$prep_statement = $db->prepare($sql);
		if ($prep_statement) {
			$prep_statement->execute();
			$row = $prep_statement->fetch(PDO::FETCH_ASSOC);
			if ($row['num_rows'] > 0) {
				$num_rows = $row['num_rows'];
			}
			else {
				$num_rows = '0';
			}
		}

	//prepare to page the results
		$rows_per_page = 10000;
		$param = "";
		$page = $_GET['page'];
		if (strlen($page) == 0) { $page = 0; $_GET['page'] = 0; }
		//list($paging_controls, $rows_per_page, $var_3) = paging($num_rows, $param, $rows_per_page);
		$offset = $rows_per_page * $page;

	//get the list
		$sql = "select * from v_invoice_items ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= " and invoice_uuid = '$invoice_uuid' ";
		if (strlen($order_by)> 0) { $sql .= "order by $order_by $order "; }
		$sql .= "limit $rows_per_page offset $offset ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
		$result_count = count($result);
		unset ($prep_statement, $sql);

	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";

	echo "<table class='tr_hover' width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo th_order_by('item_qty', $text['label-item_qty'], $order_by, $order);
	echo th_order_by('item_unit_price', $text['label-item_unit_price'], $order_by, $order);
	echo th_order_by('item_desc', $text['label-item_desc'], $order_by, $order);
	echo "<th nowrap='nowrap'>".$text['label-item_amount']."</th>\n";
	echo "<td align='right' width='42'>\n";
	$back = ($back != '') ? "&back=".$back : null;
	if (permission_exists('invoice_item_add')) {
		echo "	<a href='invoice_item_edit.php?invoice_uuid=".$_GET['id']."&contact_uuid=".$contact_uuid_to.$back."' alt='".$text['button-add']."'>$v_link_label_add</a>\n";
	}
	else {
		echo "	&nbsp;\n";
	}
	echo "</td>\n";
	echo "<tr>\n";

	if ($result_count > 0) {
		foreach($result as $row) {
			$item_desc = $row['item_desc'];
			$item_desc = str_replace("\n", "<br />", $item_desc);
			$tr_link = (permission_exists('invoice_item_edit')) ? "href='invoice_item_edit.php?invoice_uuid=".$row['invoice_uuid']."&id=".$row['invoice_item_uuid']."&contact_uuid=".$contact_uuid_to.$back."'" : null;
			echo "<tr ".$tr_link.">\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row['item_qty']."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".number_format($row['item_unit_price'], 2)."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$item_desc."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".number_format(($row['item_qty'] * $row['item_unit_price']), 2)."&nbsp;</td>\n";
			echo "	<td class='list_control_icons'>\n";
			if (permission_exists('invoice_item_edit')) {
				echo 	"<a href='invoice_item_edit.php?invoice_uuid=".$row['invoice_uuid']."&id=".$row['invoice_item_uuid']."&contact_uuid=".$contact_uuid_to.$back."' alt='".$text['button-edit']."'>$v_link_label_edit</a>";
			}
			if (permission_exists('invoice_item_delete')) {
				echo 	"<a href='invoice_item_delete.php?invoice_uuid=".$row['invoice_uuid']."&id=".$row['invoice_item_uuid']."&contact_uuid=".$contact_uuid_to.$back."' alt='".$text['button-delete']."' onclick=\"return confirm('".$text['confirm-delete']."')\">$v_link_label_delete</a>";
			}
			echo "	</td>\n";
			echo "</tr>\n";
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $row_count);
	} //end if results

	echo "<tr>\n";
	echo "<td colspan='6' align='left'>\n";
	echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap='nowrap'>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap='nowrap'>$paging_controls</td>\n";
	echo "		<td width='33.3%' align='right'>\n";
	if (permission_exists('invoice_item_add')) {
		echo "			<a href='invoice_item_edit.php?invoice_uuid=".$_GET['id']."&contact_uuid=".$contact_uuid_to.$back."' alt='".$text['button-add']."'>$v_link_label_add</a>\n";
	}
	else {
		echo "			&nbsp;\n";
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