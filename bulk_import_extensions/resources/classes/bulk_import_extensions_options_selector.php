<?php

if (!class_exists('bulk_import_extensions_options_selector')) {
    class bulk_import_extensions_options_selector {

        private $optgroups;

        public function __construct() {
            $this->text = (new text)->get();

            $this->optgroups = array(
                $this->text['label-extensions'] => array(
                    'extension' => $this->text['label-extension'],
                    'password' => $this->text['label-password'],
                    'number_alias' => $this->text['label-number_alias'],
                    'extension_user' => $this->text['label-extension_user'],
                    'accountcode' => $this->text['label-accountcode'],
                    'call_timeout' => $this->text['label-call_timeout'],
                    'effective_caller_id_name' => $this->text['label-effective_caller_id_name'],
                    'effective_caller_id_number' => $this->text['label-effective_caller_id_number'],
                    'outbound_caller_id_name' => $this->text['label-outbound_caller_id_name'],
                    'outbound_caller_id_number' => $this->text['label-outbound_caller_id_number'],
                    'directory_first_name' => $this->text['label-directory_first_name'],
                    'directory_last_name' => $this->text['label-directory_last_name'],
                    'directory_visible' => $this->text['label-directory_visible'],
                    'limit_max' => $this->text['label-limit_max'],
                    'toll_allow' => $this->text['label-toll_allow'],
                    'call_timeout' => $this->text['label-call_timeout'],
                    'call_group' => $this->text['title-call_group'],
                    'user_record' => $this->text['label-user_record'],
                    'hold_music' => $this->text['label-hold_music'],
                    'description' => $this->text['label-description'],
                ),
                // To each device add black phone symbol at the end to differ
                $this->text['label-device'] => array(
                    'device_mac_address' => $this->text['label-device_mac_address'] . " &#x260E;",
                    'device_profile' => $this->text['label-device_profile'] . " &#x260E;",
                    'device_label' => $this->text['label-device_label'] . " &#x260E;",
                    'device_vendor' => $this->text['label-device_vendor'] . " &#x260E;",
                    'device_model' => $this->text['label-device_model'] . " &#x260E;",
                    'device_template' => $this->text['label-device_template'] . " &#x260E;",
                    'device_description' => $this->text['label-device_description'] . " &#x260E;",
                ),
                $this->text['label-voicemail'] => array(
                    'voicemail_enabled' => $this->text['label-voicemail_enabled'] . " &#x2709;",
                    'voicemail_mail_to' => $this->text['label-voicemail_mail_to'] . " &#x2709;",
                    'voicemail_password' => $this->text['label-voicemail_password'] . " &#x2709;",
                    'voicemail_attach_file' => $this->text['label-voicemail_attach_file'] . " &#x2709;",
                    'voicemail_description' => $this->text['label-voicemail_description'] . " &#x2709;",
                ),
            );
        }


        public function getopts() {
            return $this->optgroups;
        }

        public function draw_selector($name = '', $selected_id = 0) {
            $i = 0;
            $selector_text .= "<select name = '" . $name . "' id = '" . $name . "' class='formfld'>";
            $selector_text .= "<option value=''></option>\n";
            foreach ($this->optgroups as $optgroup_label => $optgroup_data) {
                $selector_text .= "<optgroup label = '" . $optgroup_label . "'>\n";

                foreach ($optgroup_data as $key => $value) {
                    $selector_text .= "<option " . (($i == $selected_id) ? "selected " : " " );
                    $selector_text .= "value='" . $key . "'>" . $value . "</option>\n";
                    $i += 1;
                }
                $selector_text .= "</optgroup>\n";
            }
            $selector_text .= "</select>\n";
            return $selector_text;
        }
    }
}

?>