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
	Portions created by the Initial Developer are Copyright (C) 2008-2010
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
include "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (permission_exists('contacts_add') || permission_exists('contacts_edit')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//set the action to an add or update
	if (isset($_REQUEST["id"])) {
		$action = "update";
		$id = check_str($_REQUEST["id"]);
	}
	else {
		$action = "add";
	}

//get the http post values and set them as php variables
	if (count($_POST)>0) {
		$username = check_str($_POST["username"]);
		$password = check_str($_POST["password"]);
		$user_type = check_str($_POST["user_type"]);
		$user_category = check_str($_POST["user_category"]);
		$user_first_name = check_str($_POST["user_first_name"]);
		$user_last_name = check_str($_POST["user_last_name"]);
		$user_company_name = check_str($_POST["user_company_name"]);
		$user_physical_address_1 = check_str($_POST["user_physical_address_1"]);
		$user_physical_address_2 = check_str($_POST["user_physical_address_2"]);
		$user_physical_city = check_str($_POST["user_physical_city"]);
		$user_physical_state_province = check_str($_POST["user_physical_state_province"]);
		$user_physical_postal_code = check_str($_POST["user_physical_postal_code"]);
		$user_physical_country = check_str($_POST["user_physical_country"]);
		$user_mailing_address_1 = check_str($_POST["user_mailing_address_1"]);
		$user_mailing_address_2 = check_str($_POST["user_mailing_address_2"]);
		$user_mailing_city = check_str($_POST["user_mailing_city"]);
		$user_mailing_state_province = check_str($_POST["user_mailing_state_province"]);
		$user_mailing_postal_code = check_str($_POST["user_mailing_postal_code"]);
		$user_mailing_country = check_str($_POST["user_mailing_country"]);
		$user_billing_address_1 = check_str($_POST["user_billing_address_1"]);
		$user_billing_address_2 = check_str($_POST["user_billing_address_2"]);
		$user_billing_city = check_str($_POST["user_billing_city"]);
		$user_billing_state_province = check_str($_POST["user_billing_state_province"]);
		$user_billing_postal_code = check_str($_POST["user_billing_postal_code"]);
		$user_billing_country = check_str($_POST["user_billing_country"]);
		$user_shipping_address_1 = check_str($_POST["user_shipping_address_1"]);
		$user_shipping_address_2 = check_str($_POST["user_shipping_address_2"]);
		$user_shipping_city = check_str($_POST["user_shipping_city"]);
		$user_shipping_state_province = check_str($_POST["user_shipping_state_province"]);
		$user_shipping_postal_code = check_str($_POST["user_shipping_postal_code"]);
		$user_shipping_country = check_str($_POST["user_shipping_country"]);
		$user_phone_1 = check_str($_POST["user_phone_1"]);
		$user_phone_1_ext = check_str($_POST["user_phone_1_ext"]);
		$user_phone_2 = check_str($_POST["user_phone_2"]);
		$user_phone_2_ext = check_str($_POST["user_phone_2_ext"]);
		$user_phone_mobile = check_str($_POST["user_phone_mobile"]);
		$user_phone_fax = check_str($_POST["user_phone_fax"]);
		$user_phone_emergency_mobile = check_str($_POST["user_phone_emergency_mobile"]);
		$user_email_emergency = check_str($_POST["user_email_emergency"]);
		$user_email = check_str($_POST["user_email"]);
		$user_url = check_str($_POST["user_url"]);
		$user_notes = check_str($_POST["user_notes"]);
		$user_optional_1 = check_str($_POST["user_optional_1"]);
		$user_add_user = check_str($_POST["user_add_user"]);
		$user_add_date = check_str($_POST["user_add_date"]);
	}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';
	if ($action == "update") {
		$id = check_str($_POST["id"]);
	}

	//check for all required data
		//if (strlen($username) == 0) { $msg .= "Please provide: Username<br>\n"; }
		//if (strlen($password) == 0) { $msg .= "Please provide: Password<br>\n"; }
		//if (strlen($user_type) == 0) { $msg .= "Please provide: Type<br>\n"; }
		//if (strlen($user_category) == 0) { $msg .= "Please provide: Category<br>\n"; }
		//if (strlen($user_first_name) == 0) { $msg .= "Please provide: First Name<br>\n"; }
		//if (strlen($user_last_name) == 0) { $msg .= "Please provide: Last Name<br>\n"; }
		//if (strlen($user_company_name) == 0) { $msg .= "Please provide: Organization<br>\n"; }
		//if (strlen($user_physical_address_1) == 0) { $msg .= "Please provide: Address 1<br>\n"; }
		//if (strlen($user_physical_address_2) == 0) { $msg .= "Please provide: Address 2<br>\n"; }
		//if (strlen($user_physical_city) == 0) { $msg .= "Please provide: City<br>\n"; }
		//if (strlen($user_physical_state_province) == 0) { $msg .= "Please provide: State/Province<br>\n"; }
		//if (strlen($user_physical_postal_code) == 0) { $msg .= "Please provide: Postal Code<br>\n"; }
		//if (strlen($user_physical_country) == 0) { $msg .= "Please provide: Country<br>\n"; }
		//if (strlen($user_mailing_address_1) == 0) { $msg .= "Please provide: Address 1<br>\n"; }
		//if (strlen($user_mailing_address_2) == 0) { $msg .= "Please provide: Address 2<br>\n"; }
		//if (strlen($user_mailing_city) == 0) { $msg .= "Please provide: City<br>\n"; }
		//if (strlen($user_mailing_state_province) == 0) { $msg .= "Please provide: State/Province<br>\n"; }
		//if (strlen($user_mailing_postal_code) == 0) { $msg .= "Please provide: Postal Code<br>\n"; }
		//if (strlen($user_mailing_country) == 0) { $msg .= "Please provide: Country<br>\n"; }
		//if (strlen($user_billing_address_1) == 0) { $msg .= "Please provide: Address 1<br>\n"; }
		//if (strlen($user_billing_address_2) == 0) { $msg .= "Please provide: Address 2<br>\n"; }
		//if (strlen($user_billing_city) == 0) { $msg .= "Please provide: City<br>\n"; }
		//if (strlen($user_billing_state_province) == 0) { $msg .= "Please provide: State/Province<br>\n"; }
		//if (strlen($user_billing_postal_code) == 0) { $msg .= "Please provide: Postal Code<br>\n"; }
		//if (strlen($user_billing_country) == 0) { $msg .= "Please provide: Country<br>\n"; }
		//if (strlen($user_shipping_address_1) == 0) { $msg .= "Please provide: Address 1<br>\n"; }
		//if (strlen($user_shipping_address_2) == 0) { $msg .= "Please provide: Address 2<br>\n"; }
		//if (strlen($user_shipping_city) == 0) { $msg .= "Please provide: City<br>\n"; }
		//if (strlen($user_shipping_state_province) == 0) { $msg .= "Please provide: State/Province<br>\n"; }
		//if (strlen($user_shipping_postal_code) == 0) { $msg .= "Please provide: Postal Code<br>\n"; }
		//if (strlen($user_shipping_country) == 0) { $msg .= "Please provide: Country<br>\n"; }
		//if (strlen($user_phone_1) == 0) { $msg .= "Please provide: Phone 1<br>\n"; }
		//if (strlen($user_phone_1_ext) == 0) { $msg .= "Please provide: Ext 1<br>\n"; }
		//if (strlen($user_phone_2) == 0) { $msg .= "Please provide: Phone 2<br>\n"; }
		//if (strlen($user_phone_2_ext) == 0) { $msg .= "Please provide: Ext 2<br>\n"; }
		//if (strlen($user_phone_mobile) == 0) { $msg .= "Please provide: Mobile<br>\n"; }
		//if (strlen($user_phone_fax) == 0) { $msg .= "Please provide: FAX<br>\n"; }
		//if (strlen($user_phone_emergency_mobile) == 0) { $msg .= "Please provide: Emergency Mobile<br>\n"; }
		//if (strlen($user_email_emergency) == 0) { $msg .= "Please provide: Emergency Email<br>\n"; }
		//if (strlen($user_email) == 0) { $msg .= "Please provide: Email<br>\n"; }
		//if (strlen($user_url) == 0) { $msg .= "Please provide: URL<br>\n"; }
		//if (strlen($user_notes) == 0) { $msg .= "Please provide: Notes<br>\n"; }
		//if (strlen($user_optional_1) == 0) { $msg .= "Please provide: Optional 1<br>\n"; }
		//if (strlen($user_add_user) == 0) { $msg .= "Please provide: Add User<br>\n"; }
		//if (strlen($user_add_date) == 0) { $msg .= "Please provide: Add Date<br>\n"; }
		if (strlen($msg) > 0 && strlen($_POST["persistformvar"]) == 0) {
			require_once "includes/header.php";
			require_once "includes/persistformvar.php";
			echo "<div align='center'>\n";
			echo "<table><tr><td>\n";
			echo $msg."<br />";
			echo "</td></tr></table>\n";
			persistformvar($_POST);
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		}

		//add or update the database
			if ($_POST["persistformvar"] != "true") {
				if ($action == "add" && permission_exists('contacts_add')) {
					$sql = "insert into v_users ";
					$sql .= "(";
					$sql .= "domain_uuid, ";
					//$sql .= "username, ";
					//$sql .= "password, ";
					$sql .= "user_type, ";
					$sql .= "user_category, ";
					$sql .= "user_first_name, ";
					$sql .= "user_last_name, ";
					$sql .= "user_company_name, ";
					$sql .= "user_physical_address_1, ";
					$sql .= "user_physical_address_2, ";
					$sql .= "user_physical_city, ";
					$sql .= "user_physical_state_province, ";
					$sql .= "user_physical_postal_code, ";
					$sql .= "user_physical_country, ";
					$sql .= "user_mailing_address_1, ";
					$sql .= "user_mailing_address_2, ";
					$sql .= "user_mailing_city, ";
					$sql .= "user_mailing_state_province, ";
					$sql .= "user_mailing_postal_code, ";
					$sql .= "user_mailing_country, ";
					$sql .= "user_billing_address_1, ";
					$sql .= "user_billing_address_2, ";
					$sql .= "user_billing_city, ";
					$sql .= "user_billing_state_province, ";
					$sql .= "user_billing_postal_code, ";
					$sql .= "user_billing_country, ";
					$sql .= "user_shipping_address_1, ";
					$sql .= "user_shipping_address_2, ";
					$sql .= "user_shipping_city, ";
					$sql .= "user_shipping_state_province, ";
					$sql .= "user_shipping_postal_code, ";
					$sql .= "user_shipping_country, ";
					$sql .= "user_phone_1, ";
					$sql .= "user_phone_1_ext, ";
					$sql .= "user_phone_2, ";
					$sql .= "user_phone_2_ext, ";
					$sql .= "user_phone_mobile, ";
					$sql .= "user_phone_fax, ";
					$sql .= "user_phone_emergency_mobile, ";
					$sql .= "user_email_emergency, ";
					$sql .= "user_email, ";
					$sql .= "user_url, ";
					$sql .= "user_notes, ";
					$sql .= "user_optional_1, ";
					$sql .= "user_add_user, ";
					$sql .= "user_add_date ";
					$sql .= ")";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'$domain_uuid', ";
					//$sql .= "'$username', ";
					//$sql .= "'$password', ";
					$sql .= "'$user_type', ";
					$sql .= "'$user_category', ";
					$sql .= "'$user_first_name', ";
					$sql .= "'$user_last_name', ";
					$sql .= "'$user_company_name', ";
					$sql .= "'$user_physical_address_1', ";
					$sql .= "'$user_physical_address_2', ";
					$sql .= "'$user_physical_city', ";
					$sql .= "'$user_physical_state_province', ";
					$sql .= "'$user_physical_postal_code', ";
					$sql .= "'$user_physical_country', ";
					$sql .= "'$user_mailing_address_1', ";
					$sql .= "'$user_mailing_address_2', ";
					$sql .= "'$user_mailing_city', ";
					$sql .= "'$user_mailing_state_province', ";
					$sql .= "'$user_mailing_postal_code', ";
					$sql .= "'$user_mailing_country', ";
					$sql .= "'$user_billing_address_1', ";
					$sql .= "'$user_billing_address_2', ";
					$sql .= "'$user_billing_city', ";
					$sql .= "'$user_billing_state_province', ";
					$sql .= "'$user_billing_postal_code', ";
					$sql .= "'$user_billing_country', ";
					$sql .= "'$user_shipping_address_1', ";
					$sql .= "'$user_shipping_address_2', ";
					$sql .= "'$user_shipping_city', ";
					$sql .= "'$user_shipping_state_province', ";
					$sql .= "'$user_shipping_postal_code', ";
					$sql .= "'$user_shipping_country', ";
					$sql .= "'$user_phone_1', ";
					$sql .= "'$user_phone_1_ext', ";
					$sql .= "'$user_phone_2', ";
					$sql .= "'$user_phone_2_ext', ";
					$sql .= "'$user_phone_mobile', ";
					$sql .= "'$user_phone_fax', ";
					$sql .= "'$user_phone_emergency_mobile', ";
					$sql .= "'$user_email_emergency', ";
					$sql .= "'$user_email', ";
					$sql .= "'$user_url', ";
					$sql .= "'$user_notes', ";
					$sql .= "'$user_optional_1', ";
					$sql .= "'$user_add_user', ";
					$sql .= "'$user_add_date' ";
					$sql .= ")";
					$db->exec(check_sql($sql));
					unset($sql);

					require_once "includes/header.php";
					echo "<meta http-equiv=\"refresh\" content=\"2;url=users.php\">\n";
					echo "<div align='center'>\n";
					echo "Add Complete\n";
					echo "</div>\n";
					require_once "includes/footer.php";
					return;
				} //if ($action == "add")

				if ($action == "update" || permission_exists('contacts_edit')) {
					$sql = "update v_users set ";
					//$sql .= "username = '$username', ";
					//if (strlen($password) > 0) {
					//	$sql .= "password = '$password', ";
					//}
					$sql .= "user_type = '$user_type', ";
					$sql .= "user_category = '$user_category', ";
					$sql .= "user_first_name = '$user_first_name', ";
					$sql .= "user_last_name = '$user_last_name', ";
					$sql .= "user_company_name = '$user_company_name', ";
					$sql .= "user_physical_address_1 = '$user_physical_address_1', ";
					$sql .= "user_physical_address_2 = '$user_physical_address_2', ";
					$sql .= "user_physical_city = '$user_physical_city', ";
					$sql .= "user_physical_state_province = '$user_physical_state_province', ";
					$sql .= "user_physical_postal_code = '$user_physical_postal_code', ";
					$sql .= "user_physical_country = '$user_physical_country', ";
					$sql .= "user_mailing_address_1 = '$user_mailing_address_1', ";
					$sql .= "user_mailing_address_2 = '$user_mailing_address_2', ";
					$sql .= "user_mailing_city = '$user_mailing_city', ";
					$sql .= "user_mailing_state_province = '$user_mailing_state_province', ";
					$sql .= "user_mailing_postal_code = '$user_mailing_postal_code', ";
					$sql .= "user_mailing_country = '$user_mailing_country', ";
					$sql .= "user_billing_address_1 = '$user_billing_address_1', ";
					$sql .= "user_billing_address_2 = '$user_billing_address_2', ";
					$sql .= "user_billing_city = '$user_billing_city', ";
					$sql .= "user_billing_state_province = '$user_billing_state_province', ";
					$sql .= "user_billing_postal_code = '$user_billing_postal_code', ";
					$sql .= "user_billing_country = '$user_billing_country', ";
					$sql .= "user_shipping_address_1 = '$user_shipping_address_1', ";
					$sql .= "user_shipping_address_2 = '$user_shipping_address_2', ";
					$sql .= "user_shipping_city = '$user_shipping_city', ";
					$sql .= "user_shipping_state_province = '$user_shipping_state_province', ";
					$sql .= "user_shipping_postal_code = '$user_shipping_postal_code', ";
					$sql .= "user_shipping_country = '$user_shipping_country', ";
					$sql .= "user_phone_1 = '$user_phone_1', ";
					$sql .= "user_phone_1_ext = '$user_phone_1_ext', ";
					$sql .= "user_phone_2 = '$user_phone_2', ";
					$sql .= "user_phone_2_ext = '$user_phone_2_ext', ";
					$sql .= "user_phone_mobile = '$user_phone_mobile', ";
					$sql .= "user_phone_fax = '$user_phone_fax', ";
					$sql .= "user_phone_emergency_mobile = '$user_phone_emergency_mobile', ";
					$sql .= "user_email_emergency = '$user_email_emergency', ";
					$sql .= "user_email = '$user_email', ";
					$sql .= "user_url = '$user_url', ";
					$sql .= "user_notes = '$user_notes', ";
					$sql .= "user_optional_1 = '$user_optional_1' ";
					//$sql .= "user_add_user = '$user_add_user', ";
					//$sql .= "user_add_date = '$user_add_date' ";
					$sql .= "where domain_uuid = '$domain_uuid' ";
					$sql .= "and id = '$id' ";
					$db->exec(check_sql($sql));
					unset($sql);

					require_once "includes/header.php";
					echo "<meta http-equiv=\"refresh\" content=\"2;url=users.php\">\n";
					echo "<div align='center'>\n";
					echo "Update Complete\n";
					echo "</div>\n";
					require_once "includes/footer.php";
					return;
				} //if ($action == "update")
		} //if ($_POST["persistformvar"] != "true")
	} //(count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

	//pre-populate the form
		if (count($_GET)>0 && $_POST["persistformvar"] != "true") {
			$id = $_GET["id"];
			$sql = "";
			$sql .= "select * from v_users ";
			$sql .= "where domain_uuid = '$domain_uuid' ";
			$sql .= "and id = '$id' ";
			$prepstatement = $db->prepare(check_sql($sql));
			$prepstatement->execute();
			$result = $prepstatement->fetchAll();
			foreach ($result as &$row) {
				//$username = $row["username"];
				//$password = $row["password"];
				$user_type = $row["user_type"];
				$user_category = $row["user_category"];
				$user_first_name = $row["user_first_name"];
				$user_last_name = $row["user_last_name"];
				$user_company_name = $row["user_company_name"];
				$user_physical_address_1 = $row["user_physical_address_1"];
				$user_physical_address_2 = $row["user_physical_address_2"];
				$user_physical_city = $row["user_physical_city"];
				$user_physical_state_province = $row["user_physical_state_province"];
				$user_physical_postal_code = $row["user_physical_postal_code"];
				$user_physical_country = $row["user_physical_country"];
				$user_mailing_address_1 = $row["user_mailing_address_1"];
				$user_mailing_address_2 = $row["user_mailing_address_2"];
				$user_mailing_city = $row["user_mailing_city"];
				$user_mailing_state_province = $row["user_mailing_state_province"];
				$user_mailing_postal_code = $row["user_mailing_postal_code"];
				$user_mailing_country = $row["user_mailing_country"];
				$user_billing_address_1 = $row["user_billing_address_1"];
				$user_billing_address_2 = $row["user_billing_address_2"];
				$user_billing_city = $row["user_billing_city"];
				$user_billing_state_province = $row["user_billing_state_province"];
				$user_billing_postal_code = $row["user_billing_postal_code"];
				$user_billing_country = $row["user_billing_country"];
				$user_shipping_address_1 = $row["user_shipping_address_1"];
				$user_shipping_address_2 = $row["user_shipping_address_2"];
				$user_shipping_city = $row["user_shipping_city"];
				$user_shipping_state_province = $row["user_shipping_state_province"];
				$user_shipping_postal_code = $row["user_shipping_postal_code"];
				$user_shipping_country = $row["user_shipping_country"];
				$user_phone_1 = $row["user_phone_1"];
				$user_phone_1_ext = $row["user_phone_1_ext"];
				$user_phone_2 = $row["user_phone_2"];
				$user_phone_2_ext = $row["user_phone_2_ext"];
				$user_phone_mobile = $row["user_phone_mobile"];
				$user_phone_fax = $row["user_phone_fax"];
				$user_phone_emergency_mobile = $row["user_phone_emergency_mobile"];
				$user_email_emergency = $row["user_email_emergency"];
				$user_email = $row["user_email"];
				$user_url = $row["user_url"];
				$user_notes = $row["user_notes"];
				$user_optional_1 = $row["user_optional_1"];
				//$user_add_user = $row["user_add_user"];
				//$user_add_date = $row["user_add_date"];
				break; //limit to 1 row
			}
			unset ($prepstatement);
		}

	//show the header
		require_once "includes/header.php";

	//show the content
			echo "<div align='center'>";
			echo "<table width='100%' border='0' cellpadding='0' cellspacing=''>\n";
			echo "<tr class=''>\n";
			echo "	<td align=\"left\">\n";
			echo "	  <br>";

			$tablewidth = "width='100%'";

			echo "<form method='post' name='frm' action=''>\n";
			echo "<table width='100%'  border='0' cellpadding='0' cellspacing='0'>\n";
			echo "<tr>\n";
			if ($action == "add") {
				echo "<td width='30%' nowrap><b>Contact Add</b></td>\n";
			}
			if ($action == "update") {
				echo "<td width='30%' nowrap><b>Contact Edit</b></td>\n";
			}
			echo "<td width='70%' align='right'>\n";
			echo "	<input type='button' class='btn' name='' alt='back' onclick=\"window.location='users_vcard.php?id=$id'\" value='vcard'>\n";
			echo "	<input type='button' class='btn' name='' alt='back' onclick=\"window.location='users.php'\" value='Back'>\n";
			echo "</td>\n";
			echo "</tr>\n";
			echo "<tr><td colspan='2'>\n";
			if ($action == "add") {
				echo "Add the contact information to the fields below.</td>\n";
			}
			if ($action == "update") {
				echo "Edit the contact information using the fields below.\n";
			}
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";

			echo "<br />\n";

		echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
		echo "<tr>\n";
		echo "<td valign='top'>\n";

			echo "<b>User Info</b><br>";
			echo "<div align='center' class='' style='padding:10px;'>\n";
			echo "<table $tablewidth cellpadding='6' cellspacing='0'>";

			//echo "<tr>\n";
			//echo "<td width='30%' class='vncell' valign='top' align='left' nowrap>\n";
			//echo "	Username:\n";
			//echo "</td>\n";
			//echo "<td width='70%' class='vtable' align='left'>\n";
			//echo "	<input style='width: 80%;' class='formfld' type='text' name='username' autocomplete='off' maxlength='255' value=\"$username\">\n";
			//echo "<br />\n";
			//echo "\n";
			//echo "</td>\n";
			//echo "</tr>\n";

			//echo "<tr>\n";
			//echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			//echo "	Password:\n";
			//echo "</td>\n";
			//echo "<td class='vtable' align='left'>\n";
			//echo "	<input style='width: 80%;' class='formfld' type='password' name='password' autocomplete='off' maxlength='255' value=\"$password\">\n";
			//echo "<br />\n";
			//echo "\n";
			//echo "</td>\n";
			//echo "</tr>\n";

			echo "<tr>\n";
			echo "<td  width='30%' class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Type:\n";
			echo "</td>\n";
			echo "<td width='70%' class='vtable' align='left'>\n";
			echo "	<select style='width: 80%;' class='formfld' name='user_type'>\n";
			echo "	<option value=''></option>\n";
			if ($user_type == "Individual") {
				echo "	<option value='Individual' selected>Individual</option>\n";
			}
			else {
				echo "	<option value='Individual'>Individual</option>\n";
			}
			if ($user_type == "Organization") {
				echo "	<option value='Organization' selected>Organization</option>\n";
			}
			else {
				echo "	<option value='Organization'>Organization</option>\n";
			}
			echo "	</select>\n";

			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Category:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_category' maxlength='255' value=\"$user_category\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	First Name:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' style='' type='text' name='user_first_name' maxlength='255' value=\"$user_first_name\"> <span class='smalltext'>vcard</span>\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Last Name:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_last_name' maxlength='255' value=\"$user_last_name\"> <span class='smalltext'>vcard</span>\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Organization:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_company_name' maxlength='255' value=\"$user_company_name\"> <span class='smalltext'>vcard</span>\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";
			echo "</div>\n";


			echo "<b>Contact Information</b><br>";
			echo "<div align='center' class='' style='padding:10px;'>\n";
			echo "<table $tablewidth cellpadding='6' cellspacing='0'>";
			echo "<tr>\n";
			echo "<td width='30%' class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Phone 1:\n";
			echo "</td>\n";
			echo "<td width='70%' class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_phone_1' maxlength='255' value=\"$user_phone_1\"> <span class='smalltext'>vcard</span>\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Ext 1:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_phone_1_ext' maxlength='255' value=\"$user_phone_1_ext\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Phone 2:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_phone_2' maxlength='255' value=\"$user_phone_2\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Ext 2:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_phone_2_ext' maxlength='255' value=\"$user_phone_2_ext\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Mobile:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_phone_mobile' maxlength='255' value=\"$user_phone_mobile\"> <span class='smalltext'>vcard</span>\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	FAX:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_phone_fax' maxlength='255' value=\"$user_phone_fax\"> <span class='smalltext'>vcard</span>\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Emergency Mobile:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_phone_emergency_mobile' maxlength='255' value=\"$user_phone_emergency_mobile\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Emergency Email:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_email_emergency' maxlength='255' value=\"$user_email_emergency\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";
			echo "</div>\n";

			echo "<b>Additional Information</b><br>";
			echo "<div align='center' class='' style='padding:10px;'>\n";
			echo "<table $tablewidth cellpadding='6' cellspacing='0'>";
			echo "<tr>\n";
			echo "<td width='30%' class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Email:\n";
			echo "</td>\n";
			echo "<td width='70%' class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_email' maxlength='255' value=\"$user_email\"> <span class='smalltext'>vcard</span>\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	URL:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_url' maxlength='255' value=\"$user_url\"> <span class='smalltext'>vcard</span>\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Notes:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<textarea style='width: 80%;' class='formfld' type='text' name='user_notes' rows='5'>$user_notes</textarea> <span class='smalltext'>vcard</span>\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			//echo "<tr>\n";
			//echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			//echo "	Optional 1:\n";
			//echo "</td>\n";
			//echo "<td class='vtable' align='left'>\n";
			//echo "	<input style='width: 80%;' class='formfld' type='text' name='user_optional_1' maxlength='255' value=\"$user_optional_1\">\n";
			//echo "<br />\n";
			//echo "\n";
			//echo "</td>\n";
			//echo "</tr>\n";

			//echo "<tr>\n";
			//echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			//echo "	Optional 1:\n";
			//echo "</td>\n";
			//echo "<td class='vtable' align='left'>\n";
			//echo "	<input style='width: 80%;' class='formfld' type='text' name='user_optional_2' maxlength='255' value=\"$user_optional_2\">\n";
			//echo "<br />\n";
			//echo "\n";
			//echo "</td>\n";
			//echo "</tr>\n";

			echo "</table>\n";
			echo "</div>\n";

		echo "</td>\n";
		echo "<td valign='top'>\n";

			echo "<b>Physical Address</b><br>";
			echo "<div align='center' class='' style='padding:10px;'>\n";
			echo "<table $tablewidth cellpadding='6' cellspacing='0'>";
			echo "<tr>\n";
			echo "<td width='30%' class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Address 1:\n";
			echo "</td>\n";
			echo "<td width='70%' class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_physical_address_1' maxlength='255' value=\"$user_physical_address_1\"> <span class='smalltext'>vcard</span>\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Address 2:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_physical_address_2' maxlength='255' value=\"$user_physical_address_2\"> <span class='smalltext'>vcard</span>\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	City:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_physical_city' maxlength='255' value=\"$user_physical_city\"> <span class='smalltext'>vcard</span>\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	State/Province:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_physical_state_province' maxlength='255' value=\"$user_physical_state_province\"> <span class='smalltext'>vcard</span>\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Postal Code:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_physical_postal_code' maxlength='255' value=\"$user_physical_postal_code\"> <span class='smalltext'>vcard</span>\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Country:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_physical_country' maxlength='255' value=\"$user_physical_country\"> <span class='smalltext'>vcard</span>\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";
			echo "</div>\n";

			echo "<b>Postal Address</b><br>";
			echo "<div align='center' class='' style='padding:10px;'>\n";
			echo "<table $tablewidth cellpadding='6' cellspacing='0'>";
			echo "<tr>\n";
			echo "<td width='30%' class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Address 1:\n";
			echo "</td>\n";
			echo "<td width='70%' class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_mailing_address_1' maxlength='255' value=\"$user_mailing_address_1\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Address 2:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_mailing_address_2' maxlength='255' value=\"$user_mailing_address_2\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	City:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_mailing_city' maxlength='255' value=\"$user_mailing_city\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	State/Province:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_mailing_state_province' maxlength='255' value=\"$user_mailing_state_province\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Postal Code:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_mailing_postal_code' maxlength='255' value=\"$user_mailing_postal_code\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Country:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_mailing_country' maxlength='255' value=\"$user_mailing_country\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";
			echo "</div>\n";

			echo "<b>Billing Address</b><br>";
			echo "<div align='center' class='' style='padding:10px;'>\n";
			echo "<table $tablewidth cellpadding='6' cellspacing='0'>";
			echo "<tr>\n";
			echo "<td width='30%' class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Address 1:\n";
			echo "</td>\n";
			echo "<td width='70%' class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_billing_address_1' maxlength='255' value=\"$user_billing_address_1\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Address 2:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_billing_address_2' maxlength='255' value=\"$user_billing_address_2\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	City:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_billing_city' maxlength='255' value=\"$user_billing_city\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	State/Province:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_billing_state_province' maxlength='255' value=\"$user_billing_state_province\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Postal Code:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_billing_postal_code' maxlength='255' value=\"$user_billing_postal_code\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Country:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_billing_country' maxlength='255' value=\"$user_billing_country\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";
			echo "</div>\n";

			echo "<b>Shipping Address</b><br>";
			echo "<div align='center' class='' style='padding:10px;'>\n";
			echo "<table $tablewidth cellpadding='6' cellspacing='0'>";
			echo "<tr>\n";
			echo "<td width='30%' class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Address 1:\n";
			echo "</td>\n";
			echo "<td width='70%' class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_shipping_address_1' maxlength='255' value=\"$user_shipping_address_1\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Address 2:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_shipping_address_2' maxlength='255' value=\"$user_shipping_address_2\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	City:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_shipping_city' maxlength='255' value=\"$user_shipping_city\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	State/Province:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_shipping_state_province' maxlength='255' value=\"$user_shipping_state_province\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Postal Code:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_shipping_postal_code' maxlength='255' value=\"$user_shipping_postal_code\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Country:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='user_shipping_country' maxlength='255' value=\"$user_shipping_country\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";
			echo "</div>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

			//echo "<tr>\n";
			//echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			//echo "	Add User:\n";
			//echo "</td>\n";
			//echo "<td class='vtable' align='left'>\n";
			//echo "	<input style='width: 80%;' class='formfld' type='text' name='user_add_user' maxlength='255' value=\"$user_add_user\">\n";
			//echo "<br />\n";
			//echo "\n";
			//echo "</td>\n";
			//echo "</tr>\n";

			//echo "<tr>\n";
			//echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			//echo "	Add Date:\n";
			//echo "</td>\n";
			//echo "<td class='vtable' align='left'>\n";
			//echo "	<input style='width: 80%;' class='formfld' type='text' name='user_add_date' maxlength='255' value=\"$user_add_date\">\n";
			//echo "<br />\n";
			//echo "\n";
			//echo "</td>\n";
			//echo "</tr>\n";

			echo "	<tr>\n";
			echo "		<td colspan='2' align='right'>\n";
			if ($action == "update") {
				echo "				<input type='hidden' name='id' value='$id'>\n";
			}
			echo "				<input type='submit' name='submit' class='btn' value='Save'>\n";
			echo "		</td>\n";
			echo "	</tr>";
			echo "</table>";
			echo "</form>";

			echo "	</td>";
			echo "	</tr>";
			echo "</table>";
			echo "</div>";

			echo "<br />\n";
			echo "<br />\n";

//show the footer
	require_once "includes/footer.php";
?>
