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
	Portions created by the Initial Developer are Copyright (C) 2016
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/

if ($domains_processed == 1) {
	
	$x = 0;
	$array[$x]['default_setting_uuid'] = '67511e2d-35e1-4f70-80ac-4265ec39d2fe';
	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'carriers';
	$array[$x]['default_setting_name'] = 'array';
	$array[$x]['default_setting_value'] = 'flowroute';
	$array[$x]['default_setting_enabled'] = 'true';
	$array[$x]['default_setting_description'] = '';
	$x++;
	$array[$x]['default_setting_uuid'] = '22bcf947-b634-4849-aaec-dd0635ca6f16';
	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'carriers';
	$array[$x]['default_setting_name'] = 'array';
	$array[$x]['default_setting_value'] = 'teli';
	$array[$x]['default_setting_enabled'] = 'true';
	$array[$x]['default_setting_description'] = '';
	$x++;
	$array[$x]['default_setting_uuid'] = '27b5c0a4-b824-4e51-aadd-9a19023202cc';
	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'carriers';
	$array[$x]['default_setting_name'] = 'array';
	$array[$x]['default_setting_value'] = 'twilio';
	$array[$x]['default_setting_enabled'] = 'true';
	$array[$x]['default_setting_description'] = '';
	$x++;
	$array[$x]['default_setting_uuid'] = '5c8cea06-edb7-4092-bbdd-7fdc89f02eb0';
	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'carriers';
	$array[$x]['default_setting_name'] = 'array';
	$array[$x]['default_setting_value'] = 'plivo';
	$array[$x]['default_setting_enabled'] = 'true';
	$array[$x]['default_setting_description'] = '';
	$x++;
	$array[$x]['default_setting_uuid'] = 'e997203c-ca48-45b4-828d-e347ff66fa7c';
	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'twilio_api_url';
	$array[$x]['default_setting_name'] = 'text';
	$array[$x]['default_setting_value'] = 'https://api.twilio.com/2010-04-01/Accounts/{ACCOUNTSID}/Messages.json';
	$array[$x]['default_setting_enabled'] = 'false';
	$array[$x]['default_setting_description'] = '';
	$x++;
	$array[$x]['default_setting_uuid'] = '957b31ab-bc8e-4bff-8366-59f17e658550';
	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'plivo_api_url';
	$array[$x]['default_setting_name'] = 'text';
	$array[$x]['default_setting_value'] = 'https://api.plivo.com/v1/Account/{ACCOUNTID}/Message/';
	$array[$x]['default_setting_enabled'] = 'false';
	$array[$x]['default_setting_description'] = '';
	$x++;
	$array[$x]['default_setting_uuid'] = 'ed6cd4a7-9f89-4156-9591-313b9a73bcfa';
	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'flowroute_api_url';
	$array[$x]['default_setting_name'] = 'text';
	$array[$x]['default_setting_value'] = 'https://api.flowroute.com/v2/messages';
	$array[$x]['default_setting_enabled'] = 'false';
	$array[$x]['default_setting_description'] = '';
	$x++;
	$array[$x]['default_setting_uuid'] = '535e7884-d8af-4c61-967c-cc2a2ebfb6a3';
	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'teli_api_url';
	$array[$x]['default_setting_name'] = 'text';
	$array[$x]['default_setting_value'] = 'https://sms.teleapi.net/sms/send';
	$array[$x]['default_setting_enabled'] = 'false';
	$array[$x]['default_setting_description'] = '';
	$x++;

	$array[$x]['default_setting_uuid'] = '14101c26-c3f9-46aa-a67a-3642752e56f4';
	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'flowroute_access_key';
	$array[$x]['default_setting_name'] = 'text';
	$array[$x]['default_setting_value'] = '';
	$array[$x]['default_setting_enabled'] = 'false';
	$array[$x]['default_setting_description'] = '';
	$x++;
	$array[$x]['default_setting_uuid'] = '6fd7b4b0-ed90-4aea-9666-919e1b9b4e35';
	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'flowroute_secret_key';
	$array[$x]['default_setting_name'] = 'text';
	$array[$x]['default_setting_value'] = '';
	$array[$x]['default_setting_enabled'] = 'false';
	$array[$x]['default_setting_description'] = '';
	$x++;

	$array[$x]['default_setting_uuid'] = '551b3948-8328-42be-a873-9a32f8b49463';
	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'teli_access_key';
	$array[$x]['default_setting_name'] = 'text';
	$array[$x]['default_setting_value'] = '';
	$array[$x]['default_setting_enabled'] = 'false';
	$array[$x]['default_setting_description'] = '';
	$x++;
	
	$array[$x]['default_setting_uuid'] = 'c7607430-1b55-41ff-934e-7f9142b29df0';
	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'teli_secret_key';
	$array[$x]['default_setting_name'] = 'text';
	$array[$x]['default_setting_value'] = '';
	$array[$x]['default_setting_enabled'] = 'false';
	$array[$x]['default_setting_description'] = '';
	$x++;

	$array[$x]['default_setting_uuid'] = 'c56042fc-4cd4-425b-a39a-9297aaed7743';
	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'twilio_access_key';
	$array[$x]['default_setting_name'] = 'text';
	$array[$x]['default_setting_value'] = '';
	$array[$x]['default_setting_enabled'] = 'false';
	$array[$x]['default_setting_description'] = '';
	$x++;
	
	$array[$x]['default_setting_uuid'] = '784196cb-d012-4a74-8183-4fc0b738c06a';
	$array[$x]['default_setting_category'] = 'sms';
	$array[$x]['default_setting_subcategory'] = 'twilio_secret_key';
	$array[$x]['default_setting_name'] = 'text';
	$array[$x]['default_setting_value'] = '';
	$array[$x]['default_setting_enabled'] = 'false';
	$array[$x]['default_setting_description'] = '';
	$x++;

	//get an array of the default settings
		$sql = "select * from v_default_settings where default_setting_category = 'sms'";
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
					unset($array[$x]);
				}
			}
			$x++;
		}
		unset($array);

	//update the array structure
		if (is_array($missing)) {
			$array['default_settings'] = $missing;
			unset($missing);
		}

	//add the default settings
		if (is_array($array)) {
			$database = new database;
			$database->app_name = 'default_settings';
			$database->app_uuid = '2c2453c0-1bea-4475-9f44-4d969650de09';
			$database->save($array);
			$message = $database->message;
			unset($database);
		}
}
?>
