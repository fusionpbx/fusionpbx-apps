<?php

	//application details
		$apps[$x]['name'] = "Sessiontalk";
		$apps[$x]['uuid'] = "85774108-716c-46cb-a34b-ce80b212bc82";
		$apps[$x]['category'] = "Vendor";
		$apps[$x]['subcategory'] = "";
		$apps[$x]['version'] = "1.0";
		$apps[$x]['license'] = "Mozilla Public License 1.1";
		$apps[$x]['url'] = "http://www.fusionpbx.com";
		$apps[$x]['description']['en-us'] = "";
		$apps[$x]['description']['en-gb'] = "";
		$apps[$x]['description']['ar-eg'] = "";
		$apps[$x]['description']['de-at'] = "";
		$apps[$x]['description']['de-ch'] = "";
		$apps[$x]['description']['de-de'] = "";
		$apps[$x]['description']['es-cl'] = "";
		$apps[$x]['description']['es-mx'] = "";
		$apps[$x]['description']['fr-ca'] = "";
		$apps[$x]['description']['fr-fr'] = "";
		$apps[$x]['description']['he-il'] = "";
		$apps[$x]['description']['it-it'] = "";
		$apps[$x]['description']['nl-nl'] = "";
		$apps[$x]['description']['pl-pl'] = "";
		$apps[$x]['description']['pt-br'] = "";
		$apps[$x]['description']['pt-pt'] = "";
		$apps[$x]['description']['ro-ro'] = "";
		$apps[$x]['description']['ru-ru'] = "";
		$apps[$x]['description']['sv-se'] = "";
		$apps[$x]['description']['uk-ua'] = "";

	//permission details
	$y=0;
	$apps[$x]['permissions'][$y]['name'] = "sessiontalk_view";
	$apps[$x]['permissions'][$y]['groups'][] = "user";
	$apps[$x]['permissions'][$y]['groups'][] = "admin";
	$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
	$y++;
	$apps[$x]['permissions'][$y]['name'] = "sessiontalk_view_all";
	$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
	$y++;


	

//default settings
	$y=0;
	$apps[$x]['default_settings'][$y]['default_setting_uuid'] = "67d5e1a7-d0b1-4f3a-b329-fabfb235b0c3";
	$apps[$x]['default_settings'][$y]['default_setting_category'] = "provision";
	$apps[$x]['default_settings'][$y]['default_setting_subcategory'] = "sessiontalk_provider_id";
	$apps[$x]['default_settings'][$y]['default_setting_name'] = "text";
	$apps[$x]['default_settings'][$y]['default_setting_value'] = "";
	$apps[$x]['default_settings'][$y]['default_setting_enabled'] = "true";
	$apps[$x]['default_settings'][$y]['default_setting_description'] = "Sessiontalk Provider ID - Leave blank for whitelabel apps";
	$y++;
	$apps[$x]['default_settings'][$y]['default_setting_uuid'] = "f90a2cd2-55e3-42bf-a6b5-8c9db28ecc9b";
	$apps[$x]['default_settings'][$y]['default_setting_category'] = "provision";
	$apps[$x]['default_settings'][$y]['default_setting_subcategory'] = "sessiontalk_transport";
	$apps[$x]['default_settings'][$y]['default_setting_name'] = "text";
	$apps[$x]['default_settings'][$y]['default_setting_value'] = "UDP";
	$apps[$x]['default_settings'][$y]['default_setting_enabled'] = "false";
	$apps[$x]['default_settings'][$y]['default_setting_description'] = "Sessiontalk Transport. Enable to force Transport type for Sessiontalk apps, otherwise uses the Transport from the Lines";
	$y++;
	$apps[$x]['default_settings'][$y]['default_setting_uuid'] = "432d9632-eafd-43ef-8955-3a510504c83f";
	$apps[$x]['default_settings'][$y]['default_setting_category'] = "provision";
	$apps[$x]['default_settings'][$y]['default_setting_subcategory'] = "sessiontalk_srtp";
	$apps[$x]['default_settings'][$y]['default_setting_name'] = "text";
	$apps[$x]['default_settings'][$y]['default_setting_value'] = "Disabled";
	$apps[$x]['default_settings'][$y]['default_setting_enabled'] = "true";
	$apps[$x]['default_settings'][$y]['default_setting_description'] = "Sessiontalk SRTP";
	$y++;
	$apps[$x]['default_settings'][$y]['default_setting_uuid'] = "1104ce84-a9fa-4b65-8376-c9990b40a41c";
	$apps[$x]['default_settings'][$y]['default_setting_category'] = "provision";
	$apps[$x]['default_settings'][$y]['default_setting_subcategory'] = "sessiontalk_max_activations";
	$apps[$x]['default_settings'][$y]['default_setting_name'] = "numeric";
	$apps[$x]['default_settings'][$y]['default_setting_value'] = "1";
	$apps[$x]['default_settings'][$y]['default_setting_enabled'] = "true";
	$apps[$x]['default_settings'][$y]['default_setting_description'] = "Sessiontalk Maximum Apps per Extension. Defaults to 1 if not set, 0 for unlimited.";
	$y++;		
	$apps[$x]['default_settings'][$y]['default_setting_uuid'] = "6dbdeca0-131f-11eb-adc1-0242ac120002";
	$apps[$x]['default_settings'][$y]['default_setting_category'] = "provision";
	$apps[$x]['default_settings'][$y]['default_setting_subcategory'] = "sessiontalk_qr_expiration";
	$apps[$x]['default_settings'][$y]['default_setting_name'] = "numeric";
	$apps[$x]['default_settings'][$y]['default_setting_value'] = "172800";
	$apps[$x]['default_settings'][$y]['default_setting_enabled'] = "true";
	$apps[$x]['default_settings'][$y]['default_setting_description'] = "QR Code is valid for this many seconds after being generated";
	$y++;
	$apps[$x]['default_settings'][$y]['default_setting_uuid'] = "14b71058-9361-43fd-9f0c-8a08876c7835";
	$apps[$x]['default_settings'][$y]['default_setting_category'] = "provision";
	$apps[$x]['default_settings'][$y]['default_setting_subcategory'] = "sessiontalk_key_rotation";
	$apps[$x]['default_settings'][$y]['default_setting_name'] = "numeric";
	$apps[$x]['default_settings'][$y]['default_setting_value'] = "1209600";
	$apps[$x]['default_settings'][$y]['default_setting_enabled'] = "true";
	$apps[$x]['default_settings'][$y]['default_setting_description'] = "Rotation Period of keys. must be greater than the qr expiration period";
	$y++;
	
// schema details
	$y = 0; //table array index
	$z = 0; //field array index
	//Track Keys
	$apps[$x]['db'][$y]['table']['name'] = "v_sessiontalk_keys";
	$apps[$x]['db'][$y]['table']['parent'] = "";
	$z++;
	$apps[$x]['db'][$y]['fields'][$z]['name'] = "sessiontalk_key_uuid";
	$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
	$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = "primary";
	$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
	$z++;
	$apps[$x]['db'][$y]['fields'][$z]['name'] = "domain_uuid";
	$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "uuid";
	$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
	$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "char(36)";
	$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = "foreign";
	$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = "v_domains";
	$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = "domain_uuid";
	$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
	$z++;
	$apps[$x]['db'][$y]['fields'][$z]['name'] = "key1";
	$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
	$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
	$z++;
	$apps[$x]['db'][$y]['fields'][$z]['name'] = "key2";
	$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
	$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
	$z++;
	$apps[$x]['db'][$y]['fields'][$z]['name'] = "expiration_date";
	$apps[$x]['db'][$y]['fields'][$z]['type'] = "numeric";
	$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
	$z++;

?>
