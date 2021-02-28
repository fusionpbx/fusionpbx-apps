<?php

function load_defaults($domain_name) {
	//get the default settings
	$sql = "select * from v_default_settings ";
	$sql .= "where default_setting_enabled = 'true' ";
	$sql .= "order by default_setting_order asc ";
	$database = new database;
	$result = $database->select($sql, null, 'all');
	//unset the previous settings
	if (is_array($result) && @sizeof($result) != 0) {
		foreach ($result as $row) {
			unset($_SESSION[$row['default_setting_category']]);
		}
		//set the settings as a session
		foreach ($result as $row) {
			$name = $row['default_setting_name'];
			$category = $row['default_setting_category'];
			$subcategory = $row['default_setting_subcategory'];
			if (strlen($subcategory) == 0) {
				if ($name == "array") {
					$_SESSION[$category][] = $row['default_setting_value'];
				}
				else {
					$_SESSION[$category][$name] = $row['default_setting_value'];
				}
			}
			else {
				if ($name == "array") {
					$_SESSION[$category][$subcategory][] = $row['default_setting_value'];
				}
				else {
					$_SESSION[$category][$subcategory]['uuid'] = $row['default_setting_uuid'];
					$_SESSION[$category][$subcategory][$name] = $row['default_setting_value'];
				}
			}
		}
	}
	unset($sql, $result, $row);

	//get the domain UUID
	$sql = "select domain_uuid from v_domains ";
	$sql .= "where domain_name = :domain_name ";
	$parameters['domain_name'] = $domain_name;
	$database = new database;
	$domain_uuid = $database->select($sql, $parameters, 'column');
	unset($sql, $parameters);
	$_SESSION['domain_uuid'] = $domain_uuid;
	$_SESSION['domain_name'] = $domain_name;


	//get the domain settings
	if (is_uuid($domain_uuid)) {
		$sql = "select * from v_domain_settings ";
		$sql .= "where domain_uuid = :domain_uuid ";
		$sql .= "and domain_setting_enabled = 'true' ";
		$sql .= "order by domain_setting_order asc ";
		$parameters['domain_uuid'] = $domain_uuid;
		$database = new database;
		$result = $database->select($sql, $parameters, 'all');
		//unset the arrays that domains are overriding
		if (is_array($result) && @sizeof($result) != 0) {
			foreach ($result as $row) {
				$name = $row['domain_setting_name'];
				$category = $row['domain_setting_category'];
				$subcategory = $row['domain_setting_subcategory'];
				if ($name == "array") {
					unset($_SESSION[$category][$subcategory]);
				}
			}
			//set the settings as a session
			foreach ($result as $row) {
				$name = $row['domain_setting_name'];
				$category = $row['domain_setting_category'];
				$subcategory = $row['domain_setting_subcategory'];
				if (strlen($subcategory) == 0) {
					//$$category[$name] = $row['domain_setting_value'];
					if ($name == "array") {
						$_SESSION[$category][] = $row['domain_setting_value'];
					}
					else {
						$_SESSION[$category][$name] = $row['domain_setting_value'];
					}
				}
				else {
					//$$category[$subcategory][$name] = $row['domain_setting_value'];
					if ($name == "array") {
						$_SESSION[$category][$subcategory][] = $row['domain_setting_value'];
					}
					else {
						$_SESSION[$category][$subcategory][$name] = $row['domain_setting_value'];
					}
				}
			}
		}
		unset($sql, $result, $parameters);
	}
	return $domain_uuid;
}

function base64_url_encode($input) {
	return strtr(base64_encode($input), '+/=', '._-');
}

function base64_url_decode($input) {
	return base64_decode(strtr($input, '._-', '+/='));
}

function send_json(array $settings, $destructive = true, $update = false) {
	global $password_device_uuid;

	//Get last known md5
	if($password_device_uuid) {
		$sql = "SELECT * FROM v_device_settings as s ";
		$sql .= "WHERE device_uuid = :device_uuid ";
		$sql .= "AND device_setting_subcategory = 'json_md5' ";
		$parameters['device_uuid'] = $password_device_uuid;
		$database = new database;
		$md5_setting = $database->select($sql,$parameters,'row');
		unset($sql,$parameters,$database);
		//echo "New md5: ".md5(json_encode($settings))."<br>"."saved md5: ".$md5_setting['device_setting_value']."<br>";
		// generate json_md5
		if (count($settings['sipaccounts']) == 0) {
			// if non-dextructive and sipaccounts empty set update to the requested value.
			$update = $destructive;
		}
		elseif (md5(json_encode($settings)) == $md5_setting['device_setting_value'] ) {
			$update = false;
		}
		else {
			// else if json_md5 is changed, update = true
			$json_md5 = md5(json_encode($settings));
			//save new md5
			unset($sql,$parameters,$database);
			$sql = "UPDATE v_device_settings ";
			$sql .= "SET device_setting_value = :device_setting_value ";
			$sql .= "WHERE device_setting_uuid = :device_setting_uuid ";
			//$parameters['device_uuid'] = $password_device_uuid;
			$parameters['device_setting_uuid'] = $md5_setting['device_setting_uuid'];
			$parameters['device_setting_value'] = $json_md5;
			$database = new database;
			$database->execute($sql, $parameters);
			unset($sql, $parameters);

			$update = true;
		}
		//register that we have seen the device
		$sql = "update v_devices ";
		$sql .= "set device_provisioned_date = :device_provisioned_date, device_provisioned_method = :device_provisioned_method, device_provisioned_ip = :device_provisioned_ip ";
		$sql .= "where domain_uuid = :domain_uuid and device_uuid = :device_uuid ";
		$parameters['domain_uuid'] = $_SESSION['domain_uuid'];
		$parameters['device_uuid'] = $password_device_uuid;
		$parameters['device_provisioned_date'] = date("Y-m-d H:i:s");
		$parameters['device_provisioned_method'] = (isset($_SERVER["HTTPS"]) ? 'https' : 'http');
		$parameters['device_provisioned_ip'] = $_SERVER['REMOTE_ADDR'];
		$database = new database;
		$database->execute($sql, $parameters);
		unset($sql, $parameters);
	}
	else {
		$update = $destructive;
	}


	$settings = array('update' => $update) + $settings;

	header('Content-Type: application/json');
	print_r(json_encode($settings));
	exit;

}



?>