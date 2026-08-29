<?php
if (!defined('SENTINEL_ETC_DIR')) {
    define('SENTINEL_ETC_DIR', '/etc/network_sentinel');
}
if (!defined('SENTINEL_APPLY_SCRIPT')) {
    define('SENTINEL_APPLY_SCRIPT', '/usr/local/bin/network_sentinel_apply.sh');
}

// ISO 3166-1 alpha-2 country codes with display names.
function sentinel_country_list() {
    return [
        'EG'=>'Egypt','SA'=>'Saudi Arabia',
        // Africa
        'AO'=>'Angola','BF'=>'Burkina Faso','BI'=>'Burundi','BJ'=>'Benin','BW'=>'Botswana','CD'=>'DR Congo','CF'=>'Central African Rep.','CG'=>'Congo','CI'=>"Cote d'Ivoire",'CM'=>'Cameroon',
        'CV'=>'Cape Verde','DJ'=>'Djibouti','DZ'=>'Algeria','ER'=>'Eritrea','ET'=>'Ethiopia','GA'=>'Gabon','GH'=>'Ghana','GM'=>'Gambia','GN'=>'Guinea','GQ'=>'Equatorial Guinea',
        'GW'=>'Guinea-Bissau','KE'=>'Kenya','KM'=>'Comoros','LR'=>'Liberia','LS'=>'Lesotho','LY'=>'Libya','MA'=>'Morocco','MG'=>'Madagascar','ML'=>'Mali','MR'=>'Mauritania',
        'MU'=>'Mauritius','MW'=>'Malawi','MZ'=>'Mozambique','NA'=>'Namibia','NE'=>'Niger','NG'=>'Nigeria','RE'=>'Reunion','RW'=>'Rwanda','SC'=>'Seychelles','SD'=>'Sudan',
        'SH'=>'Saint Helena','SL'=>'Sierra Leone','SN'=>'Senegal','SO'=>'Somalia','ST'=>'Sao Tome','SZ'=>'Eswatini','TD'=>'Chad','TG'=>'Togo','TN'=>'Tunisia','TZ'=>'Tanzania',
        'UG'=>'Uganda','YT'=>'Mayotte','ZA'=>'South Africa','ZM'=>'Zambia','ZW'=>'Zimbabwe','AQ'=>'Antarctica','GS'=>'South Georgia','TF'=>'French S. Territories','AE'=>'UAE',
        // Asia / Middle East
        'AF'=>'Afghanistan','AM'=>'Armenia','AZ'=>'Azerbaijan','BD'=>'Bangladesh','BH'=>'Bahrain','BN'=>'Brunei','BT'=>'Bhutan','CC'=>'Cocos Islands','CN'=>'China','CX'=>'Christmas Island',
        'CY'=>'Cyprus','GE'=>'Georgia','HK'=>'Hong Kong','ID'=>'Indonesia','IL'=>'Israel','IN'=>'India','IO'=>'British Indian Ocean Terr.','IQ'=>'Iraq','IR'=>'Iran','JO'=>'Jordan',
        'JP'=>'Japan','KG'=>'Kyrgyzstan','KH'=>'Cambodia','KP'=>'North Korea','KR'=>'South Korea','KW'=>'Kuwait','KZ'=>'Kazakhstan','LA'=>'Laos','LB'=>'Lebanon','LK'=>'Sri Lanka',
        'MM'=>'Myanmar','MN'=>'Mongolia','MO'=>'Macao','MV'=>'Maldives','MY'=>'Malaysia','NP'=>'Nepal','OM'=>'Oman','PH'=>'Philippines','PK'=>'Pakistan','PS'=>'Palestine',
        'QA'=>'Qatar','SG'=>'Singapore','SY'=>'Syria','TH'=>'Thailand','TJ'=>'Tajikistan','TL'=>'Timor-Leste','TM'=>'Turkmenistan','TR'=>'Turkey','TW'=>'Taiwan','UZ'=>'Uzbekistan',
        'VN'=>'Vietnam','YE'=>'Yemen',
        // Europe
        'AD'=>'Andorra','AL'=>'Albania','AT'=>'Austria','AX'=>'Aland Islands','BA'=>'Bosnia','BE'=>'Belgium','BG'=>'Bulgaria','BY'=>'Belarus','CH'=>'Switzerland','CZ'=>'Czechia',
        'DE'=>'Germany','DK'=>'Denmark','EE'=>'Estonia','ES'=>'Spain','FI'=>'Finland','FO'=>'Faroe Islands','FR'=>'France','GB'=>'United Kingdom','GG'=>'Guernsey','GI'=>'Gibraltar',
        'GR'=>'Greece','HR'=>'Croatia','HU'=>'Hungary','IE'=>'Ireland','IM'=>'Isle of Man','IS'=>'Iceland','IT'=>'Italy','JE'=>'Jersey','LI'=>'Liechtenstein','LT'=>'Lithuania',
        'LU'=>'Luxembourg','LV'=>'Latvia','MC'=>'Monaco','MD'=>'Moldova','ME'=>'Montenegro','MK'=>'North Macedonia','MT'=>'Malta','NL'=>'Netherlands','NO'=>'Norway','PL'=>'Poland',
        'PT'=>'Portugal','RO'=>'Romania','RS'=>'Serbia','RU'=>'Russia','SE'=>'Sweden','SI'=>'Slovenia','SJ'=>'Svalbard','SK'=>'Slovakia','SM'=>'San Marino','UA'=>'Ukraine','VA'=>'Vatican',
        // North America / Caribbean
        'AG'=>'Antigua','AI'=>'Anguilla','AW'=>'Aruba','BB'=>'Barbados','BL'=>'Saint Barthelemy','BM'=>'Bermuda','BS'=>'Bahamas','BZ'=>'Belize','CA'=>'Canada','CR'=>'Costa Rica',
        'CU'=>'Cuba','DM'=>'Dominica','DO'=>'Dominican Rep.','GD'=>'Grenada','GL'=>'Greenland','GP'=>'Guadeloupe','GT'=>'Guatemala','HN'=>'Honduras','HT'=>'Haiti','JM'=>'Jamaica',
        'KN'=>'Saint Kitts','KY'=>'Cayman Islands','LC'=>'Saint Lucia','MF'=>'Saint Martin','MQ'=>'Martinique','MS'=>'Montserrat','MX'=>'Mexico','NI'=>'Nicaragua','PA'=>'Panama',
        'PM'=>'Saint Pierre','PR'=>'Puerto Rico','SV'=>'El Salvador','TC'=>'Turks and Caicos','TT'=>'Trinidad','US'=>'United States','VC'=>'Saint Vincent','VG'=>'British Virgin Islands',
        // Oceania
        'AS'=>'American Samoa','AU'=>'Australia','CK'=>'Cook Islands','FJ'=>'Fiji','FM'=>'Micronesia','GU'=>'Guam','KI'=>'Kiribati','MH'=>'Marshall Islands','MP'=>'N. Mariana Islands',
        'NC'=>'New Caledonia','NF'=>'Norfolk Island','NR'=>'Nauru','NU'=>'Niue','NZ'=>'New Zealand','PF'=>'French Polynesia','PG'=>'Papua New Guinea','PN'=>'Pitcairn','PW'=>'Palau',
        'SB'=>'Solomon Islands','TK'=>'Tokelau','TO'=>'Tonga','TV'=>'Tuvalu','UM'=>'US Minor Outlying Is.','VU'=>'Vanuatu','WF'=>'Wallis and Futuna','WS'=>'Samoa',
        // South America
        'AR'=>'Argentina','BO'=>'Bolivia','BR'=>'Brazil','CL'=>'Chile','CO'=>'Colombia','EC'=>'Ecuador','FK'=>'Falkland Islands','GF'=>'French Guiana','GY'=>'Guyana','PE'=>'Peru',
        'PY'=>'Paraguay','SR'=>'Suriname','UY'=>'Uruguay','VE'=>'Venezuela',
    ];
}

// countries grouped by continent, for the bulk-add page
function sentinel_continents() {
    return [
        'Africa' => ['EG','AO','BF','BI','BJ','BW','CD','CF','CG','CI','CM','CV','DJ','DZ','ER','ET','GA','GH','GM','GN','GQ','GW','KE','KM','LR','LS','LY','MA','MG','ML','MR','MU','MW','MZ','NA','NE','NG','RE','RW','SC','SD','SH','SL','SN','SO','ST','SZ','TD','TG','TN','TZ','UG','YT','ZA','ZM','ZW','AQ','GS','TF','AE'],
        'Asia / Middle East' => ['SA','AF','AM','AZ','BD','BH','BN','BT','CC','CN','CX','CY','GE','HK','ID','IL','IN','IO','IQ','IR','JO','JP','KG','KH','KP','KR','KW','KZ','LA','LB','LK','MM','MN','MO','MV','MY','NP','OM','PH','PK','PS','QA','SG','SY','TH','TJ','TL','TM','TR','TW','UZ','VN','YE'],
        'Europe' => ['AD','AL','AT','AX','BA','BE','BG','BY','CH','CZ','DE','DK','EE','ES','FI','FO','FR','GB','GG','GI','GR','HR','HU','IE','IM','IS','IT','JE','LI','LT','LU','LV','MC','MD','ME','MK','MT','NL','NO','PL','PT','RO','RS','RU','SE','SI','SJ','SK','SM','UA','VA'],
        'North America / Caribbean' => ['AG','AI','AW','BB','BL','BM','BS','BZ','CA','CR','CU','DM','DO','GD','GL','GP','GT','HN','HT','JM','KN','KY','LC','MF','MQ','MS','MX','NI','PA','PM','PR','SV','TC','TT','US','VC','VG'],
        'Oceania' => ['AS','AU','CK','FJ','FM','GU','KI','MH','MP','NC','NF','NR','NU','NZ','PF','PG','PN','PW','SB','TK','TO','TV','UM','VU','WF','WS'],
        'South America' => ['AR','BO','BR','CL','CO','EC','FK','GF','GY','PE','PY','SR','UY','VE'],
    ];
}

/**
 * Exports enabled rules from the DB into a plain conf file the root-owned
 * apply script reads. www-data needs write access to SENTINEL_ETC_DIR for this
 * (chown www-data:www-data /etc/network_sentinel), the apply script itself runs as root.
 */
function sentinel_export_rules($database) {
    $sql = "select rule_type, rule_value, action, port_group ";
    $sql .= "from v_sentinel_rules where enabled = true order by sort_order asc";
    $rows = $database->select($sql, null, 'all');

    $lines = [];
    if (is_array($rows)) {
        foreach ($rows as $row) {
            $lines[] = implode('|', [
                trim($row['rule_type']),
                trim($row['rule_value']),
                trim($row['action']),
                trim($row['port_group']),
            ]);
        }
    }

    if (!is_dir(SENTINEL_ETC_DIR)) {
        @mkdir(SENTINEL_ETC_DIR, 0750, true);
    }
    file_put_contents(SENTINEL_ETC_DIR . '/rules.conf', implode("\n", $lines) . "\n");
}

function sentinel_export_settings($database) {
    $sql = "select setting_name, setting_value from v_sentinel_settings";
    $rows = $database->select($sql, null, 'all');

    $lines = [];
    if (is_array($rows)) {
        foreach ($rows as $row) {
            $name = strtoupper(trim($row['setting_name']));
            $value = trim((string)$row['setting_value']);
            $lines[] = $name . '=' . $value;
        }
    }

    if (!is_dir(SENTINEL_ETC_DIR)) {
        @mkdir(SENTINEL_ETC_DIR, 0750, true);
    }
    file_put_contents(SENTINEL_ETC_DIR . '/ports.conf', implode("\n", $lines) . "\n");
}

/**
 * Writes the config files, then invokes the root apply script via a
 * tightly scoped sudoers rule (see README). Returns [success(bool), output(string)].
 */
function sentinel_apply_rules($database) {
    sentinel_export_rules($database);
    sentinel_export_settings($database);

    // large rule sets (100+ countries) can take a while even with parallel fetching -
    // don't let PHP's default execution time limit kill the request mid-apply.
    set_time_limit(180);

    $cmd = 'sudo -n ' . escapeshellarg(SENTINEL_APPLY_SCRIPT) . ' 2>&1';
    exec($cmd, $output, $return_code);

    return [$return_code === 0, implode("\n", $output)];
}
