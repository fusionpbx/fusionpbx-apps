<?php
require_once "root.php";
require_once "includes/config.php";
require_once "includes/header.php";
require_once "includes/paging.php";

//get variables used to control the order
	$order_by = $_GET["order_by"];
	$order = $_GET["order"];

//show the content
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr class='border'>\n";
	echo "	<td align=\"center\">\n";
	echo "		<br>";

	echo "<table width='100%' border='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='50%' align=\"left\" nowrap=\"nowrap\"><b>Itemized List</b></td>\n";
	echo "		<td width='50%' align=\"right\">&nbsp;</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";

	//prepare to page the results
		$sql = "";
		$sql .= " select count(*) as num_rows from v_invoice_items ";
		$sql .= " where domain_uuid = '$domain_uuid' ";
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
		$rows_per_page = 10;
		$param = "";
		$page = $_GET['page'];
		if (strlen($page) == 0) { $page = 0; $_GET['page'] = 0; } 
		list($paging_controls, $rows_per_page, $var_3) = paging($num_rows, $param, $rows_per_page); 
		$offset = $rows_per_page * $page; 

	//get the invoice list
		$sql = "";
		$sql .= " select * from v_invoice_items ";
		$sql .= " where domain_uuid = '$domain_uuid' ";
		$sql .= " and invoice_uuid = '$invoice_uuid' ";
		if (strlen($order_by)> 0) { $sql .= "order by $order_by $order "; }
		$sql .= " limit $rows_per_page offset $offset ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll();
		$result_count = count($result);
		unset ($prep_statement, $sql);

	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";

	echo "<div align='center'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo thorder_by('item_qty', 'Quantity', $order_by, $order);
	echo thorder_by('item_unit_price', 'Unit Price', $order_by, $order);
	echo thorder_by('item_desc', 'Description', $order_by, $order);
	echo "<th nowrap='nowrap'>Amount</th>\n";
	echo "<td align='right' width='42'>\n";
	echo "	<a href='v_invoice_items_edit.php?invoice_uuid=".$_GET['id']."&contact_uuid=".$contact_uuid."' alt='add'>$v_link_label_add</a>\n";
	echo "</td>\n";
	echo "<tr>\n";

	if ($result_count > 0) {
		foreach($result as $row) {
			$item_desc = $row['item_desc'];
			$item_desc = str_replace("\n", "<br />", $item_desc);
			echo "<tr >\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row['item_qty']."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".number_format($row['item_unit_price'], 2)."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$item_desc."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".number_format(($row['item_qty'] * $row['item_unit_price']), 2)."&nbsp;</td>\n";
			echo "	<td valign='top' align='right'>\n";
			echo "		<a href='v_invoice_items_edit.php?invoice_uuid=".$row['invoice_uuid']."&id=".$row['invoice_item_uuid']."&contact_uuid=".$contact_uuid."' alt='edit'>$v_link_label_edit</a>\n";
			echo "		<a href='v_invoice_items_delete.php?invoice_uuid=".$row['invoice_uuid']."&id=".$row['invoice_item_uuid']."&contact_uuid=".$contact_uuid."' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\">$v_link_label_delete</a>\n";
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
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap>$paging_controls</td>\n";
	echo "		<td width='33.3%' align='right'>\n";
	echo "			<a href='v_invoice_items_edit.php?invoice_uuid=".$_GET['id']."&contact_uuid=".$contact_uuid."' alt='add'>$v_link_label_add</a>\n";
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

//include the footer
	require_once "includes/footer.php";
?>
