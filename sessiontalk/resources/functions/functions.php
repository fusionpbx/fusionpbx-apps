<?php

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