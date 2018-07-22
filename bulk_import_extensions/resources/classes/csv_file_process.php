<?php
if (!class_exists('csv_file_process')) {
    class csv_file_process {
        
        private $csv_file;
        private $csv_fields_order;

        
        private $is_generate_vm_pass;
        private $is_generate_ext_pass;
        private $is_add_device_profile;
        private $is_add_extension_user;
        private $is_add_device;

        private $vm_password_length;

        private $db;
        private $domain_uuid;
        private $domain_name;
        private $line_sip_transport;
        private $line_sip_port;
        private $line_register_expires;

        public function __construct($file_path) {
            if (!file_exists($file_path)) {
                $this->csv_file = False;
                return;
            }

            $this->is_import_devices = False;
            $this->csv_fields_order = False;
            $this->is_generate_vm_pass = False;
            $this->is_generate_ext_pass = False;
            $this->is_is_add_device_profile = False;

            //$this->numerical_fields = ['number_alias', 'call_timeout', 'voicemail_id'];

            $this->csv_file = new SplFileObject($file_path);
            
            // Guessing CSV delimiter

            if (count($this->csv_file->fgetcsv()) != 1) {
                return;
            }

            // Trying ';'
            $this->csv_file->rewind();
            $this->csv_file->setCsvControl(";");
            if (count($this->csv_file->fgetcsv()) != 1) {
                return;
            }

            // Trying 'tab'
            $this->csv_file->rewind();
            $this->csv_file->setCsvControl("\t");
            if (count($this->csv_file->fgetcsv()) != 1) {
                return;
            }

            // Trying 'space'
            $this->csv_file->rewind();
            $this->csv_file->setCsvControl(" ");
            if (count($this->csv_file->fgetcsv()) != 1) {
                return;
            }
            // Trying ':'
            $this->csv_file->rewind();
            $this->csv_file->setCsvControl(":");
            if (count($this->csv_file->fgetcsv()) != 1) {
                return;
            }
            // Cannot get csv file delimiter. Unsetting file
            unset($this->csv_file);
        }

        public function __destruct() {
            unset($this->csv_file);
        }

        // Creation part end
        // Private functions start


        private function starts_with($haystack, $needle) {
            return strncmp($haystack, $needle, strlen($needle)) === 0;
        }

        private function ends_with($haystack, $needle) {
            return $needle === '' || substr_compare($haystack, $needle, -strlen($needle)) === 0;
        }

        // Funcion to add missing fields if any and check if some of actual fields are correct.
        private function normalize_line($csv_line) {

            $result = array();

            foreach ($this->csv_fields_order as $index => $value) {
                $csv_field = (isset($csv_line[$index])) ? $csv_line[$index] : '';
                switch ($value) {
                    case 'extension':
                        if (strlen($csv_field) == 0) {
                            // Immediate exit if extension field is empty
                            return False;
                        }
                        $result['voicemail_id'] = (is_numeric($csv_field)) ? (int) $csv_field : False;
                        break;
                    case 'number_alias':
                    case 'voicemail_password':
                        $csv_field = (is_numeric($csv_field)) ? (int) $csv_field : '';
                        break;
                    case 'limit_max':
                        $csv_field = (is_numeric($csv_field)) ? (int) $csv_field : 5;
                        break;
                    case 'call_timeout':
                        $csv_field = (is_numeric($csv_field)) ? (int) $csv_field : 30;
                        break;
                    case 'voicemail_enabled': 
                        $csv_field = (filter_var($csv_field, FILTER_VALIDATE_BOOLEAN)) ? 'true' : 'false';
                        break;
                }       
                $result[$value] = $csv_field;
            }

            if ($this->is_generate_vm_pass) {
                $result['voicemail_password'] = generate_password($this->vm_password_length, 1);
            }

            if ($this->is_generate_ext_pass) {
                $result['password'] = generate_password();
            }

            // Set defaults that might be set before

            $result['limit_max'] = isset($result['limit_max']) ? $result['limit_max'] : 5;
            $result['call_timeout'] = isset($result['call_timeout']) ? $result['call_timeout'] : 30;
            $result['directory_visible'] = isset($result['directory_visible']) ? $result['directory_visible'] : 'true';
            $result['directory_exten_visible'] = $result['directory_visible'];
            $result['description'] = isset($result['description']) ? $result['description'] : '';

            $result['voicemail_mail_to'] = isset($result['voicemail_mail_to']) ? $result['voicemail_mail_to'] : '';
            $result['voicemail_enabled'] = isset($result['voicemail_enabled']) ? $result['voicemail_enabled'] : 'true';
            $result['voicemail_description'] = $result['description'];
            $result['device_label'] = isset($result['device_label']) ? $result['device_label'] : $result['extension'];
            $result['device_template'] = isset($result['device_template']) ? strtolower($result['device_template']) : '';
            
            // Set various defaults that is not controlled by user
            
            $result['limit_destination'] = 'error/user_busy';
            $result['call_screen_enabled'] = 'false';
            $result['user_context'] = $this->domain_name;
            $result['enabled'] = 'true';
            $result['voicemail_file'] = 'attach';
            $result['voicemail_local_after_email'] = 'true';
            $result['device_enabled'] = 'true';
            $result['device_vendor'] = explode('/', $result['device_template'])[0];

            return $result;
        }

        private function get_one_result($sql) {
            $prep_statement = $this->db->prepare(check_sql($sql));
            $prep_statement->execute();
            $result = $prep_statement->fetch(PDO::FETCH_NUM);
            $result_count = count($result);
            if ($result_count == 0) {
                return False;
            }
            return $result[0];
        }

        private function form_prepare_insert_statement($csv_line) {
            
            // Funcion to form part of INSERT statement with PDO prepared form
            // like '(extension_uuid, domain_uuid) VALUES (?, ?)
            // ? symbols replaced on execute() stage.

            $sql = '';

            $keys = array_keys($csv_line);
            $sql .= "(" . implode(',', $keys) . ") VALUES (";
            $sql .= str_repeat('?,', count($keys));
            $sql = rtrim($sql, ", ") . ")";

            return $sql;
        }

        private function form_prepare_update_statement($csv_line) {

            // Function to form part of UPDATE statement with PDO prepared form
            // like 'extension_uuid = ?, domain_uuid = ?'
            // ? symbols replaced on execute() stage.

            $sql = '';
            $keys = array_keys($csv_line);
            $sql .= implode(' = ?,', $keys);

            $sql .= " = ?";
            return $sql;
        }

        private function prepare_and_execute_statement($sql, $insert_array = NULL) {

            // Function for prepare end execute statements. Mostly done for extensive logging errors.

            $prep_statement = $this->db->prepare(check_sql($sql));
            if (!$prep_statement) {
                // Not that efficient logging for errors, but better than nothing
                echo $sql . "\n";
                echo "Prepare error: ". json_encode($this->db->errorInfo()) . "\n" . json_encode($prep_statement->errorInfo()) . "\n";
                return;
            }

            if (!$prep_statement->execute(array_values($insert_array))) {
                echo "Execute error: ". json_encode($prep_statement->errorInfo()) . "\n";
                echo $sql . "\n" . json_encode(array_values($insert_array)) . "\n";
            }
        }

        private function add_extension($csv_line) {

            // Check if extension is there
            $sql = "SELECT extension_uuid FROM v_extensions";
            $sql .= " WHERE domain_uuid = '" . $this->domain_uuid . "'";
            $sql .= " AND extension = '" . $csv_line['extension'] . "'";
            $sql .= " LIMIT 1";
            // Getting UUID if exists

            $extension_csv_line = array();
            $extension_csv_line['extension_uuid'] = $this->get_one_result($sql);

            // Remove non-needed entires from CSV

            foreach ($csv_line as $key => $value) {
                if ($this->starts_with($key, 'device_') or $this->starts_with($key, 'voicemail_') or $this->starts_with($key, 'extension_')) {
                    continue;
                }
                $extension_csv_line[$key] = $value;
            }

            if ($extension_csv_line['extension_uuid']) {
                // Update existing extension
                $sql = "UPDATE v_extensions SET ";
                $sql .= $this->form_prepare_update_statement($extension_csv_line);
                $sql .= " WHERE extension_uuid = '" . $extension_csv_line['extension_uuid'] . "'";
                $sql .= " AND domain_uuid = '" . $this->domain_uuid . "'";
            } else {
                // Insert new extension
                $extension_csv_line['domain_uuid'] = $this->domain_uuid;
                $extension_csv_line['extension_uuid'] = uuid();
                $sql = "INSERT INTO v_extensions ";
                $sql .= $this->form_prepare_insert_statement($extension_csv_line);
            }

            $this->prepare_and_execute_statement($sql, $extension_csv_line);

            if ($this->is_add_extension_user) {
                // Get user id by name
                $sql = "SELECT user_uuid FROM v_users";
                $sql .= " WHERE domain_uuid = '" . $this->domain_uuid . "'";
                $sql .= " AND username = '" . $csv_line['extension_user']  . "'";
                $sql .= " LIMIT 1";
                $user_uuid = $this->get_one_result($sql);
                
                if (!$user_uuid) {
                    // Cannot find this user_uuid. So, can't add user to extension
                    return;
                }

                // Check if this link is already exists
                $sql = "SELECT extension_user_uuid FROM v_extension_users";
                $sql .= " WHERE domain_uuid = '" . $this->domain_uuid . "'";
                $sql .= " AND extension_uuid = '" . $extension_csv_line['extension_uuid'] . "'";
                $sql .= " AND user_uuid = '" . $user_uuid . "'";
                $sql .= " LIMIT 1";

                if ($this->get_one_result($sql)) {
                    // Link is already exists
                    return;
                }

                // Prepare data to insert into database
                $extension_to_user = array(
                    'extension_user_uuid' => uuid(),
                    'domain_uuid' => $this->domain_uuid,
                    'extension_uuid' => $extension_csv_line['extension_uuid'],
                    'user_uuid' => $user_uuid,
                );

                $sql = "INSERT INTO v_extension_users";
                $sql .= $this->form_prepare_insert_statement($extension_to_user);

                $this->prepare_and_execute_statement($sql, $extension_to_user);

            }
        }

        private function add_voicemail($csv_line) {

            if (strlen($csv_line['voicemail_id']) == 0) {
                return;
            }

            // Check if VM id is there
            $sql = "SELECT voicemail_uuid FROM v_voicemails";
            $sql .= " WHERE domain_uuid = '" . $this->domain_uuid . "'";
            $sql .= " AND voicemail_id = '" . $csv_line['voicemail_id'] . "'";
            $sql .= " LIMIT 1";
            
            $voicemail_csv_line = array();

            // Getting UUID if exists
            $voicemail_csv_line['voicemail_uuid'] = $this->get_one_result($sql);

            // Leaving only necessary data
            foreach ($csv_line as $key => $value) {
                if ($this->starts_with($key, 'voicemail_')) {
                    $voicemail_csv_line[$key] = $value;
                }
            }

            if ($voicemail_csv_line['voicemail_uuid']) {
                $sql = "UPDATE v_voicemails SET ";
                $sql .= $this->form_prepare_update_statement($voicemail_csv_line);
                $sql .= " WHERE voicemail_uuid = '" . $voicemail_csv_line['voicemail_uuid'] . "'";
                $sql .= " AND domain_uuid = '" . $this->domain_uuid . "'";
            } else {
                $voicemail_csv_line['domain_uuid'] = $this->domain_uuid;
                $voicemail_csv_line['voicemail_uuid'] = uuid();
                $sql = "INSERT INTO v_voicemails";
                $sql .= $this->form_prepare_insert_statement($voicemail_csv_line);
            }
            $this->prepare_and_execute_statement($sql, $voicemail_csv_line);

        }

        private function add_device($csv_line) {
            if (!$this->is_add_device) {
                return;
            }
            // First - check if device exists
            $sql = "SELECT device_uuid FROM v_devices";
            $sql .= " WHERE domain_uuid = '" . $this->domain_uuid . "'";
            $sql .= " AND device_mac_address = '" . $csv_line['device_mac_address'] . "'";
            $sql .= " LIMIT 1";
            
            $device_csv_line = array();

            $device_csv_line['device_uuid'] = $this->get_one_result($sql);

            // Cleanup CSV file
            foreach ($csv_line as $key => $value) {
                if ($this->starts_with($key, 'device_')) {
                    $device_csv_line[$key] = $value;
                }
            }

            // Check for profile UUID
            if (isset($device_csv_line['device_profile'])) {
                $sql = "SELECT device_profile_uuid FROM v_device_profiles";
                $sql .= " WHERE domain_uuid = '" . $this->domain_uuid . "'";
                $sql .= " AND device_profile_name = '" . $device_csv_line['device_profile'] . "'";
                $sql .= " AND device_profile_enabled = 'true'";
                $sql .= " LIMIT 1";

                $device_csv_line['device_profile_uuid'] = $this->get_one_result($sql);
                if (!$device_csv_line['device_profile_uuid']) {
                    unset($device_csv_line['device_profile_uuid']);
                }
                unset($device_csv_line['device_profile']);
            }

            if ($device_csv_line['device_uuid']) {
                $sql = "UPDATE v_devices SET ";
                $sql .= $this->form_prepare_update_statement($device_csv_line);
                $sql .= " WHERE device_uuid = '" . $device_csv_line['device_uuid'] . "'";
                $sql .= " AND domain_uuid = '" . $this->domain_uuid . "'";
            } else {
                $device_csv_line['domain_uuid'] = $this->domain_uuid;
                $device_csv_line['device_uuid'] = uuid();
                $sql = "INSERT INTO v_devices ";
                $sql .= $this->form_prepare_insert_statement($device_csv_line);
            }

            $this->prepare_and_execute_statement($sql, $device_csv_line);

            // Add line 1 for extension

            $device_line_array = array(
                'line_number' => '1',
                'server_address' => $this->domain_name,
                'display_name' => $csv_line['extension'],
                'user_id' => $csv_line['extension'],
                'auth_id' => $csv_line['extension'],
                'password' => $csv_line['password'],
                'sip_port' => $this->line_sip_port,
                'sip_transport' => $this->line_sip_transport,
                'register_expires' => $this->line_register_expires,
                'enabled' => 'true',
                'device_uuid' => $device_csv_line['device_uuid'],
            );

            // Check if line 1 for this device already exists

            $sql = "SELECT device_line_uuid FROM v_device_lines";
            $sql .= " WHERE domain_uuid = '" . $this->domain_uuid . "'";
            $sql .= " AND device_uuid = '" . $device_csv_line['device_uuid'] . "'";
            $sql .= " LIMIT 1";

            $device_line_array['device_line_uuid'] = $this->get_one_result($sql);

            if ($device_line_array['device_line_uuid']) {
                $sql = "UPDATE v_device_lines SET ";
                $sql .= $this->form_prepare_update_statement($device_line_array);
                $sql .= " WHERE device_line_uuid = '" . $device_line_array['device_line_uuid'] . "'";
                $sql .= " AND domain_uuid = '" . $this->domain_uuid . "'";
            } else {
                $device_line_array['domain_uuid'] = $this->domain_uuid;
                $device_line_array['device_line_uuid'] = uuid();
                $sql = "INSERT INTO v_device_lines";
                $sql .= $this->form_prepare_insert_statement($device_line_array);
            }

            $this->prepare_and_execute_statement($sql, $device_line_array);
        }

        // Private funcions end
        // Public functions start

        public function is_valid() {
            if ($this->csv_file) {
                return True;
            }
            return False;
        }

        public function read_first($number_to_read = 4) {

            $this->csv_file->rewind();
            
            if (!$this->csv_file->valid()) {
                return False;
            }
            $result = array();
            for ($i = 1; $i < $number_to_read; $i++) {
                if (!$this->csv_file->valid()) {
                    break;
                }
                $result[] = array_map('escape',$this->csv_file->fgetcsv());
                //$result[] = $this->csv_file->fgetcsv();
            }
            $this->csv_file->rewind();
            return $result;
        }

        public function set_csv_fields_order($csv_fields_order) {
            $this->csv_fields_order = $csv_fields_order;

            $this->is_add_device = in_array('device_mac_address', $csv_fields_order);
            $this->is_generate_ext_pass = !in_array('password',  $csv_fields_order);

            $this->is_generate_vm_pass = !in_array('voicemail_password',  $csv_fields_order);
            $this->is_add_extension_user = in_array('extension_user', $csv_fields_order);

            if ($this->is_import_devices) {
                $this->is_add_device_profile = in_array('device_profile',  $csv_fields_order);
            }
        }

        public function process_csv_file($options) {

            // Increase running time to 5 min
            set_time_limit(5 * 60);
            $result_message = '';

            $text = (new text)->get();
            $this->vm_password_length = $options['vm_password_length'];
            $this->db = $options['db'];
            $this->domain_uuid = $options['domain_uuid'];
            $this->domain_name = $options['domain_name'];
            $this->line_sip_transport = $options['line_sip_transport'];
            $this->line_sip_port = $options['line_sip_port'];
            $this->line_register_expires = $options['line_register_expires'];

            $skip_first_line = $options['skip_first_line'];

            if (!$this->csv_fields_order) {
                $result_message .= $text['message-csv_info_missing'] . "\n";
                return $result_message;
            }

            // Read file line by line
            $this->csv_file->rewind();
            $result_message .= $text['message-process_csv_file_start'] . "\n";
            // Skip first line if applied
            if ($skip_first_line) {
                $result_message .= $text['message-process_csv_file_skip_first_line'] . "\n";
                $this->csv_file->current();
                $this->csv_file->next();
            }

            $added_lines_count = 0;
            $skipped_lines_count = 0;
            $skipped_lines_array = array();

            while (!$this->csv_file->eof()) {

                // Read CSV line and sterialize it
                $csv_line = array_map('check_str', $this->csv_file->fgetcsv());
                $csv_line = $this->normalize_line($csv_line);
                
                if ($csv_line) { // CSV line is correct and extension is present
                    $this->add_extension($csv_line);
                    $this->add_voicemail($csv_line);
                    $this->add_device($csv_line);
                    $added_lines_count += 1;
                } else {
                    $skipped_lines_count += 1;
                    $skipped_lines_array[] = $csv_line;
                }

            }
            $result_message .= $text['message-process_csv_file_end'] . "\n\n";
            // Add statistics to result message
            $result_message .= $text['message-process_csv_file_stats'] . "\n";
            $result_message .= " " . $text['message-process_csv_file_added_lines'] . " " . $added_lines_count . "\n";
            $result_message .= " " . $text['message-process_csv_file_skipped_lines'] . " " . $skipped_lines_count . "\n";
            if ($skipped_lines > 0) {
                foreach ($skipped_lines_array as $skipped_line) {
                    $result_message .= "   " . implode(',', $skipped_line) . "\n";
                }
            }

            return $result_message;

        }
    }
}
?>