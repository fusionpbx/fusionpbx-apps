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
*/

if ($domains_processed == 1) {

	$x = 0;
	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'carriers';
	$array[$x]['default_setting_name'] = 'array';
	$array[$x]['default_setting_value'] = 'flowroute';
	$array[$x]['default_setting_enabled'] = 'true';
	$array[$x]['default_setting_description'] = '';
	$x++;
	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'carriers';
	$array[$x]['default_setting_name'] = 'array';
	$array[$x]['default_setting_value'] = 'teli';
	$array[$x]['default_setting_enabled'] = 'true';
	$array[$x]['default_setting_description'] = '';
	$x++;
	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'carriers';
	$array[$x]['default_setting_name'] = 'array';
	$array[$x]['default_setting_value'] = 'twilio';
	$array[$x]['default_setting_enabled'] = 'true';
	$array[$x]['default_setting_description'] = '';
	$x++;
	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'carriers';
	$array[$x]['default_setting_name'] = 'array';
	$array[$x]['default_setting_value'] = 'plivo';
	$array[$x]['default_setting_enabled'] = 'true';
	$array[$x]['default_setting_description'] = '';
	$x++;

	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'twilio_api_url';
	$array[$x]['default_setting_name'] = 'text';
	$array[$x]['default_setting_value'] = 'https://api.twilio.com/2010-04-01/Accounts/{ACCOUNTSID}/Messages.json';
	$array[$x]['default_setting_enabled'] = 'false';
	$array[$x]['default_setting_description'] = '';
	$x++;
	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'plivo_api_url';
	$array[$x]['default_setting_name'] = 'text';
	$array[$x]['default_setting_value'] = 'https://api.plivo.com/v1/Account/{ACCOUNTID}/Message/';
	$array[$x]['default_setting_enabled'] = 'false';
	$array[$x]['default_setting_description'] = '';
	$x++;
	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'flowroute_api_url';
	$array[$x]['default_setting_name'] = 'text';
	$array[$x]['default_setting_value'] = 'https://api.flowroute.com/v2/messages';
	$array[$x]['default_setting_enabled'] = 'false';
	$array[$x]['default_setting_description'] = '';
	$x++;
	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'teli_api_url';
	$array[$x]['default_setting_name'] = 'text';
	$array[$x]['default_setting_value'] = 'https://sms.teleapi.net/sms/send';
	$array[$x]['default_setting_enabled'] = 'false';
	$array[$x]['default_setting_description'] = '';
	$x++;

	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'twilio_api_url';
	$array[$x]['default_setting_name'] = 'text';
	$array[$x]['default_setting_value'] = 'https://api.twilio.com/2010-04-01/Accounts/{ACCOUNTSID}/Messages.json';
	$array[$x]['default_setting_enabled'] = 'false';
	$array[$x]['default_setting_description'] = '';
	$x++;
	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'plivo_api_url';
	$array[$x]['default_setting_name'] = 'text';
	$array[$x]['default_setting_value'] = 'https://api.plivo.com/v1/Account/{ACCOUNTID}/Message/';
	$array[$x]['default_setting_enabled'] = 'false';
	$array[$x]['default_setting_description'] = '';
	$x++;
	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'flowroute_api_url';
	$array[$x]['default_setting_name'] = 'text';
	$array[$x]['default_setting_value'] = 'https://api.flowroute.com/v2/messages';
	$array[$x]['default_setting_enabled'] = 'false';
	$array[$x]['default_setting_description'] = '';
	$x++;
	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'teli_api_url';
	$array[$x]['default_setting_name'] = 'text';
	$array[$x]['default_setting_value'] = 'https://sms.teleapi.net/sms/send';
	$array[$x]['default_setting_enabled'] = 'false';
	$array[$x]['default_setting_description'] = '';
	$x++;

/*	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'flowroute_access_key';
	$array[$x]['default_setting_name'] = 'text';
	$array[$x]['default_setting_value'] = '';
	$array[$x]['default_setting_enabled'] = 'false';
	$array[$x]['default_setting_description'] = '';
	$x++;
	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'flowroute_secret_key';
	$array[$x]['default_setting_name'] = 'text';
	$array[$x]['default_setting_value'] = '';
	$array[$x]['default_setting_enabled'] = 'false';
	$array[$x]['default_setting_description'] = '';
	$x++;

	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'teli_access_key';
	$array[$x]['default_setting_name'] = 'text';
	$array[$x]['default_setting_value'] = '';
	$array[$x]['default_setting_enabled'] = 'false';
	$array[$x]['default_setting_description'] = '';
	$x++;
	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'teli_secret_key';
	$array[$x]['default_setting_name'] = 'text';
	$array[$x]['default_setting_value'] = '';
	$array[$x]['default_setting_enabled'] = 'false';
	$array[$x]['default_setting_description'] = '';
	$x++;

	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'twilio_access_key';
	$array[$x]['default_setting_name'] = 'text';
	$array[$x]['default_setting_value'] = '';
	$array[$x]['default_setting_enabled'] = 'false';
	$array[$x]['default_setting_description'] = '';
	$x++;
	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'twilio_secret_key';
	$array[$x]['default_setting_name'] = 'text';
	$array[$x]['default_setting_value'] = '';
	$array[$x]['default_setting_enabled'] = 'false';
	$array[$x]['default_setting_description'] = '';
	$x++;
*/
		
	//get an array of the default settings
		$sql = "select * from v_default_settings ";
		$prep_statement = $db->prepare($sql);
		$prep_statement->execute();
		$default_settings = $prep_statement->fetchAll(PDO::FETCH_NAMED);
		unset ($prep_statement, $sql);

	//find the missing default settings
		$x = 0;
		foreach ($array as $setting) {
			$found = false;
			$missing[$x] = $setting;
			foreach ($default_settings as $row) {
				if (trim($row['default_setting_subcategory']) == trim($setting['default_setting_subcategory'])) {
					$found = true;
					//remove items from the array that were found
					unset($missing[$x]);
				}
			}
			$x++;
		}

	//add the missing default settings
		if (count($missing) > 0) foreach ($missing as $row) {
			//add the default settings
			$orm = new orm;
			$orm->name('default_settings');
			$orm->save($row);
			$message = $orm->message;
			unset($orm);
		}
		unset($missing);

}

?>
