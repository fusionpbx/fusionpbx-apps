<?php
/*
 * FusionPBX
 * Version: MPL 1.1
 *
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is FusionPBX
 *
 * The Initial Developer of the Original Code is
 * Mark J Crane <markjcrane@fusionpbx.com>
 * Copyright (C) 2010 - 2019
 * All Rights Reserved.
 *
 * Contributor(s):
 * Mark J Crane <markjcrane@fusionpbx.com>
 */
include "root.php";

// define the st_device class
	class sessiontalk {

		public $db;
		public $domain_uuid;
		public $device_uuid;
		public $device_vendor_uuid;
		public $device_profile_uuid;
		public $extension;  
		public $settings;
		public $template_dir;

		/**
		 * declare private variables
		 */
		private $app_name;
		private $app_uuid;
		private $credentials;
		private $permission_prefix;
		private $table;
		private $uuid_prefix;
		private $toggle_field;
		private $toggle_values;
		private $domain_name;

		public function __construct() {

			// assign private variables
			$this->app_name = 'sessiontalk';
			$this->app_uuid = '85774108-716c-46cb-a34b-ce80b212bc82';
		}

		public function __destruct() {
			foreach ($this as $key => $value) {
				unset($this->$key);
			}
		}

		public function get_domain_uuid() {
			return $this->domain_uuid;
		}

		public function set_extension($extension_details, $domain_uuid, $domain_name) {
			$this->domain_name = $domain_name;
			$this->domain_uuid = $domain_uuid;
			if (is_array($extension_details)) {
				$this->extension = $extension_details;
				$this->domain_uuid = $domain_uuid;
			} 
			else {

				$sql = "SELECT e.extension_uuid, e.extension, e.description, e.number_alias ";
				$sql .= "FROM v_extensions AS e ";
				$sql .= "WHERE e.domain_uuid = :domain_uuid ";
				$sql .= "AND e.enabled = 'true' ";
				if (preg_match('/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i', $extension_details)) {
					$sql .= "AND e.extension_uuid = :extension_uuid ";
					$parameters['extension_uuid'] = $extension_details;
				} 
				else {
					$sql .= "AND e.extension = :extension ";
					$parameters['extension'] = $extension_details;
				}
				$parameters['domain_uuid'] = $domain_uuid ?: $this->domain_uuid;
				$database = new database();
				$this->extension = $database->select($sql, $parameters, 'row');
				unset($sql, $parameters, $database);
			}
		}

		public function get_credentials() {
			// get the username
			$username = $this->extension['extension'];
			if (isset($this->extension['number_alias']) && strlen($this->extension['number_alias']) > 0) {
				$username = $this->extension['number_alias'];
			}

			// Get the variables
			$key_rotation = $this->settings['key_rotation']['numeric'] * 2;
			$this->credentials['username'] = $username . "@" . $this->domain_name;
			$this->credentials['expiration'] = date("U") + $this->settings['qr_expiration']['numeric'];
			$this->credentials['providerid'] = $this->settings['provider_id']['text'];
			if (isset($this->credentials['providerid']) && strlen($this->credentials['providerid'] > 0)) {
				$this->credentials['providerid'] = ":" . $this->credentials['providerid'];
			}

			// Fetch the active keys for this domain
			$sql = "SELECT * FROM v_sessiontalk_keys ";
			$sql .= "WHERE domain_uuid = :domain_uuid ";
			$parameters['domain_uuid'] = $this->domain_uuid;
			$database = new database();
			$key = $database->select($sql, $parameters, 'row');
			unset($sql, $parameters);

			// check if there is a key
			if (!$key) {
				$key['sessiontalk_key_uuid'] = uuid();
				$key['domain_uuid'] = $this->domain_uuid;
				$key['key1'] = generate_password(32, 3);
				$key['expiration_date'] = date("U") + $key_rotation;
				$key_updated = true;
			} // check if it is time to rotate the key
			elseif ($key['expiration_date'] < $this->credentials['expiration']) {
				$key['key2'] = $key['key1'];
				$key['key1'] = generate_password(32, 3);
				$key['expiration_date'] = date("U") + $key_rotation;
				$key_updated = true;
			}

			// save the new key if modified or created
			if ($key_updated) {

				$array['sessiontalk_keys'][0] = $key;

				$p = new permissions();
				$p->add('sessiontalk_key_add', 'temp');
				$p->add('sessiontalk_key_edit', 'temp');

				// save the data
				$database = new database();
				$database->app_name = 'sessiontalk';
				$database->app_uuid = '85774108-716c-46cb-a34b-ce80b212bc82';
				$database->save($array);
				unset($array);
			}

			// generate the stateless self-expiring password
			$plaintext = $this->credentials['username'] . "@" . $this->credentials['expiration'];

			// Configure openssl
			$cipher = "AES-128-CBC";
			$iv_length = openssl_cipher_iv_length($cipher);

			$iv = random_bytes($iv_length);
			$password = openssl_encrypt($plaintext, $cipher, $key['key1'], $options = 0, $iv);
			$this->credentials['password'] = base64_url_encode($iv . $password);

			// $password_decoded = base64_url_decode($qr['password']);
			// $iv_decoded = substr($password_decoded, 0, 16);
			// $password_split = substr($password_decoded, 16);
			// $original_plaintext = openssl_decrypt($password_split, $cipher, $key['key1'], $options = 0, $iv_decoded);

			$this->credentials['mobile'] = "scsc:" . $this->credentials['username'] . ":" . $this->credentials['password'];
			if (strlen($this->credentials['providerid']) > 0) {
				$this->credentials['mobile'] .= ":" . $this->credentials['providerid'];
			}
			$this->credentials['windows'] = "ms-appinstaller:?source=";
			$this->credentials['windows'] .= $this->settings['windows_softphone_url']['text'];
			$this->credentials['windows'] .= "&activationUri=scsc:?username=";
			$this->credentials['windows'] .= $this->credentials['username'];
			if (strlen($this->credentials['providerid']) > 0) {
				$this->credentials['windows'] .= ":" . $this->credentials['providerid'];
			}
			$this->credentials['windows'] .= "%26password=";
			$this->credentials['windows'] .= $this->credentials['password'];
			$this->credentials['qr_image'] = $this->render_qr();
			return $this->credentials;
		}

		public function render_qr() {
			//stream the file
			$qr_content = html_entity_decode( $this->credentials['mobile'], ENT_QUOTES, 'UTF-8' );
			
			require_once 'resources/qr_code/QRErrorCorrectLevel.php';
			require_once 'resources/qr_code/QRCode.php';
			require_once 'resources/qr_code/QRCodeImage.php';

			try {
				$code = new QRCode (- 1, QRErrorCorrectLevel::H);
				$code->addData($qr_content);
				$code->make();
				
				$img = new QRCodeImage ($code, 420, 420, 50);
				$img->draw();
				$image = $img->getImage();
				$img->finish();
			}
			catch (Exception $error) {
				return $error;
			}
			return $image;
		}

		public function get_activations() {
			// Count Devices for this extension
			// Not a perfect method, if you have manually added the same line to multiple devices it is still counted.
			// Also if you add the same line multiple times to a single device for some reason it will still be counted.
			$sql = "SELECT count(*) FROM v_devices as d ";
			$sql .= "JOIN v_device_lines as l ON d.device_uuid = l.device_uuid ";
			$sql .= "WHERE l.user_id = :extension ";
			$sql .= "AND l.server_address = :domain_name ";
			$sql .= "AND d.device_vendor = 'sessiontalk' ";
			$sql .= "AND l.enabled = 'true' ";
			$parameters['extension'] = $this->extension['extension'];
			$parameters['domain_name'] = $this->domain_name;
			$database = new database();
			$activations = $database->select($sql, $parameters, 'column');
			unset($sql, $parameters);
			return $activations;
		}

		public static function get_vendor($mac) {
			// use the mac address to find the vendor
			$mac = preg_replace('#[^a-fA-F0-9./]#', '', $mac);
			$mac = strtolower($mac);
			switch (substr($mac, 0, 6)) {
				case "00085d":
					$device_vendor = "aastra";
					break;
				case "001873":
					$device_vendor = "cisco";
					break;
				case "a44c11":
					$device_vendor = "cisco";
					break;
				case "0021A0":
					$device_vendor = "cisco";
					break;
				case "30e4db":
					$device_vendor = "cisco";
					break;
				case "002155":
					$device_vendor = "cisco";
					break;
				case "68efbd":
					$device_vendor = "cisco";
					break;
				case "000b82":
					$device_vendor = "grandstream";
					break;
				case "00177d":
					$device_vendor = "konftel";
					break;
				case "00045a":
					$device_vendor = "linksys";
					break;
				case "000625":
					$device_vendor = "linksys";
					break;
				case "000e08":
					$device_vendor = "linksys";
					break;
				case "08000f":
					$device_vendor = "mitel";
					break;
				case "0080f0":
					$device_vendor = "panasonic";
					break;
				case "0004f2":
					$device_vendor = "polycom";
					break;
				case "00907a":
					$device_vendor = "polycom";
					break;
				case "64167f":
					$device_vendor = "polycom";
					break;
				case "000413":
					$device_vendor = "snom";
					break;
				case "001565":
					$device_vendor = "yealink";
					break;
				case "805ec0":
					$device_vendor = "yealink";
					break;
				case "00268B":
					$device_vendor = "escene";
					break;
				case "001fc1":
					$device_vendor = "htek";
					break;
				case "0C383E":
					$device_vendor = "fanvil";
					break;
				case "7c2f80":
					$device_vendor = "gigaset";
					break;
				case "14b370":
					$device_vendor = "gigaset";
					break;
				case "002104":
					$device_vendor = "gigaset";
					break;
				case "bcc342":
					$device_vendor = "panasonic";
					break;
				case "080023":
					$device_vendor = "panasonic";
					break;
				case "0080f0":
					$device_vendor = "panasonic";
					break;
				default:
					$device_vendor = "";
			}
			return $device_vendor;
		}

		public function get_template_dir() {
			// set the default template directory
			if (PHP_OS == "Linux") {
				// set the default template dir
				if (strlen($this->template_dir) == 0) {
					if (file_exists('/etc/fusionpbx/resources/templates/provision')) {
						$this->template_dir = '/etc/fusionpbx/resources/templates/provision';
					} else {
						$this->template_dir = $_SERVER["DOCUMENT_ROOT"] . PROJECT_PATH . '/resources/templates/provision';
					}
				}
			} elseif (PHP_OS == "FreeBSD") {
				// if the FreeBSD port is installed use the following paths by default.
				if (file_exists('/usr/local/etc/fusionpbx/resources/templates/provision')) {
					if (strlen($this->template_dir) == 0) {
						$this->template_dir = '/usr/local/etc/fusionpbx/resources/templates/provision';
					} else {
						$this->template_dir = $_SERVER["DOCUMENT_ROOT"] . PROJECT_PATH . '/resources/templates/provision';
					}
				} else {
					if (strlen($this->template_dir) == 0) {
						$this->template_dir = $_SERVER["DOCUMENT_ROOT"] . PROJECT_PATH . '/resources/templates/provision';
					} else {
						$this->template_dir = $_SERVER["DOCUMENT_ROOT"] . PROJECT_PATH . '/resources/templates/provision';
					}
				}
			} elseif (PHP_OS == "NetBSD") {
				// set the default template_dir
				if (strlen($this->template_dir) == 0) {
					$this->template_dir = $_SERVER["DOCUMENT_ROOT"] . PROJECT_PATH . '/resources/templates/provision';
				}
			} elseif (PHP_OS == "OpenBSD") {
				// set the default template_dir
				if (strlen($this->template_dir) == 0) {
					$this->template_dir = $_SERVER["DOCUMENT_ROOT"] . PROJECT_PATH . '/resources/templates/provision';
				}
			} else {
				// set the default template_dir
				if (strlen($this->template_dir) == 0) {
					$this->template_dir = $_SERVER["DOCUMENT_ROOT"] . PROJECT_PATH . '/resources/templates/provision';
				}
			}

			// check to see if the domain name sub directory exists
			if (is_dir($this->template_dir . "/" . $_SESSION["domain_name"])) {
				$this->template_dir = $this->template_dir . "/" . $_SESSION["domain_name"];
			}

			// return the template directory
			return $this->template_dir;
		}

		/**
		 * delete records
		 */
		public function delete($records) {

			// assign private variables
			$this->permission_prefix = 'device_';
			$this->list_page = 'devices.php';
			$this->table = 'devices';
			$this->uuid_prefix = 'device_';

			if (permission_exists($this->permission_prefix . 'delete')) {

				// add multi-lingual support
				$language = new text();
				$text = $language->get();

				// validate the token
				// $token = new token;
				// if (!$token->validate($_SERVER['PHP_SELF'])) {
				// message::add($text['message-invalid_token'],'negative');
				// header('Location: '.$this->list_page);
				// exit;
				// }

				// delete multiple records

				if (is_array($records) && @sizeof($records) != 0) {

					// build the delete array
					foreach ($records as $x => $record) {
						if ($record['checked'] == 'true' && is_uuid($record['uuid'])) {
							$array[$this->table][$x][$this->uuid_prefix . 'uuid'] = $record['uuid'];
							$array['device_settings'][$x]['device_uuid'] = $record['uuid'];
							$array['device_lines'][$x]['device_uuid'] = $record['uuid'];
							$array['device_keys'][$x]['device_uuid'] = $record['uuid'];
						}
					}

					// delete the checked rows
					if (is_array($array) && @sizeof($array) != 0) {

						// grant temporary permissions
						$p = new permissions();
						$p->add('device_setting_delete', 'temp');
						$p->add('device_line_delete', 'temp');
						$p->add('device_key_delete', 'temp');

						// execute delete
						$database = new database();
						$database->app_name = $this->app_name;
						$database->app_uuid = $this->app_uuid;
						$database->delete($array);
						unset($array);

						// revoke temporary permissions
						$p->delete('device_setting_delete', 'temp');
						$p->delete('device_line_delete', 'temp');
						$p->delete('device_key_delete', 'temp');

						// write the provision files
						if (strlen($_SESSION['provision']['path']['text']) > 0) {
							$prov = new provision();
							$prov->domain_uuid = $_SESSION['domain_uuid'];
							$response = $prov->write();
						}

						// set message
						message::add($text['message-delete']);
					}
					unset($records);
				}
			}
		}

		public function delete_lines($records) {
			// assign private variables
			$this->permission_prefix = 'device_line_';
			$this->table = 'device_lines';
			$this->uuid_prefix = 'device_line_';

			if (permission_exists($this->permission_prefix . 'delete')) {

				// add multi-lingual support
				$language = new text();
				$text = $language->get();

				// validate the token
				// $token = new token;
				// if (!$token->validate($_SERVER['PHP_SELF'])) {
				// message::add($text['message-invalid_token'],'negative');
				// header('Location: '.$this->list_page);
				// exit;
				// }

				// delete multiple records
				if (is_array($records) && @sizeof($records) != 0) {

					// filter out unchecked st_device lines, build delete array
					$x = 0;
					foreach ($records as $record) {
						if ($record['checked'] == 'true' && is_uuid($record['uuid'])) {
							$array[$this->table][$x][$this->uuid_prefix . 'uuid'] = $record['uuid'];
							$array[$this->table][$x]['device_uuid'] = $this->device_uuid;
							$x ++;
						}
					}

					// delete the checked rows
					if (is_array($array) && @sizeof($array) != 0) {
						// execute delete
						$database = new database();
						$database->app_name = $this->app_name;
						$database->app_uuid = $this->app_uuid;
						$database->delete($array);
						unset($array);
					}
					unset($records);
				}
			}
		}

		public function delete_keys($records) {
			// assign private variables
			$this->permission_prefix = 'device_key_';
			$this->table = 'device_keys';
			$this->uuid_prefix = 'device_key_';

			if (permission_exists($this->permission_prefix . 'delete')) {

				// add multi-lingual support
				$language = new text();
				$text = $language->get();

				// validate the token
				// $token = new token;
				// if (!$token->validate($_SERVER['PHP_SELF'])) {
				// message::add($text['message-invalid_token'],'negative');
				// header('Location: '.$this->list_page);
				// exit;
				// }

				// delete multiple records
				if (is_array($records) && @sizeof($records) != 0) {

					// filter out unchecked st_device keys, build delete array
					$x = 0;
					foreach ($records as $record) {
						if ($record['checked'] == 'true' && is_uuid($record['uuid'])) {
							$array[$this->table][$x][$this->uuid_prefix . 'uuid'] = $record['uuid'];
							$array[$this->table][$x]['device_uuid'] = $this->device_uuid;
							$x ++;
						}
					}

					// delete the checked rows
					if (is_array($array) && @sizeof($array) != 0) {
						// execute delete
						$database = new database();
						$database->app_name = $this->app_name;
						$database->app_uuid = $this->app_uuid;
						$database->delete($array);
						unset($array);
					}
					unset($records);
				}
			}
		}

		public function delete_settings($records) {
			// assign private variables
			$this->permission_prefix = 'device_setting_';
			$this->table = 'device_settings';
			$this->uuid_prefix = 'device_setting_';

			if (permission_exists($this->permission_prefix . 'delete')) {

				// add multi-lingual support
				$language = new text();
				$text = $language->get();

				// validate the token
				// $token = new token;
				// if (!$token->validate($_SERVER['PHP_SELF'])) {
				// message::add($text['message-invalid_token'],'negative');
				// header('Location: '.$this->list_page);
				// exit;
				// }

				// delete multiple records
				if (is_array($records) && @sizeof($records) != 0) {

					// filter out unchecked st_device settings, build delete array
					$x = 0;
					foreach ($records as $record) {
						if ($record['checked'] == 'true' && is_uuid($record['uuid'])) {
							$array[$this->table][$x][$this->uuid_prefix . 'uuid'] = $record['uuid'];
							$array[$this->table][$x]['device_uuid'] = $this->device_uuid;
							$x ++;
						}
					}

					// delete the checked rows
					if (is_array($array) && @sizeof($array) != 0) {
						// execute delete
						$database = new database();
						$database->app_name = $this->app_name;
						$database->app_uuid = $this->app_uuid;
						$database->delete($array);
						unset($array);
					}
					unset($records);
				}
			}
		}

		public function delete_vendors($records) {

			// assign private variables
			$this->permission_prefix = 'device_vendor_';
			$this->list_page = 'device_vendors.php';
			$this->tables[] = 'device_vendors';
			$this->tables[] = 'device_vendor_functions';
			$this->tables[] = 'device_vendor_function_groups';
			$this->uuid_prefix = 'device_vendor_';

			if (permission_exists($this->permission_prefix . 'delete')) {

				// add multi-lingual support
				$language = new text();
				$text = $language->get();

				// validate the token
				// $token = new token;
				// if (!$token->validate($_SERVER['PHP_SELF'])) {
				// message::add($text['message-invalid_token'],'negative');
				// header('Location: '.$this->list_page);
				// exit;
				// }

				// delete multiple records
				if (is_array($records) && @sizeof($records) != 0) {

					// build the delete array
					foreach ($records as $x => $record) {
						if ($record['checked'] == 'true' && is_uuid($record['uuid'])) {
							foreach ($this->tables as $table) {
								$array[$table][$x][$this->uuid_prefix . 'uuid'] = $record['uuid'];
							}
						}
					}

					// delete the checked rows
					if (is_array($array) && @sizeof($array) != 0) {

						// grant temporary permissions
						$p = new permissions();
						$p->add('device_vendor_function_delete', 'temp');
						$p->add('device_vendor_function_group_delete', 'temp');

						// execute delete
						$database = new database();
						$database->app_name = $this->app_name;
						$database->app_uuid = $this->app_uuid;
						$database->delete($array);
						unset($array);

						// revoke temporary permissions
						$p->delete('device_vendor_function_delete', 'temp');
						$p->delete('device_vendor_function_group_delete', 'temp');

						// set message
						message::add($text['message-delete']);
					}
					unset($records);
				}
			}
		}

		public function delete_vendor_functions($records) {

			// assign private variables
			$this->permission_prefix = 'device_vendor_function_';
			$this->list_page = 'device_vendor_edit.php';
			$this->tables[] = 'device_vendor_functions';
			$this->tables[] = 'device_vendor_function_groups';
			$this->uuid_prefix = 'device_vendor_function_';

			if (permission_exists($this->permission_prefix . 'delete')) {

				// add multi-lingual support
				$language = new text();
				$text = $language->get();

				// validate the token
				// $token = new token;
				// if (!$token->validate('/app/devices/device_vendor_functions.php')) {
				// message::add($text['message-invalid_token'],'negative');
				// header('Location: '.$this->list_page.'?id='.$this->device_vendor_uuid);
				// exit;
				// }

				// delete multiple records
				if (is_array($records) && @sizeof($records) != 0) {

					// build the delete array
					foreach ($records as $x => $record) {
						if ($record['checked'] == 'true' && is_uuid($record['uuid'])) {
							foreach ($this->tables as $table) {
								$array[$table][$x][$this->uuid_prefix . 'uuid'] = $record['uuid'];
							}
						}
					}

					// delete the checked rows
					if (is_array($array) && @sizeof($array) != 0) {

						// grant temporary permissions
						$p = new permissions();
						$p->add('device_vendor_function_group_delete', 'temp');

						// execute delete
						$database = new database();
						$database->app_name = $this->app_name;
						$database->app_uuid = $this->app_uuid;
						$database->delete($array);
						unset($array);

						// revoke temporary permissions
						$p->delete('device_vendor_function_group_delete', 'temp');

						// set message
						message::add($text['message-delete']);
					}
					unset($records);
				}
			}
		}

		public function delete_profiles($records) {

			// assign private variables
			$this->permission_prefix = 'device_profile_';
			$this->list_page = 'device_profiles.php';
			$this->tables[] = 'device_profiles';
			$this->tables[] = 'device_profile_keys';
			$this->tables[] = 'device_profile_settings';
			$this->uuid_prefix = 'device_profile_';

			if (permission_exists($this->permission_prefix . 'delete')) {

				// add multi-lingual support
				$language = new text();
				$text = $language->get();

				// validate the token
				// $token = new token;
				// if (!$token->validate($_SERVER['PHP_SELF'])) {
				// message::add($text['message-invalid_token'],'negative');
				// header('Location: '.$this->list_page);
				// exit;
				// }

				// delete multiple records
				if (is_array($records) && @sizeof($records) != 0) {

					// build the delete array
					foreach ($records as $x => $record) {
						if ($record['checked'] == 'true' && is_uuid($record['uuid'])) {
							foreach ($this->tables as $table) {
								$array[$table][$x][$this->uuid_prefix . 'uuid'] = $record['uuid'];
							}
						}
					}

					// delete the checked rows
					if (is_array($array) && @sizeof($array) != 0) {

						// grant temporary permissions
						$p = new permissions();
						$p->add('device_profile_key_delete', 'temp');
						$p->add('device_profile_setting_delete', 'temp');

						// execute delete
						$database = new database();
						$database->app_name = $this->app_name;
						$database->app_uuid = $this->app_uuid;
						$database->delete($array);
						unset($array);

						// revoke temporary permissions
						$p->delete('device_profile_key_delete', 'temp');
						$p->delete('device_profile_setting_delete', 'temp');

						// set message
						message::add($text['message-delete']);
					}
					unset($records);
				}
			}
		}

		public function delete_profile_keys($records) {

			// assign private variables
			$this->permission_prefix = 'device_profile_key_';
			$this->list_page = 'device_profile_edit.php?id=' . $this->device_profile_uuid;
			$this->table = 'device_profile_keys';
			$this->uuid_prefix = 'device_profile_key_';

			if (permission_exists($this->permission_prefix . 'delete')) {

				// add multi-lingual support
				$language = new text();
				$text = $language->get();

				// validate the token
				// $token = new token;
				// if (!$token->validate($_SERVER['PHP_SELF'])) {
				// message::add($text['message-invalid_token'],'negative');
				// header('Location: '.$this->list_page);
				// exit;
				// }

				// delete multiple records
				if (is_array($records) && @sizeof($records) != 0) {

					// build the delete array
					foreach ($records as $x => $record) {
						if ($record['checked'] == 'true' && is_uuid($record['uuid'])) {
							$array[$this->table][$x][$this->uuid_prefix . 'uuid'] = $record['uuid'];
						}
					}

					// execute delete
					if (is_array($array) && @sizeof($array) != 0) {
						$database = new database();
						$database->app_name = $this->app_name;
						$database->app_uuid = $this->app_uuid;
						$database->delete($array);
						unset($array);
					}
					unset($records);
				}
			}
		}

		public function delete_profile_settings($records) {

			// assign private variables
			$this->permission_prefix = 'device_profile_setting_';
			$this->list_page = 'device_profile_edit.php?id=' . $this->device_profile_uuid;
			$this->table = 'device_profile_settings';
			$this->uuid_prefix = 'device_profile_setting_';

			if (permission_exists($this->permission_prefix . 'delete')) {

				// add multi-lingual support
				$language = new text();
				$text = $language->get();

				// validate the token
				// $token = new token;
				// if (!$token->validate($_SERVER['PHP_SELF'])) {
				// message::add($text['message-invalid_token'],'negative');
				// header('Location: '.$this->list_page);
				// exit;
				// }

				// delete multiple records
				if (is_array($records) && @sizeof($records) != 0) {

					// build the delete array
					foreach ($records as $x => $record) {
						if ($record['checked'] == 'true' && is_uuid($record['uuid'])) {
							$array[$this->table][$x][$this->uuid_prefix . 'uuid'] = $record['uuid'];
						}
					}

					// execute delete
					if (is_array($array) && @sizeof($array) != 0) {
						$database = new database();
						$database->app_name = $this->app_name;
						$database->app_uuid = $this->app_uuid;
						$database->delete($array);
						unset($array);
					}
					unset($records);
				}
			}
		}

		/**
		 * toggle records
		 */
		public function toggle($records) {

			// assign private variables
			$this->permission_prefix = 'device_';
			$this->list_page = 'devices.php';
			$this->table = 'devices';
			$this->uuid_prefix = 'device_';
			$this->toggle_field = 'device_enabled';
			$this->toggle_values = [
				'true',
				'false'
			];

			if (permission_exists($this->permission_prefix . 'edit')) {

				// add multi-lingual support
				$language = new text();
				$text = $language->get();

				// validate the token
				// $token = new token;
				// if (!$token->validate($_SERVER['PHP_SELF'])) {
				// message::add($text['message-invalid_token'],'negative');
				// header('Location: '.$this->list_page);
				// exit;
				// }

				// toggle the checked records
				if (is_array($records) && @sizeof($records) != 0) {

					// get current toggle state
					foreach ($records as $x => $record) {
						if ($record['checked'] == 'true' && is_uuid($record['uuid'])) {
							$uuids[] = "'" . $record['uuid'] . "'";
						}
					}
					if (is_array($uuids) && @sizeof($uuids) != 0) {
						$sql = "select " . $this->uuid_prefix . "uuid as uuid, " . $this->toggle_field . " as toggle from v_" . $this->table . " ";
						$sql .= "where (domain_uuid = :domain_uuid or domain_uuid is null) ";
						$sql .= "and " . $this->uuid_prefix . "uuid in (" . implode(', ', $uuids) . ") ";
						$parameters['domain_uuid'] = $_SESSION['domain_uuid'];
						$database = new database();
						$rows = $database->select($sql, $parameters, 'all');
						if (is_array($rows) && @sizeof($rows) != 0) {
							foreach ($rows as $row) {
								$states[$row['uuid']] = $row['toggle'];
							}
						}
						unset($sql, $parameters, $rows, $row);
					}

					// build update array
					$x = 0;
					foreach ($states as $uuid => $state) {
						$array[$this->table][$x][$this->uuid_prefix . 'uuid'] = $uuid;
						$array[$this->table][$x][$this->toggle_field] = $state == $this->toggle_values[0] ? $this->toggle_values[1] : $this->toggle_values[0];
						$x ++;
					}

					// save the changes
					if (is_array($array) && @sizeof($array) != 0) {

						// save the array
						$database = new database();
						$database->app_name = $this->app_name;
						$database->app_uuid = $this->app_uuid;
						$database->save($array);
						unset($array);

						// write the provision files
						if (strlen($_SESSION['provision']['path']['text']) > 0) {
							$prov = new provision();
							$prov->domain_uuid = $_SESSION['domain_uuid'];
							$response = $prov->write();
						}

						// set message
						message::add($text['message-toggle']);
					}
					unset($records, $states);
				}
			}
		}

		public function toggle_vendors($records) {

			// assign private variables
			$this->permission_prefix = 'device_vendor_';
			$this->list_page = 'device_vendors.php';
			$this->table = 'device_vendors';
			$this->uuid_prefix = 'device_vendor_';
			$this->toggle_field = 'enabled';
			$this->toggle_values = [
				'true',
				'false'
			];

			if (permission_exists($this->permission_prefix . 'edit')) {

				// add multi-lingual support
				$language = new text();
				$text = $language->get();

				// validate the token
				// $token = new token;
				// if (!$token->validate($_SERVER['PHP_SELF'])) {
				// message::add($text['message-invalid_token'],'negative');
				// header('Location: '.$this->list_page);
				// exit;
				// }

				// toggle the checked records
				if (is_array($records) && @sizeof($records) != 0) {

					// get current toggle state
					foreach ($records as $x => $record) {
						if ($record['checked'] == 'true' && is_uuid($record['uuid'])) {
							$uuids[] = "'" . $record['uuid'] . "'";
						}
					}
					if (is_array($uuids) && @sizeof($uuids) != 0) {
						$sql = "select " . $this->uuid_prefix . "uuid as uuid, " . $this->toggle_field . " as toggle from v_" . $this->table . " ";
						$sql .= "where " . $this->uuid_prefix . "uuid in (" . implode(', ', $uuids) . ") ";
						$database = new database();
						$rows = $database->select($sql, '', 'all');
						if (is_array($rows) && @sizeof($rows) != 0) {
							foreach ($rows as $row) {
								$states[$row['uuid']] = $row['toggle'];
							}
						}
						unset($sql, $parameters, $rows, $row);
					}

					// build update array
					$x = 0;
					foreach ($states as $uuid => $state) {
						$array[$this->table][$x][$this->uuid_prefix . 'uuid'] = $uuid;
						$array[$this->table][$x][$this->toggle_field] = $state == $this->toggle_values[0] ? $this->toggle_values[1] : $this->toggle_values[0];
						$x ++;
					}

					// save the changes
					if (is_array($array) && @sizeof($array) != 0) {

						// save the array
						$database = new database();
						$database->app_name = $this->app_name;
						$database->app_uuid = $this->app_uuid;
						$database->save($array);
						unset($array);

						// set message
						message::add($text['message-toggle']);
					}
					unset($records, $states);
				}
			}
		}

		public function toggle_vendor_functions($records) {

			// assign private variables
			$this->permission_prefix = 'device_vendor_function_';
			$this->list_page = 'device_vendor_edit.php';
			$this->table = 'device_vendor_functions';
			$this->uuid_prefix = 'device_vendor_function_';
			$this->toggle_field = 'enabled';
			$this->toggle_values = [
				'true',
				'false'
			];

			if (permission_exists($this->permission_prefix . 'edit')) {

				// add multi-lingual support
				$language = new text();
				$text = $language->get();

				// validate the token
				// $token = new token;
				// if (!$token->validate('/app/devices/device_vendor_functions.php')) {
				// message::add($text['message-invalid_token'],'negative');
				// header('Location: '.$this->list_page.'?id='.$this->device_vendor_uuid);
				// exit;
				// }

				// toggle the checked records
				if (is_array($records) && @sizeof($records) != 0) {

					// get current toggle state
					foreach ($records as $x => $record) {
						if ($record['checked'] == 'true' && is_uuid($record['uuid'])) {
							$uuids[] = "'" . $record['uuid'] . "'";
						}
					}
					if (is_array($uuids) && @sizeof($uuids) != 0) {
						$sql = "select " . $this->uuid_prefix . "uuid as uuid, " . $this->toggle_field . " as toggle from v_" . $this->table . " ";
						$sql .= "where " . $this->uuid_prefix . "uuid in (" . implode(', ', $uuids) . ") ";
						$database = new database();
						$rows = $database->select($sql, '', 'all');
						if (is_array($rows) && @sizeof($rows) != 0) {
							foreach ($rows as $row) {
								$states[$row['uuid']] = $row['toggle'];
							}
						}
						unset($sql, $parameters, $rows, $row);
					}

					// build update array
					$x = 0;
					foreach ($states as $uuid => $state) {
						$array[$this->table][$x][$this->uuid_prefix . 'uuid'] = $uuid;
						$array[$this->table][$x][$this->toggle_field] = $state == $this->toggle_values[0] ? $this->toggle_values[1] : $this->toggle_values[0];
						$x ++;
					}

					// save the changes
					if (is_array($array) && @sizeof($array) != 0) {

						// save the array
						$database = new database();
						$database->app_name = $this->app_name;
						$database->app_uuid = $this->app_uuid;
						$database->save($array);
						unset($array);

						// set message
						message::add($text['message-toggle']);
					}
					unset($records, $states);
				}
			}
		}

		public function toggle_profiles($records) {

			// assign private variables
			$this->permission_prefix = 'device_profile_';
			$this->list_page = 'device_profiles.php';
			$this->table = 'device_profiles';
			$this->uuid_prefix = 'device_profile_';
			$this->toggle_field = 'device_profile_enabled';
			$this->toggle_values = [
				'true',
				'false'
			];

			if (permission_exists($this->permission_prefix . 'edit')) {

				// add multi-lingual support
				$language = new text();
				$text = $language->get();

				// validate the token
				// $token = new token;
				// if (!$token->validate($_SERVER['PHP_SELF'])) {
				// message::add($text['message-invalid_token'],'negative');
				// header('Location: '.$this->list_page);
				// exit;
				// }

				// toggle the checked records
				if (is_array($records) && @sizeof($records) != 0) {

					// get current toggle state
					foreach ($records as $x => $record) {
						if ($record['checked'] == 'true' && is_uuid($record['uuid'])) {
							$uuids[] = "'" . $record['uuid'] . "'";
						}
					}
					if (is_array($uuids) && @sizeof($uuids) != 0) {
						$sql = "select " . $this->uuid_prefix . "uuid as uuid, " . $this->toggle_field . " as toggle from v_" . $this->table . " ";
						$sql .= "where " . $this->uuid_prefix . "uuid in (" . implode(', ', $uuids) . ") ";
						$database = new database();
						$rows = $database->select($sql, '', 'all');
						if (is_array($rows) && @sizeof($rows) != 0) {
							foreach ($rows as $row) {
								$states[$row['uuid']] = $row['toggle'];
							}
						}
						unset($sql, $parameters, $rows, $row);
					}

					// build update array
					$x = 0;
					foreach ($states as $uuid => $state) {
						$array[$this->table][$x][$this->uuid_prefix . 'uuid'] = $uuid;
						$array[$this->table][$x][$this->toggle_field] = $state == $this->toggle_values[0] ? $this->toggle_values[1] : $this->toggle_values[0];
						$x ++;
					}

					// save the changes
					if (is_array($array) && @sizeof($array) != 0) {

						// save the array
						$database = new database();
						$database->app_name = $this->app_name;
						$database->app_uuid = $this->app_uuid;
						$database->save($array);
						unset($array);

						// set message
						message::add($text['message-toggle']);
					}
					unset($records, $states);
				}
			}
		}

		/**
		 * copy records
		 */
		public function copy_profiles($records) {

			// assign private variables
			$this->permission_prefix = 'device_profile_';
			$this->list_page = 'device_profiles.php';
			$this->table = 'device_profiles';
			$this->uuid_prefix = 'device_profile_';

			if (permission_exists($this->permission_prefix . 'add')) {

				// add multi-lingual support
				$language = new text();
				$text = $language->get();

				// validate the token
				// $token = new token;
				// if (!$token->validate($_SERVER['PHP_SELF'])) {
				// message::add($text['message-invalid_token'],'negative');
				// header('Location: '.$this->list_page);
				// exit;
				// }

				// copy the checked records
				if (is_array($records) && @sizeof($records) != 0) {

					// get checked records
					foreach ($records as $x => $record) {
						if ($record['checked'] == 'true' && is_uuid($record['uuid'])) {
							$uuids[] = "'" . $record['uuid'] . "'";
						}
					}

					// create insert array from existing data
					if (is_array($uuids) && @sizeof($uuids) != 0) {
						$sql = "select * from v_" . $this->table . " ";
						$sql .= "where (domain_uuid = :domain_uuid or domain_uuid is null) ";
						$sql .= "and " . $this->uuid_prefix . "uuid in (" . implode(', ', $uuids) . ") ";
						$parameters['domain_uuid'] = $_SESSION['domain_uuid'];
						$database = new database();
						$rows = $database->select($sql, $parameters, 'all');
						if (is_array($rows) && @sizeof($rows) != 0) {
							$y = $z = 0;
							foreach ($rows as $x => $row) {
								$primary_uuid = uuid();

								// copy data
								$array[$this->table][$x] = $row;

								// overwrite
								$array[$this->table][$x][$this->uuid_prefix . 'uuid'] = $primary_uuid;
								$array[$this->table][$x]['device_profile_description'] = trim($row['device_profile_description'] . ' (' . $text['label-copy'] . ')');

								// keys sub table
								$sql_2 = "select * from v_device_profile_keys ";
								$sql_2 .= "where device_profile_uuid = :device_profile_uuid ";
								$sql_2 .= "order by ";
								$sql_2 .= "case profile_key_category ";
								$sql_2 .= "when 'line' then 1 ";
								$sql_2 .= "when 'memort' then 2 ";
								$sql_2 .= "when 'programmable' then 3 ";
								$sql_2 .= "when 'expansion' then 4 ";
								$sql_2 .= "else 100 end, ";
								$sql_2 .= "profile_key_id asc ";
								$parameters_2['device_profile_uuid'] = $row['device_profile_uuid'];
								$database = new database();
								$rows_2 = $database->select($sql_2, $parameters_2, 'all');
								if (is_array($rows_2) && @sizeof($rows_2) != 0) {
									foreach ($rows_2 as $row_2) {

										// copy data
										$array['device_profile_keys'][$y] = $row_2;

										// overwrite
										$array['device_profile_keys'][$y]['device_profile_key_uuid'] = uuid();
										$array['device_profile_keys'][$y]['device_profile_uuid'] = $primary_uuid;

										// increment
										$y ++;
									}
								}
								unset($sql_2, $parameters_2, $rows_2, $row_2);

								// settings sub table
								$sql_3 = "select * from v_device_profile_settings where device_profile_uuid = :device_profile_uuid";
								$parameters_3['device_profile_uuid'] = $row['device_profile_uuid'];
								$database = new database();
								$rows_3 = $database->select($sql_3, $parameters_3, 'all');
								if (is_array($rows_3) && @sizeof($rows_3) != 0) {
									foreach ($rows_3 as $row_3) {

										// copy data
										$array['device_profile_settings'][$z] = $row_3;

										// overwrite
										$array['device_profile_settings'][$z]['device_profile_setting_uuid'] = uuid();
										$array['device_profile_settings'][$z]['device_profile_uuid'] = $primary_uuid;

										// increment
										$z ++;
									}
								}
								unset($sql_3, $parameters_3, $rows_3, $row_3);
							}
						}
						unset($sql, $parameters, $rows, $row);
					}

					// save the changes and set the message
					if (is_array($array) && @sizeof($array) != 0) {

						// grant temporary permissions
						$p = new permissions();
						$p->add('device_profile_key_add', 'temp');
						$p->add('device_profile_setting_add', 'temp');

						// save the array
						$database = new database();
						$database->app_name = $this->app_name;
						$database->app_uuid = $this->app_uuid;
						$database->save($array);
						unset($array);

						// revoke temporary permissions
						$p->delete('device_profile_key_add', 'temp');
						$p->delete('device_profile_setting_add', 'temp');

						// set message
						message::add($text['message-copy']);
					}
					unset($records);
				}
			}
		} // method
	} // class

?>
