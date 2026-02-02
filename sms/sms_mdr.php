<?php
/* $Id$ */
/*
	call.php
	Copyright (C) 2008, 2009 Mark J Crane
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
	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
	James Rose <james.o.rose@gmail.com>

*/

//includes files
	require_once dirname(__DIR__, 2) . "/resources/require.php";
	require_once "resources/check_auth.php";

//check permissions
	if (!permission_exists('sms_view')) {
		echo "access denied";
		exit;
	}

//add multi-lingual support
	$language = new text;
	$text = $language->get();

//include the header
	require_once "resources/header.php";
	require_once "resources/paging.php";

//set variables
	//$sql = "select domain_name, extension, sms_message_uuid,start_stamp,from_number,to_number,message,direction from v_sms_messages, v_domains, v_extensions where v_sms_messages.domain_uuid = v_domains.domain_uuid and v_sms_messages.extension_uuid = v_extensions.extension_uuid and v_domains.domain_uuid = :domain_uuid order by start_stamp DESC";
	$num_rows = '0';
	$param = "";

//get the number of rows in the v_xml_cdr
	$sql = "SELECT count(*) as num_rows ";
	$sql .= "from v_sms_messages s as, v_domains as d ";
	$sql .= "WHERE d.domain_uuid = s.domain_uuid ";
	$sql .= "AND d.domain_uuid = :domain_uuid ";
	$parameters['domain_uuid'] = $domain_uuid;
	$row = $database->select($sql, $parameters ?? null, 'row');
	if (is_array($row) {
		if ($row['num_rows'] > 0) {
			$num_rows = $row['num_rows'];
		} else {
			$num_rows = '0';
		}
	}
	unset($parameters);

//prepare to page the results
	$rows_per_page = $settings->get('domain', 'paging', 50);
	$param = !empty($search) ? "&search=".$search : null;
	$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 0;
	list($paging_controls, $rows_per_page) = paging($num_rows, $param, $rows_per_page);
	list($paging_controls_mini, $rows_per_page) = paging($num_rows, $param, $rows_per_page, true);
	$offset = $rows_per_page * $page;

//get the rows per page
	$rows_per_page = $settings->get('domain', 'paging', 50);

//get messages from the database
	$sql = "SELECT domain_name, s.extension_uuid as extension, ";
	$sql .= "sms_message_uuid, start_stamp, from_number, to_number, message,direction ";
	$sql .= "FROM v_sms_messages as s, v_domains as d ";
	$sql .= "WHERE d.domain_uuid = s.domain_uuid ";
	$sql .= "and d.domain_uuid = :domain_uuid order by start_stamp DESC ";
	$sql .= "limit " . $rows_per_page . " offset " . $offset . " ";
	error_log("SQL: " . print_r($sql,true));
	$parameters['domain_uuid'] = $domain_uuid;
	$result = $database->select($sql, $parameters ?? null, 'row');
	$result_count = count($result);
	unset ($parameters, $sql);

//set the values for the row style
	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";

//mod paging parameters for inclusion in column sort heading links
	$param = substr($param, 1); //remove leading '&'
	$param = substr($param, 0, strrpos($param, '&order_by=')); //remove trailing order by

//show the results
	$col_count = 6;
	echo "<form name='frm' method='post' action='xml_cdr_delete.php'>\n";
	echo "<table class='tr_hover' width='100%' cellpadding='0' cellspacing='0' border='0'>\n";
	echo "<tr>\n";
	echo "<th>&nbsp;</th>\n";
	if ($_REQUEST['showall'] && permission_exists('xml_cdr_all')) {
		echo th_order_by('domain_name', $text['label-domain'], $order_by, $order, null, null, $param);
		$col_count++;
	}
	echo th_order_by('extension', $text['label-extension'], $order_by, $order, null, null, $param);
	echo th_order_by('start_stamp', $text['label-start'], $order_by, $order, null, "style='text-align: center;'", $param);
	echo th_order_by('caller_id_number', $text['label-source'], $order_by, $order, null, null, $param);
	echo th_order_by('destination_number', $text['label-destination'], $order_by, $order, null, null, $param);
	echo th_order_by('message', $text['label-message'], $order_by, $order, null, null, $param);
	echo "</tr>\n";

	if ($result_count > 0) {
		echo "<tr>\n";

		//determine if theme images exist
		$theme_image_path = $_SERVER["DOCUMENT_ROOT"]."/themes/".$settings->get('domain', 'template', 'default')."/images/";
		$theme_cdr_images_exist = (
			file_exists($theme_image_path."icon_cdr_inbound_answered.png") &&
			file_exists($theme_image_path."icon_cdr_inbound_voicemail.png") &&
			file_exists($theme_image_path."icon_cdr_inbound_cancelled.png") &&
			file_exists($theme_image_path."icon_cdr_inbound_failed.png") &&
			file_exists($theme_image_path."icon_cdr_outbound_answered.png") &&
			file_exists($theme_image_path."icon_cdr_outbound_cancelled.png") &&
			file_exists($theme_image_path."icon_cdr_outbound_failed.png") &&
			file_exists($theme_image_path."icon_cdr_local_answered.png") &&
			file_exists($theme_image_path."icon_cdr_local_voicemail.png") &&
			file_exists($theme_image_path."icon_cdr_local_cancelled.png") &&
			file_exists($theme_image_path."icon_cdr_local_failed.png")
			) ? true : false;

		foreach($result as $index => $row) {

			$tmp_start_epoch = ($_SESSION['domain']['time_format']['text'] == '12h') ? date("j M Y g:i:sa", $row['start_stamp']) : date("j M Y H:i:s", $row['start_epoch']);

			$extension = " - ";
			if(!empty($row['extension'])) {
				$sql = "SELECT extension FROM v_extensions ";
				$sql .= "WHERE extension_uuid = :extension_uuid ";
				$sql .= "AND domain_uuid = :domain_uuid ";
				$parameters['domain_uuid'] = $domain_uuid;
				$parameters['extension_uuid'] = $row['extension'];
				$result = $database->select($sql, $parameters ?? null, 'row');
				$extension = !empty($result['extension'])? $result['extension'] : " - ";
			}

			//determine call result and appropriate icon
			echo "<td valign='top' class='".$row_style[$c]."'>\n";
			if ($theme_cdr_images_exist) {
				$call_result = 'answered';
				echo "<img src='".PROJECT_PATH."/themes/".$_SESSION['domain']['template']['name']."/images/icon_cdr_".$row['direction']."_".$call_result.".png' width='16' style='border: none; cursor: help;' title='".$text['label-'.$row['direction']].": ".$text['label-'.$call_result]."'>\n";
			}
			else { echo "&nbsp;"; }
			echo "</td>\n";

			//domain name
			if ($_REQUEST['showall'] && permission_exists('xml_cdr_all')) {
				echo "	<td valign='top' class='".$row_style[$c]."'>";
				echo 	$row['domain_name'].'&nbsp;';
				echo "	</td>\n";
			}
			echo "	<td valign='top' class='".$row_style[$c]."'>".escape($extension)."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".escape($row['start_stamp'])."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".escape($row['from_number'])."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".escape($row['to_number'])."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".escape(urldecode($row['message']))."&nbsp;</td>\n";
			echo "</tr>\n";
			$c = ($c) ? 0 : 1;
		} // end foreach
		unset($sql, $result, $row_count);
	} // end if

	echo "</table>\n";
	echo "</form>\n";

echo "<br><br>";
if ($result_count == $rows_per_page) {
	echo $paging_controls;
}

echo "<br><br>";
//show the footer
	require_once "resources/footer.php";
?>
