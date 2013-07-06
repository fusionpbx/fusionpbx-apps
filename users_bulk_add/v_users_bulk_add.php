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

$dq = '"';
$self = $_SERVER['PHP_SELF'];
ini_set('auto_detect_line_endings', '1');

function get_domain_uuids(PDO $db) {
	$query 				= sprintf("SELECT domain_uuid, domain_name FROM v_domains;");
	$stmt 				= $db->query($query);
	$results 			= $stmt->fetchAll(PDO::FETCH_ASSOC);
	$result_count 		= count($results);
	for ($i = 0; $i < $result_count; $i++) {
		$domain_name 		= $results[$i]['domain_name'];
		$domain_uuid 		= $results[$i]['domain_uuid'];
		$domain_uuids[$domain_name]	= $domain_uuid;
	}
	return $domain_uuids;
}

function generate_insert_query($line, $places, $table, PDO $db, $domain_uuids) {
	global $v_salt;
	foreach ($places as $field => $place) {
		$fields[] = $field;
		if ($field == 'password') {
			$values[] = $db->quote(md5($v_salt.$line[$place]));
		} else {
			$values[] = $db->quote($line[$place]);
		}
	}

	$username = $line[$places['username']];
	switch ($table) {
		case 'v_users':
			if (empty($username) || $username == 'NULL') return false;

			// we'll assume that every user should be able to login
			if (!in_array('password', $fields)){
				$fields[] = 'password';
				$values[] = $db->quote(md5($v_salt.$username));
			}
			break;

		case 'v_extensions':
			$ext = $line[$places['extension']];
			if (empty($ext) || $ext == 'NULL') return false;

			// let's also assume every extension should also have a vm pin
			if (!in_array('vm_password', $fields)) {
				$fields[] = 'vm_password';
				$values[] = $db->quote($ext);
			}

			/* if we have a username but no user_list,
			 * let's assume we want the extension tied to the current user
			 */
			if (!in_array('user_list', $fields) && $username) {
				$fields[] = 'user_list';
				$values[] = $db->quote(sprintf('|%s|', $username));
			}

			$idx = array_search('username', $fields);
			unset($fields[$idx]);
			unset($values[$idx]);
			break;
		default:
			break;
	}

	if (!in_array('domain_uuid')) {
		//print "domain_uuid not found, adding one for localhost<br>\n";
		//printf('<pre>%s</pre>', print_r($domain_uuids, true));
		$fields[] = 'domain_uuid';
		$values[] = $domain_uuids['localhost'];
	}

	$query = sprintf('INSERT INTO %s (%s) VALUES (%s);'
	, $table, join(', ', $fields), join(', ', $values)
	);
	return $query;
}

function get_field_places($first_line, $valid_fields) {
	$places = array();
	foreach ($valid_fields as $key => $value) {
		//print "Looking for $value in valid fields<br>\n";
		$idx = array_search($value, $first_line);
		if ($idx !== false) {
			$places[$value] = $idx;
		}
	}
	return $places;
}

function check_required_fields($line, $all_fields) {
	//printf('<pre>%s</pre>', print_r($line, true));
	if (!in_array('username', $line) || !in_array('extension', $line)) {
		$all_fields = array_unique($all_fields);
		sort($all_fields);
		printf("You need to add a header line to the csv, the valid fields are:<br>\n%s"
		, implode("<br>\n", $all_fields)
		);
		return false;
	}
	return true;
}

function insert_db_row($db, $line, $places, $table, $domain_uuids) {
	global $inserted;
	//printf("<pre>%s</pre>\n", print_r($inserted, true));
	$inserted[$table]++;
	$query = generate_insert_query($line, $places, $table, $db, $domain_uuids);
	//print "QUERY: $query<br>\n";
	if (empty($query)) {
		return;
	}
	$affected_rows = $db->exec($query);
	//printf("we affected %s rows<br>\n", $affected_rows);
}


include "root.php";
require_once "includes/require.php";
require_once "resources/check_auth.php";
if (!if_group("admin") && !if_group("superadmin")) {
	printf("access denied");
	exit;
}
require_once "includes/header.php";
require_once "includes/paging.php";

$inserted = array('v_users' => 0, 'v_extensions' => 0, 'v_group_users' => 0);
if (is_array($_FILES) && array_key_exists('users_file', $_FILES)) {
	$domain_uuids 				= get_domain_uuids($db);
	$user_fields 		= get_db_field_names($db, 'v_users');
	//printf("<pre>users => %s<br></pre>\n", print_r($user_fields, true));
	$extension_fields 	= get_db_field_names($db, 'v_extensions');
	//printf("<pre>exts => %s<br></pre>\n", print_r($extension_fields, true));

	$all_fields = array_merge($user_fields, $extension_fields);
	//printf("<pre>all => %s</pre>\n", print_r($all_fields, true));

	$fh 				= fopen($_FILES['users_file']['tmp_name'], 'r');
	if (!$fh) {
		//printf('<pre>%s</pre>', print_r($_FILES, true));
		print "Couldn't open the uploaded file<br>\n";
	} else {
		$line 				= fgetcsv($fh, null, ',');
		$user_places		= get_field_places($line, $user_fields);
		$extension_places	= get_field_places($line, $extension_fields);
		if (array_key_exists('username', $user_places)) {
			$extension_places['username'] = $user_places['username'];
		}
		//printf("<pre>user_places => %s</pre>\n", print_r($user_places, true));
		//printf("<pre>ext_places => %s</pre>\n", print_r($extension_places, true));

		//printf("<pre>FIRST LINE => %s</pre>", print_r($line, true));
		if (check_required_fields($line, $all_fields)) {
			while ($line = fgetcsv($fh, null, ',')) {
				// create user
				insert_db_row($db, $line, $user_places, 'v_users', $domain_uuids);

				// add user to members group
				$grp_line 	= array('member', $line[$user_places['username']]);
				$grp_places	= array('group_name' => 0, 'username' => 1);
				insert_db_row($db, $grp_line, $grp_places, 'v_group_users', $domain_uuids);
				
				// add user's extension
				insert_db_row($db, $line, $extension_places, 'v_extensions', $domain_uuids);
			}
		}
		fclose($fh);
		//printf("<pre>%s</pre>\n", print_r($inserted, true));

		printf("<h3>Bulk Add Results:</h3>\n");
		printf("<table>\n");
		foreach ($inserted as $key => $value) {
			printf("<tr>\n");
			$name_parts 	= explode('_', $key);
			$table_prefix 	= array_shift($name_parts);
			printf("<td>");
			foreach ($name_parts as $phrase_word) {
				printf('%s ', ucfirst($phrase_word));
			}
			printf("</td>\n");
			printf("<td>%d</td>\n", $value);
			printf("</tr>\n");
		}
		printf("</table>\n");
	}
}

printf("<form method=${dq}POST${dq} action=${dq}$self${dq} enctype=${dq}multipart/form-data${dq}");
printf("<input type=${dq}file${dq} name=${dq}users_file${dq}");
printf("<input type=${dq}submit${dq} value=${dq}Upload${dq}");
printf("</form>");

require_once "includes/footer.php";
