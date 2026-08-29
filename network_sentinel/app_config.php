<?php
//application details
$apps[$x]['name'] = 'Network Sentinel';
$apps[$x]['uuid'] = '3f9b2c1e-6a3d-4b8e-9a2b-7c1d5e8f4a11';
$apps[$x]['category'] = 'Advanced';
$apps[$x]['subcategory'] = '';
$apps[$x]['version'] = '1.0';
$apps[$x]['license'] = 'Mozilla Public License 1.1';
$apps[$x]['url'] = '';
$apps[$x]['description']['en-us'] = 'Country/IP based allow-deny access control for SIP, RTP, web, and SSH ports, applied via ipset + iptables.';
$apps[$x]['description']['ar-eg'] = 'حماية شبكية للسماح/المنع حسب الدولة أو الـ IP على بورتات SIP/RTP/الإدارة، تُطبَّق عبر ipset و iptables.';

//permission details
$y = 0;
$apps[$x]['permissions'][$y]['name'] = 'sentinel_rule_view';
$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
$y++;
$apps[$x]['permissions'][$y]['name'] = 'sentinel_rule_add';
$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
$y++;
$apps[$x]['permissions'][$y]['name'] = 'sentinel_rule_edit';
$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
$y++;
$apps[$x]['permissions'][$y]['name'] = 'sentinel_rule_delete';
$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
$y++;
$apps[$x]['permissions'][$y]['name'] = 'sentinel_setting_edit';
$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
$y++;
$apps[$x]['permissions'][$y]['name'] = 'sentinel_apply';
$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
$y++;

//schema details - table 0: v_sentinel_rules
$y = 0; //table array index
$z = 0; //field array index
$apps[$x]['db'][$y]['table']['name'] = 'v_sentinel_rules';
$apps[$x]['db'][$y]['table']['parent'] = '';

$apps[$x]['db'][$y]['fields'][$z]['name'] = 'sentinel_rule_uuid';
$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = 'primary';
$z++;

$apps[$x]['db'][$y]['fields'][$z]['name'] = 'rule_type';
$apps[$x]['db'][$y]['fields'][$z]['type'] = 'character varying(20)';
$z++;

$apps[$x]['db'][$y]['fields'][$z]['name'] = 'rule_value';
$apps[$x]['db'][$y]['fields'][$z]['type'] = 'character varying(64)';
$z++;

$apps[$x]['db'][$y]['fields'][$z]['name'] = 'action';
$apps[$x]['db'][$y]['fields'][$z]['type'] = 'character varying(10)';
$z++;

$apps[$x]['db'][$y]['fields'][$z]['name'] = 'port_group';
$apps[$x]['db'][$y]['fields'][$z]['type'] = 'character varying(20)';
$z++;

$apps[$x]['db'][$y]['fields'][$z]['name'] = 'sort_order';
$apps[$x]['db'][$y]['fields'][$z]['type'] = 'integer';
$z++;

$apps[$x]['db'][$y]['fields'][$z]['name'] = 'enabled';
$apps[$x]['db'][$y]['fields'][$z]['type'] = 'boolean';
$z++;

$apps[$x]['db'][$y]['fields'][$z]['name'] = 'description';
$apps[$x]['db'][$y]['fields'][$z]['type'] = 'character varying(255)';
$z++;

$apps[$x]['db'][$y]['fields'][$z]['name'] = 'insert_date';
$apps[$x]['db'][$y]['fields'][$z]['type'] = 'timestamptz';
$z++;

$apps[$x]['db'][$y]['fields'][$z]['name'] = 'insert_user';
$apps[$x]['db'][$y]['fields'][$z]['type'] = 'uuid';
$z++;

$apps[$x]['db'][$y]['fields'][$z]['name'] = 'update_date';
$apps[$x]['db'][$y]['fields'][$z]['type'] = 'timestamptz';
$z++;

$apps[$x]['db'][$y]['fields'][$z]['name'] = 'update_user';
$apps[$x]['db'][$y]['fields'][$z]['type'] = 'uuid';
$z++;

//schema details - table 1: v_sentinel_settings
$y = 1; //table array index
$z = 0; //field array index
$apps[$x]['db'][$y]['table']['name'] = 'v_sentinel_settings';
$apps[$x]['db'][$y]['table']['parent'] = '';

$apps[$x]['db'][$y]['fields'][$z]['name'] = 'sentinel_setting_uuid';
$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = 'primary';
$z++;

$apps[$x]['db'][$y]['fields'][$z]['name'] = 'setting_name';
$apps[$x]['db'][$y]['fields'][$z]['type'] = 'character varying(64)';
$z++;

$apps[$x]['db'][$y]['fields'][$z]['name'] = 'setting_value';
$apps[$x]['db'][$y]['fields'][$z]['type'] = 'character varying(255)';
$z++;

$apps[$x]['db'][$y]['fields'][$z]['name'] = 'description';
$apps[$x]['db'][$y]['fields'][$z]['type'] = 'character varying(255)';
$z++;
