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
	KonradSC <konrd@yahoo.com>
*/

//includes
	include "root.php";
	require_once "resources/require.php";
	require_once "resources/check_auth.php";
	require_once "resources/paging.php";	

//include the class
	require_once "resources/check_auth.php";

//check permissions
	require_once "resources/check_auth.php";
	if (permission_exists('domain_counts_view')) {
		//access granted
	}
	else {
		echo "access denied";
		exit;
	}
	
//add multi-lingual support
	$language = new text;
	$text = $language->get();

//get the http values and set them as variables
	$order_by = check_str($_GET["order_by"]);
	$order = check_str($_GET["order"]);

//handle search term
	$search = check_str($_GET["search"]);
	if (strlen($search) > 0) {
		$sql_mod = "WHERE d.domain_name like '%".$search."%' "; 
	}
	if (strlen($order_by) < 1) {
		$order_by = "domain_name";
		$order = "ASC";
	}

//get all the counts from the database
	$sql = "SELECT \n";
	$sql .= "d.domain_uuid, \n";
	$sql .= "d.domain_name, \n";

	//extension
	$sql .= "(\n";
	$sql .= "select count(*) from v_extensions \n";
	$sql .= "where domain_uuid = d.domain_uuid\n";
	$sql .= ") as extension_count, \n";
	
	//users
	$sql .= "(\n";
	$sql .= "select count(*) from v_users \n";
	$sql .= "where domain_uuid = d.domain_uuid\n";
	$sql .= ") as user_count, \n";	

	//devices
	$sql .= "(\n";
	$sql .= "select count(*) from v_devices \n";
	$sql .= "where domain_uuid = d.domain_uuid\n";
	$sql .= ") as device_count, \n";

	//destinations
	$sql .= "(\n";
	$sql .= "select count(*) from v_destinations \n";
	$sql .= "where domain_uuid = d.domain_uuid\n";
	$sql .= ") as destination_count, \n";
	
	//faxes
	$sql .= "(\n";
	$sql .= "select count(*) from v_fax \n";
	$sql .= "where domain_uuid = d.domain_uuid\n";
	$sql .= ") as fax_count, \n";

	//ivr
	$sql .= "(\n";
	$sql .= "select count(*) from v_ivr_menus \n";
	$sql .= "where domain_uuid = d.domain_uuid\n";
	$sql .= ") as ivr_count, \n";

	//voicemail
	$sql .= "(\n";
	$sql .= "select count(*) from v_voicemails \n";
	$sql .= "where domain_uuid = d.domain_uuid\n";
	$sql .= ") as voicemail_count, \n";

	//ring_group
	$sql .= "(\n";
	$sql .= "select count(*) from v_ring_groups \n";
	$sql .= "where domain_uuid = d.domain_uuid\n";
	$sql .= ") as ring_group_count, \n";

	//call_center_queues
	$sql .= "(\n";
	$sql .= "select count(*) from v_call_center_queues \n";
	$sql .= "where domain_uuid = d.domain_uuid\n";
	$sql .= ") as cc_queue_count, \n";

	//contacts
	$sql .= "(\n";
	$sql .= "select count(*) from v_contacts \n";
	$sql .= "where domain_uuid = d.domain_uuid\n";
	$sql .= ") as contact_count, \n";
	
	//accountcodes
	$sql .= "(\n";
	$sql .= "select count(DISTINCT accountcode) from v_extensions \n";
	$sql .= "where domain_uuid = d.domain_uuid\n";
	$sql .= ") as accountcode_count \n";	
	
	$sql .= "FROM v_domains as d \n";
	$sql .= $sql_mod; //add search mod from above
	$sql .= "ORDER BY ".$order_by." ".$order." \n";

	$database = new database;
	$domain_counts = $database->select($sql, null);

//lookup the domain count
	$database = new database;
	$database->table = "v_domains";
	$where[1]["name"] = "domain_uuid";
	$where[1]["operator"] = "=";
	$where[1]["value"] = "*";	
	$numeric_domain_counts = $database->count();
	unset($database,$result);	

//set the http header
	if ($_REQUEST['type'] == "csv") {
	
		//set the headers
			header('Content-type: application/octet-binary');
			header("Content-Disposition: attachment; filename=domain-counts_" . date("Y-m-d") . ".csv");

		//show the column names on the first line
			$z = 0;
			foreach($domain_counts[1] as $key => $val) {
				if ($z == 0) {
					echo '"'.$key.'"';
				}
				else {
					echo ',"'.$key.'"';
				}
				$z++;
			}
			echo "\n";
		
		//add the values to the csv
			$x = 0;
			foreach($domain_counts as $domains) {
				$z = 0;
				foreach($domains as $key => $val) {
					if ($z == 0) {
						echo '"'.$domain_counts[$x][$key].'"';
					}
					else {
						echo ',"'.$domain_counts[$x][$key].'"';
					}
					$z++;
				}
				echo "\n";
				$x++;
			}
			exit;
	}
	
//additional includes
	require_once "resources/header.php";
	$document['title'] = $text['title-domain_counts'];

//set the alternating styles
	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";
	
//show the content
	echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
	echo "  <tr>\n";
	echo "	<td align='left' width='100%'>\n";
	if (permission_exists('domain_counts_view_all')) {
		echo "		<b>".$text['header-domain_counts']."  (".$numeric_domain_counts.")</b><br>\n";
	}
	if (permission_exists('domain_counts_view_domain')) {
		echo "		<b>".$text['header-domain_counts']."</b><br>\n";
	}	
	echo "	</td>\n";
	echo "		<td align='right' width='100%' style='vertical-align: top;'>";
	echo "		<form method='get' action=''>\n";
	echo "			<td style='vertical-align: top; text-align: right; white-space: nowrap;'>\n";
	if (permission_exists('domain_counts_view_all')) {
		echo "				<input type='text' class='txt' style='width: 150px' name='search' id='search' value='".$search."'>";
		echo "				<input type='submit' class='btn' name='submit' value='".$text['button-search']."'>";
	}
	echo "				<input type='button' class='btn' value='".$text['button-export']."' ";
	echo "onclick=\"window.location='domain_counts.php?";
	if (strlen($_SERVER["QUERY_STRING"]) > 0) { 
		echo $_SERVER["QUERY_STRING"]."&type=csv';\">\n";
	} else { 
		echo "type=csv';\">\n";
	}


#	if ($paging_controls_mini != '') {
#		echo 			"<span style='margin-left: 15px;'>".$paging_controls_mini."</span>\n";
#	}
	echo "			</td>\n";
	echo "		</form>\n";	
	echo "  </tr>\n";
	
	
	echo "	<tr>\n";
	echo "		<td colspan='2'>\n";
	echo "			".$text['description-domain_counts']."\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";
	echo "<br />";

	echo "<form name='frm' method='post' action='domain_counts_delete.php'>\n";
	echo "<table class='tr_hover' width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo th_order_by('domain_name', $text['label-domain_name'], $order_by, $order);
	echo th_order_by('extension_count', $text['label-extensions'], $order_by,$order);
	echo th_order_by('user_count', $text['label-users'], $order_by, $order);
	echo th_order_by('device_count', $text['label-devices'], $order_by, $order);
	echo th_order_by('destination_count', $text['label-destinations'], $order_by, $order);
	echo th_order_by('fax_count', $text['label-faxes'], $order_by, $order);
	echo th_order_by('ivr_count', $text['label-ivrs'], $order_by, $order);
	echo th_order_by('voicemail_count', $text['label-voicemails'], $order_by, $order);
	echo th_order_by('ring_group_count', $text['label-ring_groups'], $order_by, $order);
	echo th_order_by('cc_queue_count', $text['label-cc_queues'], $order_by, $order);	
	echo th_order_by('contact_count', $text['label-contacts'], $order_by, $order);
	echo th_order_by('accountcode_count', $text['label-accountcodes'], $order_by, $order);	
	echo "</tr>\n";

	if (isset($domain_counts)) foreach ($domain_counts as $key => $row) {
		
		if (permission_exists('domain_counts_view_all') || (permission_exists('domain_counts_view_domain') && $_SESSION['domain_name'] == $row['domain_name']) ) {
			echo "	<td valign='top' class='".$row_style[$c]."'>".escape($row['domain_name'])."</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".escape($row['extension_count'])."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".escape($row['user_count'])."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".escape($row['device_count'])."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".escape($row['destination_count'])."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".escape($row['fax_count'])."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".escape($row['ivr_count'])."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".escape($row['voicemail_count'])."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".escape($row['ring_group_count'])."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".escape($row['cc_queue_count'])."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".escape($row['contact_count'])."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'><a href='domain_counts_accountcodes.php?id=".escape($row['domain_uuid'])."'>".escape($row['accountcode_count'])."&nbsp;</td>\n";		
			echo "</tr>\n";
			$c = ($c==0) ? 1 : 0;
		}
	}

	echo "</table>";
	echo "</form>";

//show the footer
	require_once "resources/footer.php";
?>
