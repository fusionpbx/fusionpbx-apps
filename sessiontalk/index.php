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
	Portions created by the Initial Developer are Copyright (C) 2008-2020
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
	KonradSC <konrd@yahoo.com>
*/

//includes
	require_once "root.php";
	require_once "resources/require.php";
	require_once "resources/check_auth.php";

//check permissions
	if (permission_exists('sessiontalk_view')) {
		//access granted
	}
	else {
		echo "access denied";
		exit;
	}

//add multi-lingual support
	$language = new text;
	$text = $language->get();

//verify the id is as uuid then set as a variable
	if (is_uuid($_GET['id'])) {
		$extension_uuid = $_GET['id'];
	}

//get the extension(s)
	if (permission_exists('extension_edit')) {
		//admin user
		$sql = "SELECT e.extension_uuid, e.extension, e.description, u.api_key, e.number_alias ";
		$sql .= "FROM v_extensions AS e, v_extension_users AS eu, v_users AS u ";
		$sql .= "WHERE e.domain_uuid = :domain_uuid ";
		$sql .= "AND e.enabled = 'true' ";
		$sql .= "AND e.extension_uuid = eu.extension_uuid ";
		$sql .= "AND eu.user_uuid = u.user_uuid ";
		$sql .= "order by e.extension asc ";
	}
	else {
		//normal user
		$sql = "SELECT e.extension_uuid, e.extension, e.description, u.api_key, e.number_alias ";
		$sql .= "FROM v_extensions AS e, v_extension_users AS eu, v_users AS u ";
		$sql .= "WHERE e.domain_uuid = :domain_uuid ";
		$sql .= "AND eu.user_uuid = :user_uuid ";
		$sql .= "AND e.extension_uuid = eu.extension_uuid ";
		$sql .= "AND eu.user_uuid = u.user_uuid ";
		$sql .= "order by e.extension asc ";
		$parameters['user_uuid'] = $_SESSION['user']['user_uuid'];
	}
	$parameters['domain_uuid'] = $_SESSION['domain_uuid'];
	$database = new database;
	$extensions = $database->select($sql, $parameters, 'all');
//echo $sql;
//exit;
	unset($sql, $parameters);

	if (is_uuid($extension_uuid) && is_array($extensions) && @sizeof($extensions) != 0) {

		//loop through get selected extension
			if (is_array($extensions) && @sizeof($extensions) != 0) {
				foreach ($extensions as $extension) {
					if ($extension['extension_uuid'] == $extension_uuid) {
						$field = $extension;
						break;
					}
				}
			}

		//get the username
			$username = $field['extension'];
			if (isset($field['number_alias']) && strlen($field['number_alias']) > 0) {
				$username = $field['number_alias'];
			}

			$qr_content = "scsc:". $username . "@" . $_SESSION['domain_name'] . ":". $field['api_key'] . ":" . $_SESSION['provision']['sessiontalk_provider_id']['text'];

	}

//debian
	//apt install qrencode

//include the header
	$document['title'] = $text['title-sessiontalk'];
	require_once "resources/header.php";

//show the content
	echo "<form name='frm' id='frm' method='get'>\n";

	echo "<div class='action_bar' id='action_bar'>\n";
	echo "	<div class='heading'><b>".$text['title-sessiontalk']."</b></div>\n";
	echo "	<div class='actions'>\n";
	echo "		<a href='https://play.google.com/store/apps/details?id=co.froute.sessioncloud' target='_blank'><img src='/app/sessiontalk/resources/images/google_play.png' style='width: auto; height: 30px;' /></a>";
	echo "		<a href='https://apps.apple.com/us/app/sessioncloud-sip-softphone/id1065327562' target='_blank'><img src='/app/sessiontalk/resources/images/apple_app_store.png' style='width: auto; height: 30px;' /></a>";
	echo "	</div>\n";
	echo "	<div style='clear: both;'></div>\n";
	echo "</div>\n";

	echo $text['title_description-sessiontalk']."\n";
	echo "<br /><br />\n";
	//echo $qr_content; //debug 
	echo "<div style='text-align: center; white-space: nowrap; margin: 10px 0 40px 0;'>";
	echo $text['label-extension']."<br />\n";
	echo "<select name='id' class='formfld' onchange='this.form.submit();'>\n";
	echo "	<option value='' >".$text['label-select']."...</option>\n";
	if (is_array($extensions) && @sizeof($extensions) != 0) {
		foreach ($extensions as $row) {
			$selected = $row['extension_uuid'] == $extension_uuid ? "selected='selected'" : null;
			echo "	<option value='".escape($row['extension_uuid'])."' ".$selected.">".escape($row['extension'])." ".escape($row['number_alias'])." ".escape($row['description'])."</option>\n";
		}
	}
	echo "</select>\n";
echo "</div>\n";
	echo "</form>\n";
	echo "<br>\n";



//stream the file
	if (is_uuid($extension_uuid)) {
		
		$html_link = "scsc:?username=". $username . "@" . $_SESSION['domain_name'] . ":". $_SESSION['provision']['sessiontalk_provider_id']['text']."%26password=".$field['api_key'];
		$html_link = html_entity_decode( $html_link, ENT_QUOTES, 'UTF-8' );

		//Windows 10
		echo "<div class='action_bar' id='action_bar_2'>\n";
		echo "	<div class='heading'><b>".$text['header-windows_10']."</b></div>\n";
		echo "	<div style='clear: both;'></div>\n";
		echo "</div>\n";	

		echo "<div>\n";
		echo " ".$text['description-step_1']."\n";
		echo " <a href='ms-appinstaller:?source=https://windows-softphone.s3.eu-west-2.amazonaws.com/sessioncloud.appinstaller&activationUri=".$html_link."'>".$text['description-windows_10']."</a>\n";
		echo " <br/>\n";
		echo " <br/>\n";
		echo " ".$text['description-step_2']."<a href='".PROJECT_PATH."/app/sessiontalk/sessiontalk_directory.php'>".$text['description-windows_10_directory']."</a>\n";
		echo "</div>\n";
		echo "<br>\n";
		echo "<br>\n";

		//Mobile
		echo "<div class='action_bar' id='action_bar'>\n";
		echo "	<div class='heading'><b>".$text['header-mobile']."</b></div>\n";
		echo "	<div style='clear: both;'></div>\n";
		echo "</div>\n";	

		echo "<div>\n";
		echo " ".$text['description-step_1_mobile']."\n";
		echo " <br>\n";
		echo " <br>\n";
		echo " ".$text['description-step_2_mobile']."\n";
		echo "</div>\n";

		require_once 'resources/qr_code/QRErrorCorrectLevel.php';
		require_once 'resources/qr_code/QRCode.php';
		require_once 'resources/qr_code/QRCodeImage.php';
		$qr_content = html_entity_decode( $qr_content, ENT_QUOTES, 'UTF-8' );
  
		try {
			
			$code = new QRCode (- 1, QRErrorCorrectLevel::H);
			$code->addData($qr_content);
			$code->make();
			
			$img = new QRCodeImage ($code, $width=420, $height=420, $quality=50);
			$img->draw();
			$image = $img->getImage();
			$img->finish();
		}
		catch (Exception $error) {
			echo $error;
		}
	}

//html image
	if (is_uuid($extension_uuid)) {
		echo "<img src=\"data:image/jpeg;base64,".base64_encode($image)."\" style='margin-top: 30px; padding: 5px; background: white; max-width: 100%;'>\n";
	}

//add the footer
	require_once "resources/footer.php";

?>
