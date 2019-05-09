<?php

	//application details
		$apps[$x]['name'] = "Call ACL";
		$apps[$x]['uuid'] = "32aeff16-c7bf-439b-a54d-2568a7e0cec0";
		$apps[$x]['category'] = "Switch";
		$apps[$x]['subcategory'] = "";
		$apps[$x]['version'] = "1.0";
		$apps[$x]['license'] = "Mozilla Public License 1.1";
		$apps[$x]['url'] = "http://www.fusionpbx.com";
		$apps[$x]['description']['en-us'] = "A tool to make call ACL across domain";

	//permission details
		$y=0;
		$apps[$x]['permissions'][$y]['name'] = "call_acl_view";
		$apps[$x]['permissions'][$y]['menu']['uuid'] = "0674b1b7-e487-4563-bd07-1b1d4dc6e64d";
		$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
		$apps[$x]['permissions'][$y]['groups'][] = "admin";
		$y++;
		$apps[$x]['permissions'][$y]['name'] = "call_acl_add";
		$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
		$apps[$x]['permissions'][$y]['groups'][] = "admin";
		$y++;
		$apps[$x]['permissions'][$y]['name'] = "call_acl_edit";
		$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
		$apps[$x]['permissions'][$y]['groups'][] = "admin";
		$y++;
		$apps[$x]['permissions'][$y]['name'] = "call_acl_delete";
		$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
		$apps[$x]['permissions'][$y]['groups'][] = "admin";
	//default settings
		$y = 0;
		$apps[$x]['default_settings'][$y]['default_setting_uuid'] = '3c1b5c71-bd6a-446c-b3a2-b5215c212f28';
		$apps[$x]['default_settings'][$y]['default_setting_category'] = 'call_acl';
		$apps[$x]['default_settings'][$y]['default_setting_subcategory'] = 'max_order';
		$apps[$x]['default_settings'][$y]['default_setting_name'] = 'numeric';
		$apps[$x]['default_settings'][$y]['default_setting_value'] = '20';
		$apps[$x]['default_settings'][$y]['default_setting_enabled'] = 'true';
		$apps[$x]['default_settings'][$y]['default_setting_description'] = 'Maximum order number to select';

	//schema details
		$y=0;
		$apps[$x]['db'][$y]['table']['name'] = "v_call_acl";
		$apps[$x]['db'][$y]['table']['parent'] = "";
		$z=0;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = "domain_uuid";
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "uuid";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "char(36)";
		$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = "foreign";
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['table'] = "v_domains";
		$apps[$x]['db'][$y]['fields'][$z]['key']['reference']['field'] = "domain_uuid";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = "call_acl_uuid";
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = "uuid";
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = "char(36)";
		$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = "primary";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = "call_acl_order";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "numeric";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "Order";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = "call_acl_name";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "Enter the name.";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = "call_acl_source";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "Call ACL source to be checked against";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = "call_acl_destination";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "Call ACL destination to be checked against";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = "call_acl_action";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "Action for call.";
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = "call_acl_enabled";
		$apps[$x]['db'][$y]['fields'][$z]['type'] = "text";
		$apps[$x]['db'][$y]['fields'][$z]['description']['en-us'] = "Enable/disable ACL rule";
?>