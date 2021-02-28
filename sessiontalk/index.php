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

if (strpos($_SERVER['REQUEST_URI'],'.appinstaller') !== false) {
	require_once "appinstaller.php";
	exit;
}

//includes
	require_once "root.php";
	require_once "resources/require.php";
	require_once "resources/check_auth.php";
	require_once "resources/functions/functions.php";

//check permissions
	if (permission_exists('sessiontalk_view') or permission_exists('sessiontalk_view_all')) {
		//access granted
	}
	else {
		echo "access denied";
		exit;
	}

//add multi-lingual support
	$language = new text;
	$text = $language->get();

//verify the id is a uuid then set as a variable
	if (is_uuid($_GET['id'])) {
		$extension_uuid = $_GET['id'];
	}

//get the extension(s)
	if (permission_exists('sessiontalk_view_all')) {
		//admin user
		$sql = "SELECT e.extension_uuid, e.extension, e.description, e.number_alias ";
		$sql .= "FROM v_extensions AS e ";
		$sql .= "WHERE e.domain_uuid = :domain_uuid ";
		$sql .= "AND e.enabled = 'true' ";
		$sql .= "order by e.extension asc ";
	}
	else {
		//normal user
		$sql = "SELECT e.extension_uuid, e.extension, e.description, e.number_alias ";
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

		$app_details = new sessiontalk;
		$app_details->settings = $_SESSION['sessiontalk'];
		$app_details->set_extension($field, $_SESSION['domain_uuid'], $_SESSION['domain_name']);
        $credentials = $app_details->get_credentials();

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
	echo "		<a href='".$_SESSION['sessiontalk']['android_url']['text']."' target='_blank'><img src='/app/sessiontalk/resources/images/google_play.png' style='width: auto; height: 30px;' /></a>";
	echo "		<a href='".$_SESSION['sessiontalk']['apple_url']['text']."' target='_blank'><img src='/app/sessiontalk/resources/images/apple_app_store.png' style='width: auto; height: 30px;' /></a>";
	echo "	</div>\n";
	echo "	<div style='clear: both;'></div>\n";
	echo "</div>\n";

	echo $text['title_description-sessiontalk']."\n";
	echo "<br /><br />\n";
	echo "<div style='text-align: center; white-space: nowrap;'>";


	echo "<br /><br />\n";
	//echo "QR Content:".$credentials['mobile']."<br />\n  ";  //enable for debugging
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

	echo "</form>\n";
	echo "<br />\n";


//Activation Link for Windows Softphone
	if ($_SESSION['sessiontalk']['windows_softphone']['boolean'] && is_uuid($extension_uuid)) {
		$windows_credentials = $app_details->get_credentials();
		echo "<br /><div style='text-align: center; white-space: nowrap;'>";
		echo "<a href=\"".$windows_credentials['windows']."\"/>".$text['label-windows-softphone']."</a><br />";
		echo "</div>\n";
	}

//html image
	if (is_uuid($extension_uuid)) {
		echo "<img src=\"data:image/jpeg;base64,".base64_encode($credentials['qr_image'])."\" style='margin-top: 30px; padding: 5px; background: white; max-width: 100%;'/>\n";
	}

	echo "</div>\n";
	echo "</div>\n";

//add the footer
	require_once "resources/footer.php";

?>
