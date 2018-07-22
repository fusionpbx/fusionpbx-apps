<?php

	//application details
		$apps[$x]['name'] = "Import Extensions";
		$apps[$x]['uuid'] = "9257cf72-e70c-4187-a84b-cefe66a9ebeb";
		$apps[$x]['category'] = "Switch";
		$apps[$x]['subcategory'] = "";
		$apps[$x]['version'] = "";
		$apps[$x]['license'] = "Mozilla Public License 1.1";
		$apps[$x]['url'] = "http://www.fusionpbx.com";
		$apps[$x]['description']['en-us'] = "Possibility to import extensions and devices from CSV file";
		$apps[$x]['description']['es-cl'] = "";
		$apps[$x]['description']['de-de'] = "";
		$apps[$x]['description']['de-ch'] = "";
		$apps[$x]['description']['de-at'] = "";
		$apps[$x]['description']['fr-fr'] = "";
		$apps[$x]['description']['fr-ca'] = "";
		$apps[$x]['description']['fr-ch'] = "";
		$apps[$x]['description']['pt-pt'] = "";
		$apps[$x]['description']['pt-br'] = "";

	//permission details
		$y = 0;
		$apps[$x]['permissions'][$y]['name'] = "import_extensions";
		$apps[$x]['permissions'][$y]['menu']['uuid'] = "70934a0a-b49f-4e91-ba11-a7849cc30ed6";
		$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
		$apps[$x]['permissions'][$y]['groups'][] = "admin";
		$y++;

		//default settings
		$y = 0;
		$apps[$x]['default_settings'][$y]['default_setting_uuid'] = '44fe8455-6c5c-4772-a1b7-e3827f32b520';
		$apps[$x]['default_settings'][$y]['default_setting_category'] = 'import_extensions';
		$apps[$x]['default_settings'][$y]['default_setting_subcategory'] = 'rows_to_show';
		$apps[$x]['default_settings'][$y]['default_setting_name'] = 'numeric';
		$apps[$x]['default_settings'][$y]['default_setting_value'] = '4';
		$apps[$x]['default_settings'][$y]['default_setting_enabled'] = 'true';
		$apps[$x]['default_settings'][$y]['default_setting_description'] = 'Set the maximum lines from file to show on Import screen';
		$y++;
?>
