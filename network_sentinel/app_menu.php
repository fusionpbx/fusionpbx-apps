<?php
$y = 0;

// item 1: Sentinel Rules
$apps[$x]['menu'][$y]['title']['en-us'] = "Sentinel Rules";
$apps[$x]['menu'][$y]['title']['ar-eg'] = "قواعد Sentinel";
$apps[$x]['menu'][$y]['uuid'] = "b1e2a3c4-1111-4a5b-8c6d-9e0f1a2b3c4d";
$apps[$x]['menu'][$y]['parent_uuid'] = "594d99c5-6128-9c88-ca35-4b33392cec0f"; // Advanced (standard FusionPBX parent)
$apps[$x]['menu'][$y]['category'] = "internal";
$apps[$x]['menu'][$y]['icon'] = "";
$apps[$x]['menu'][$y]['path'] = "/app/network_sentinel/country_rules.php";
$apps[$x]['menu'][$y]['order'] = "";
$apps[$x]['menu'][$y]['groups'][] = "superadmin";
$y++;

// item 3: Bulk Add Countries
$apps[$x]['menu'][$y]['title']['en-us'] = "Sentinel Bulk Add";
$apps[$x]['menu'][$y]['title']['ar-eg'] = "إضافة دول بالجملة";
$apps[$x]['menu'][$y]['uuid'] = "b1e2a3c4-3333-4a5b-8c6d-9e0f1a2b3c4d";
$apps[$x]['menu'][$y]['parent_uuid'] = "594d99c5-6128-9c88-ca35-4b33392cec0f"; // Advanced
$apps[$x]['menu'][$y]['category'] = "internal";
$apps[$x]['menu'][$y]['icon'] = "";
$apps[$x]['menu'][$y]['path'] = "/app/network_sentinel/country_bulk_add.php";
$apps[$x]['menu'][$y]['order'] = "";
$apps[$x]['menu'][$y]['groups'][] = "superadmin";
$y++;
$apps[$x]['menu'][$y]['title']['en-us'] = "Sentinel Settings";
$apps[$x]['menu'][$y]['title']['ar-eg'] = "إعدادات Sentinel";
$apps[$x]['menu'][$y]['uuid'] = "b1e2a3c4-2222-4a5b-8c6d-9e0f1a2b3c4d";
$apps[$x]['menu'][$y]['parent_uuid'] = "594d99c5-6128-9c88-ca35-4b33392cec0f"; // Advanced
$apps[$x]['menu'][$y]['category'] = "internal";
$apps[$x]['menu'][$y]['icon'] = "";
$apps[$x]['menu'][$y]['path'] = "/app/network_sentinel/sentinel_settings.php";
$apps[$x]['menu'][$y]['order'] = "";
$apps[$x]['menu'][$y]['groups'][] = "superadmin";
$y++;
