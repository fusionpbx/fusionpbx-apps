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
	if (permission_exists('bulk_account_settings_voicemails')) {
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
	$option_selected = check_str($_GET["option_selected"]);
	$option_action = check_str($_GET["option_action"]);
	
//handle search term
	$search = check_str($_GET["search"]);
	if (strlen($search) > 0) {
		$sql_mod = "and ( ";
		$sql_mod .= "CAST(voicemail_id AS TEXT) LIKE '%".$search."%' ";
		$sql_mod .= "or voicemail_description ILIKE '%".$search."%' ";		
		$sql_mod .= ") ";
	}
	if (strlen($order_by) < 1) {
		$order_by = "voicemail_id";
		$order = "ASC";
	}

	$domain_uuid = $_SESSION['domain_uuid'];

//get total voicemail count from the database
	$sql = "select count(*) as num_rows from v_voicemails where domain_uuid = '".$_SESSION['domain_uuid']."' ".$sql_mod." ";
	$prep_statement = $db->prepare($sql);
	if ($prep_statement) {
		$prep_statement->execute();
		$row = $prep_statement->fetch(PDO::FETCH_ASSOC);
		$total_voicemails = $row['num_rows'];
		if (($db_type == "pgsql") or ($db_type == "mysql")) {
			$numeric_voicemails = $row['num_rows'];
		}
	}
	unset($prep_statement, $row);

//prepare to page the results
	$rows_per_page = ($_SESSION['domain']['paging']['numeric'] != '') ? $_SESSION['domain']['paging']['numeric'] : 50;
	$param = "&search=".$search;
	if (!isset($_GET['page'])) { $_GET['page'] = 0; }
	$_GET['page'] = check_str($_GET['page']);
	list($paging_controls_mini, $rows_per_page, $var_3) = paging($total_voicemails, $param, $rows_per_page, true); //top
	list($paging_controls, $rows_per_page, $var_3) = paging($total_voicemails, $param, $rows_per_page); //bottom
	$offset = $rows_per_page * $_GET['page'];

//voicemail options
	if (preg_match ('/option_(.)/',$option_selected)) {
		preg_match ('/option_(.)/',$option_selected, $matches);
		$option_number = $matches[1];
	}

//get all the voicemails from the database
	$sql = "SELECT \n";
	$sql .= "v.voicemail_description, \n";
	$sql .= "v.voicemail_id, \n";
	$sql .= "v.voicemail_uuid, \n";
	$sql .= "v.voicemail_file, \n";
	$sql .= "v.voicemail_enabled, \n";	
	$sql .= "v.voicemail_local_after_email, \n";
	$sql .= "v.voicemail_transcription_enabled \n";
	$sql .= "FROM v_voicemails as v \n";
	$sql .= "WHERE v.domain_uuid = '$domain_uuid' \n";
	$sql .= $sql_mod; //add search mod from above
	$sql .= "ORDER BY ".$order_by." ".$order." \n";
	$sql .= "limit $rows_per_page offset $offset ";
	$database = new database;
	$database->select($sql);
	$directory = $database->result;

$sql_view = $sql;
	unset($database,$result);

//lookup the options
	$x = 0;
	foreach ($directory as $key => $row) {
		$sql = "SELECT voicemail_option_param, voicemail_option_order \n";
		$sql .= "FROM v_voicemail_options \n";
		$sql .= "WHERE domain_uuid = '$domain_uuid' \n";
		$sql .= "and voicemail_uuid = '".$row['voicemail_uuid']."' ";
		$sql .= "and voicemail_option_digits = '".$option_number."' ";
		$database = new database;
		$database->select($sql);
		$result = $database->result;
		$directory[$key]['option_db_value'] = $result;		
		unset($result,$database);
		$x++;
	}
	
//additional includes
	require_once "resources/header.php";
	$document['title'] = $text['title-voicemails_settings'];

//set the alternating styles
	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";

//initialize the destinations object
	$destination = new destinations;

//show the content
	echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
	echo "  <tr>\n";
	echo "	<td align='left' width='100%'>\n";
	echo "		<b>".$text['header-voicemails']." (".$numeric_voicemails.")</b><br>\n";

//options list
	echo "<form name='frm' method='get' id=option_selected>\n";
	echo "    <select class='formfld' name='option_selected'  onchange=\"this.form.submit();\">\n";
	echo "    <option value=''>".$text['label-voicemail_null']."</option>\n";
	if ($option_selected == "voicemail_file") {
		echo "    <option value='voicemail_file' selected='selected'>".$text['label-voicemail_file']."</option>\n";
	}
	else {
		echo "    <option value='voicemail_file'>".$text['label-voicemail_file']."</option>\n";
	}
	if ($option_selected == "voicemail_enabled") {
		echo "    <option value='voicemail_enabled' selected='selected'>".$text['label-voicemail_enabled']."</option>\n";
	}
	else {
		echo "    <option value='voicemail_enabled'>".$text['label-voicemail_enabled']."</option>\n";
	}
	if ($option_selected == "voicemail_local_after_email") {
		echo "    <option value='voicemail_local_after_email' selected='selected'>".$text['label-voicemail_local_after_email']."</option>\n";
	}
	else {
		echo "    <option value='voicemail_local_after_email'>".$text['label-voicemail_local_after_email']."</option>\n";
	}
	if ($option_selected == "voicemail_password") {
		echo "    <option value='voicemail_password' selected='selected'>".$text['label-voicemail_password']."</option>\n";
	}
	else {
		echo "    <option value='voicemail_password'>".$text['label-voicemail_password']."</option>\n";
	}
	if($_SESSION['voicemail']['transcribe_enabled']['boolean'] == "true") {
		if ($option_selected == "voicemail_transcription_enabled") {
			echo "    <option value='voicemail_transcription_enabled' selected='selected'>".$text['label-voicemail_transcription_enabled']."</option>\n";
		}
		else {
			echo "    <option value='voicemail_transcription_enabled'>".$text['label-voicemail_transcription_enabled']."</option>\n";
		}
	}
	if ($option_selected == "voicemail_option_0") {
		echo "    <option value='voicemail_option_0' selected='selected'>".$text['label-voicemail_option_0']."</option>\n";
	}
	else {
		echo "    <option value='voicemail_option_0'>".$text['label-voicemail_option_0']."</option>\n";
	}
	if ($option_selected == "voicemail_option_1") {
		echo "    <option value='voicemail_option_1' selected='selected'>".$text['label-voicemail_option_1']."</option>\n";
	}
	else {
		echo "    <option value='voicemail_option_1'>".$text['label-voicemail_option_1']."</option>\n";
	}
	if ($option_selected == "voicemail_option_2") {
		echo "    <option value='voicemail_option_1' selected='selected'>".$text['label-voicemail_option_2']."</option>\n";
	}
	else {
		echo "    <option value='voicemail_option_2'>".$text['label-voicemail_option_2']."</option>\n";
	}
	if ($option_selected == "voicemail_option_3") {
		echo "    <option value='voicemail_option_3' selected='selected'>".$text['label-voicemail_option_3']."</option>\n";
	}
	else {
		echo "    <option value='voicemail_option_3'>".$text['label-voicemail_option_3']."</option>\n";
	}
	if ($option_selected == "voicemail_option_4") {
		echo "    <option value='voicemail_option_4' selected='selected'>".$text['label-voicemail_option_4']."</option>\n";
	}
	else {
		echo "    <option value='voicemail_option_4'>".$text['label-voicemail_option_4']."</option>\n";
	}
	if ($option_selected == "voicemail_option_5") {
		echo "    <option value='voicemail_option_5' selected='selected'>".$text['label-voicemail_option_5']."</option>\n";
	}
	else {
		echo "    <option value='voicemail_option_5'>".$text['label-voicemail_option_5']."</option>\n";
	}
	if ($option_selected == "voicemail_option_6") {
		echo "    <option value='voicemail_option_6' selected='selected'>".$text['label-voicemail_option_6']."</option>\n";
	}
	else {
		echo "    <option value='voicemail_option_6'>".$text['label-voicemail_option_6']."</option>\n";
	}
	if ($option_selected == "voicemail_option_7") {
		echo "    <option value='voicemail_option_7' selected='selected'>".$text['label-voicemail_option_7']."</option>\n";
	}
	else {
		echo "    <option value='voicemail_option_7'>".$text['label-voicemail_option_7']."</option>\n";
	}
	if ($option_selected == "voicemail_option_8") {
		echo "    <option value='voicemail_option_8' selected='selected'>".$text['label-voicemail_option_8']."</option>\n";
	}
	else {
		echo "    <option value='voicemail_option_8'>".$text['label-voicemail_option_8']."</option>\n";
	}
	if ($option_selected == "voicemail_option_9") {
		echo "    <option value='voicemail_option_9' selected='selected'>".$text['label-voicemail_option_9']."</option>\n";
	}
	else {
		echo "    <option value='voicemail_option_9'>".$text['label-voicemail_option_9']."</option>\n";
	}	
	echo "    </select>\n";
	echo "    </form>\n";
	echo "<br />\n";
	echo $text['description-voicemail_settings_description']."\n";
	echo "</td>\n";
	
	echo "		<td align='right' width='100%' style='vertical-align: top;'>";
	echo "		<form method='get' action=''>\n";
	echo "			<td style='vertical-align: top; text-align: right; white-space: nowrap;'>\n";
	echo "				<input type='button' class='btn' alt='".$text['button-back']."' onclick=\"window.location='bulk_account_settings.php'\" value='".$text['button-back']."'>\n";	
	echo "				<input type='text' class='txt' style='width: 150px' name='search' id='search' value='".$search."'>";
	echo "				<input type='hidden' class='txt' style='width: 150px' name='option_selected' id='option_selected' value='".$option_selected."'>";
	echo "				<input type='submit' class='btn' name='submit' value='".$text['button-search']."'>";
	if ($paging_controls_mini != '') {
		echo 			"<span style='margin-left: 15px;'>".$paging_controls_mini."</span>\n";
	}
	echo "			</td>\n";
	echo "		</form>\n";	
	echo "  </tr>\n";
	
	echo "	<tr>\n";
	echo "		<td colspan='2'>\n";
	echo "			".$text['description-voicemails_settings']."\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";
	echo "<br />";

	if (strlen($option_selected) > 0) {
		echo "<form name='voicemails' method='post' action='bulk_account_settings_voicemails_update.php'>\n";
		echo "<input class='formfld' type='hidden' name='option_selected' maxlength='255' value=\"$option_selected\">\n";
		echo "<table width='auto' border='0' cellpadding='0' cellspacing='0'>\n";
		echo "<tr>\n";
		//option is Password
		if($option_selected == 'voicemail_password') {
			echo "<td class='vtable' align='left'>\n";
			echo "    <input class='formfld' type='password' name='new_setting' id='password' autocomplete='off' onmouseover=\"this.type='text';\" onfocus=\"this.type='text';\" onmouseout=\"if (!$(this).is(':focus')) { this.type='password'; }\" onblur=\"this.type='password';\" autocomplete='off' maxlength='50' value=\"$new_setting\">\n";
			
			echo "<br />\n";
			echo $text["description-".$option_selected.""]."\n";
			echo "</td>\n";
		}
		//option is voicemail_enabled or voicemail_local_after_email or voicemail_transcription_enabled
		if($option_selected == 'voicemail_enabled' || $option_selected == 'voicemail_local_after_email' || $option_selected == 'voicemail_transcription_enabled') {
			echo "<td class='vtable' align='left'>\n";
			echo "    <select class='formfld' name='new_setting'>\n";
			echo "    <option value='true'>".$text['label-true']."</option>\n";
			echo "    <option value='false'>".$text['label-false']."</option>\n";
			echo "    </select>\n";
			echo "    <br />\n";
			echo $text["description-".$option_selected.""]."\n";
			echo "</td>\n";
		}
		//option is voicemail_file
		if($option_selected == 'voicemail_file') {
			echo "<td class='vtable' align='left'>\n";
			echo "    <select class='formfld' name='new_setting'>\n";
			echo "    <option value='listen'>".$text['option-voicemail_file_listen']."</option>\n";
			echo "    <option value='link'>".$text['option-voicemail_file_link']."</option>\n";
			echo "    <option value='attach'>".$text['option-voicemail_file_attach']."</option>\n";
			echo "    </select>\n";
			echo "    <br />\n";
			echo $text["description-".$option_selected.""]."\n";
			echo "</td>\n";
		}
		//option is voicemail_option
		if (preg_match ('/option_/',$option_selected)) {
			echo "<td class='vtable' align='left'>\n";
			echo "    <select class='formfld' name='option_action' onchange=\"$('.add_option').slideToggle();\">\n";
			echo "    <option value='add'>".$text['label-add']."</option>\n";
			echo "    <option value='remove'>".$text['label-remove']."</option>\n";
			echo "    </select><br>\n";
			echo "	  ".$text["label-".$option_selected.""]."\n";
			echo "</td>\n";

			echo "<td class='vtable add_option' align='left' nowrap='nowrap'>\n";
			echo $destination->select('ivr', 'voicemail_option_param', '');
			echo "<br>".$text['label-destination']."</td>\n";
			echo "<td class='vtable add_option' align='left'>\n";
			echo "	<select name='voicemail_option_order' class='formfld' style='width:55px'>\n";
			if (strlen(htmlspecialchars($voicemail_option_order))> 0) {
				echo "	<option selected='yes' value='".htmlspecialchars($voicemail_option_order)."'>".htmlspecialchars($voicemail_option_order)."</option>\n";
			}
			$i=0;
			while($i<=999) {
				if (strlen($i) == 1) {
					echo "	<option value='00$i'>00$i</option>\n";
				}
				if (strlen($i) == 2) {
					echo "	<option value='0$i'>0$i</option>\n";
				}
				if (strlen($i) == 3) {
					echo "	<option value='$i'>$i</option>\n";
				}
				$i++;
			}
			echo "	</select><br>\n";
			echo "".$text['label-order']."</td>\n";
			echo "<td class='vtable add_option' align='left'>\n";
			echo "	<input class='formfld' style='width:100px' type='text' name='voicemail_option_description' maxlength='255' value=\"".$voicemail_option_description."\">\n";
			echo "<br>".$text['label-description']."</td>\n";
		}
		
		echo "<td align='left'>\n";
		echo "<input type='button' class='btn' alt='".$text['button-submit']."' onclick=\"if (confirm('".$text['confirm-update']."')) { document.forms.voicemails.submit(); }\" value='".$text['button-submit']."'>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>";
		echo "<br />";
	}
	echo "<table class='tr_hover' width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	if (is_array($directory)) {
		echo "<th style='width: 30px; text-align: center; padding: 0px;'><input type='checkbox' id='chk_all' onchange=\"(this.checked) ? check('all') : check('none');\"></th>";
	}
	echo th_order_by('voicemail_id', $text['label-voicemail_id'], $order_by,$order,'','',"option_selected=".$option_selected."&search=".$search."");
	if (preg_match ('/option_/',$option_selected)) {
		echo th_order_by('voicemail_id', $text["label-".$option_selected.""], $order_by, $order,'','',"option_selected=".$option_selected."&search=".$search."");	
	}
	else {
		echo th_order_by('voicemail_id', $text['label-voicemail_file'], $order_by, $order,'','',"option_selected=".$option_selected."&search=".$search."");
		echo th_order_by('voicemail_id', $text['label-voicemail_local_after_email'], $order_by, $order,'','',"option_selected=".$option_selected."&search=".$search."");
		if($_SESSION['voicemail']['transcribe_enabled']['boolean'] == "true") {
			echo th_order_by('voicemail_id', $text['label-voicemail_transcription_enabled'], $order_by, $order,'','',"option_selected=".$option_selected."&search=".$search."");	
		}
	}
	echo th_order_by('voicemail_id', $text['label-voicemail_enabled'], $order_by, $order,'','',"option_selected=".$option_selected."&search=".$search."");	
	echo th_order_by('voicemail_id', $text['label-voicemail_description'], $order_by, $order,'','',"option_selected=".$option_selected."&search=".$search."");
	echo "</tr>\n";



if (is_array($directory)) {

		foreach($directory as $key => $row) {
			$tr_link = (permission_exists('voicemail_edit')) ? " href='/app/voicemails/voicemail_edit.php?id=".$row['voicemail_uuid']."'" : null;
			echo "<tr ".$tr_link.">\n";

			echo "	<td valign='top' class='".$row_style[$c]." tr_link_void' style='text-align: center; vertical-align: middle; padding: 0px;'>";
			echo "		<input type='checkbox' name='id[]' id='checkbox_".$row['voicemail_uuid']."' value='".$row['voicemail_uuid']."' onclick=\"if (!this.checked) { document.getElementById('chk_all').checked = false; }\">";
			echo "	</td>";
			$ext_ids[] = 'checkbox_'.$row['voicemail_uuid'];

			echo "	<td valign='top' class='".$row_style[$c]."'> ".$row['voicemail_id']."&nbsp;</td>\n";
			if (preg_match ('/option_/',$option_selected)) {
				echo "	<td valign='top' class='".$row_style[$c]."'>\n";
					$x = 0;
					foreach($row['option_db_value'] as $key => $value) {
						if ($x > 0) {
							echo ", ";
						}
						echo $value['voicemail_option_param']." (Order: ".$value['voicemail_option_order'].")";
						$x++;
					}
				echo "&nbsp;</td>\n";
			}				

			else {
				echo "	<td valign='top' class='".$row_style[$c]."'> ".$row['voicemail_file']."&nbsp;</td>\n";
				echo "	<td valign='top' class='".$row_style[$c]."'> ".$row['voicemail_local_after_email']."&nbsp;</td>\n";
				if($_SESSION['voicemail']['transcribe_enabled']['boolean'] == "true") {
					echo "	<td valign='top' class='".$row_style[$c]."'> ".$row['voicemail_transcription_enabled']."&nbsp;</td>\n";			
				}
			}
			echo "	<td valign='top' class='".$row_style[$c]."'> ".$row['voicemail_enabled']."&nbsp;</td>\n";			
			echo "	<td valign='top' class='".$row_style[$c]."'> ".$row['voicemail_description']."</td>\n";
			echo "</tr>\n";
			$c = ($c) ? 0 : 1;
		}

		unset($directory, $row);
	}

	echo "</table>";
	echo "</form>";

	if (strlen($paging_controls) > 0) {
		echo "<br />";
		echo $paging_controls."\n";
	}
	echo "<br /><br />".((is_array($directory)) ? "<br /><br />" : null);

	// check or uncheck all checkboxes
	if (sizeof($ext_ids) > 0) {
		echo "<script>\n";
		echo "	function check(what) {\n";
		echo "		document.getElementById('chk_all').checked = (what == 'all') ? true : false;\n";
		foreach ($ext_ids as $ext_id) {
			echo "		document.getElementById('".$ext_id."').checked = (what == 'all') ? true : false;\n";
		}
		echo "	}\n";
		echo "</script>\n";
	}

	if (is_array($directory)) {
		// check all checkboxes
		key_press('ctrl+a', 'down', 'document', null, null, "check('all');", true);

		// delete checked
		key_press('delete', 'up', 'document', array('#search'), $text['confirm-delete'], 'document.forms.frm.submit();', true);
	}

//show the footer
	require_once "resources/footer.php";
?>
