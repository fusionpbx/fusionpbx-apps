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
		$usertype = check_str($_POST["usertype"]);
		$usercategory = check_str($_POST["usercategory"]);
		$userfirstname = check_str($_POST["userfirstname"]);
		$userlastname = check_str($_POST["userlastname"]);
		$usercompanyname = check_str($_POST["usercompanyname"]);
		$userphysicaladdress1 = check_str($_POST["userphysicaladdress1"]);
		$userphysicaladdress2 = check_str($_POST["userphysicaladdress2"]);
		$userphysicalcity = check_str($_POST["userphysicalcity"]);
		$userphysicalstateprovince = check_str($_POST["userphysicalstateprovince"]);
		$userphysicalpostalcode = check_str($_POST["userphysicalpostalcode"]);
		$userphysicalcountry = check_str($_POST["userphysicalcountry"]);
		$usermailingaddress1 = check_str($_POST["usermailingaddress1"]);
		$usermailingaddress2 = check_str($_POST["usermailingaddress2"]);
		$usermailingcity = check_str($_POST["usermailingcity"]);
		$usermailingstateprovince = check_str($_POST["usermailingstateprovince"]);
		$usermailingpostalcode = check_str($_POST["usermailingpostalcode"]);
		$usermailingcountry = check_str($_POST["usermailingcountry"]);
		$userbillingaddress1 = check_str($_POST["userbillingaddress1"]);
		$userbillingaddress2 = check_str($_POST["userbillingaddress2"]);
		$userbillingcity = check_str($_POST["userbillingcity"]);
		$userbillingstateprovince = check_str($_POST["userbillingstateprovince"]);
		$userbillingpostalcode = check_str($_POST["userbillingpostalcode"]);
		$userbillingcountry = check_str($_POST["userbillingcountry"]);
		$usershippingaddress1 = check_str($_POST["usershippingaddress1"]);
		$usershippingaddress2 = check_str($_POST["usershippingaddress2"]);
		$usershippingcity = check_str($_POST["usershippingcity"]);
		$usershippingstateprovince = check_str($_POST["usershippingstateprovince"]);
		$usershippingpostalcode = check_str($_POST["usershippingpostalcode"]);
		$usershippingcountry = check_str($_POST["usershippingcountry"]);
		$userphone1 = check_str($_POST["userphone1"]);
		$userphone1ext = check_str($_POST["userphone1ext"]);
		$userphone2 = check_str($_POST["userphone2"]);
		$userphone2ext = check_str($_POST["userphone2ext"]);
		$userphonemobile = check_str($_POST["userphonemobile"]);
		$userphonefax = check_str($_POST["userphonefax"]);
		$userphoneemergencymobile = check_str($_POST["userphoneemergencymobile"]);
		$useremailemergency = check_str($_POST["useremailemergency"]);
		$useremail = check_str($_POST["useremail"]);
		$userurl = check_str($_POST["userurl"]);
		$usernotes = check_str($_POST["usernotes"]);
		$useroptional1 = check_str($_POST["useroptional1"]);
		$useradduser = check_str($_POST["useradduser"]);
		$useradddate = check_str($_POST["useradddate"]);
	}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';
	if ($action == "update") {
		$id = check_str($_POST["id"]);
	}

	//check for all required data
		//if (strlen($username) == 0) { $msg .= "Please provide: Username<br>\n"; }
		//if (strlen($password) == 0) { $msg .= "Please provide: Password<br>\n"; }
		//if (strlen($usertype) == 0) { $msg .= "Please provide: Type<br>\n"; }
		//if (strlen($usercategory) == 0) { $msg .= "Please provide: Category<br>\n"; }
		//if (strlen($userfirstname) == 0) { $msg .= "Please provide: First Name<br>\n"; }
		//if (strlen($userlastname) == 0) { $msg .= "Please provide: Last Name<br>\n"; }
		//if (strlen($usercompanyname) == 0) { $msg .= "Please provide: Organization<br>\n"; }
		//if (strlen($userphysicaladdress1) == 0) { $msg .= "Please provide: Address 1<br>\n"; }
		//if (strlen($userphysicaladdress2) == 0) { $msg .= "Please provide: Address 2<br>\n"; }
		//if (strlen($userphysicalcity) == 0) { $msg .= "Please provide: City<br>\n"; }
		//if (strlen($userphysicalstateprovince) == 0) { $msg .= "Please provide: State/Province<br>\n"; }
		//if (strlen($userphysicalpostalcode) == 0) { $msg .= "Please provide: Postal Code<br>\n"; }
		//if (strlen($userphysicalcountry) == 0) { $msg .= "Please provide: Country<br>\n"; }
		//if (strlen($usermailingaddress1) == 0) { $msg .= "Please provide: Address 1<br>\n"; }
		//if (strlen($usermailingaddress2) == 0) { $msg .= "Please provide: Address 2<br>\n"; }
		//if (strlen($usermailingcity) == 0) { $msg .= "Please provide: City<br>\n"; }
		//if (strlen($usermailingstateprovince) == 0) { $msg .= "Please provide: State/Province<br>\n"; }
		//if (strlen($usermailingpostalcode) == 0) { $msg .= "Please provide: Postal Code<br>\n"; }
		//if (strlen($usermailingcountry) == 0) { $msg .= "Please provide: Country<br>\n"; }
		//if (strlen($userbillingaddress1) == 0) { $msg .= "Please provide: Address 1<br>\n"; }
		//if (strlen($userbillingaddress2) == 0) { $msg .= "Please provide: Address 2<br>\n"; }
		//if (strlen($userbillingcity) == 0) { $msg .= "Please provide: City<br>\n"; }
		//if (strlen($userbillingstateprovince) == 0) { $msg .= "Please provide: State/Province<br>\n"; }
		//if (strlen($userbillingpostalcode) == 0) { $msg .= "Please provide: Postal Code<br>\n"; }
		//if (strlen($userbillingcountry) == 0) { $msg .= "Please provide: Country<br>\n"; }
		//if (strlen($usershippingaddress1) == 0) { $msg .= "Please provide: Address 1<br>\n"; }
		//if (strlen($usershippingaddress2) == 0) { $msg .= "Please provide: Address 2<br>\n"; }
		//if (strlen($usershippingcity) == 0) { $msg .= "Please provide: City<br>\n"; }
		//if (strlen($usershippingstateprovince) == 0) { $msg .= "Please provide: State/Province<br>\n"; }
		//if (strlen($usershippingpostalcode) == 0) { $msg .= "Please provide: Postal Code<br>\n"; }
		//if (strlen($usershippingcountry) == 0) { $msg .= "Please provide: Country<br>\n"; }
		//if (strlen($userphone1) == 0) { $msg .= "Please provide: Phone 1<br>\n"; }
		//if (strlen($userphone1ext) == 0) { $msg .= "Please provide: Ext 1<br>\n"; }
		//if (strlen($userphone2) == 0) { $msg .= "Please provide: Phone 2<br>\n"; }
		//if (strlen($userphone2ext) == 0) { $msg .= "Please provide: Ext 2<br>\n"; }
		//if (strlen($userphonemobile) == 0) { $msg .= "Please provide: Mobile<br>\n"; }
		//if (strlen($userphonefax) == 0) { $msg .= "Please provide: FAX<br>\n"; }
		//if (strlen($userphoneemergencymobile) == 0) { $msg .= "Please provide: Emergency Mobile<br>\n"; }
		//if (strlen($useremailemergency) == 0) { $msg .= "Please provide: Emergency Email<br>\n"; }
		//if (strlen($useremail) == 0) { $msg .= "Please provide: Email<br>\n"; }
		//if (strlen($userurl) == 0) { $msg .= "Please provide: URL<br>\n"; }
		//if (strlen($usernotes) == 0) { $msg .= "Please provide: Notes<br>\n"; }
		//if (strlen($useroptional1) == 0) { $msg .= "Please provide: Optional 1<br>\n"; }
		//if (strlen($useradduser) == 0) { $msg .= "Please provide: Add User<br>\n"; }
		//if (strlen($useradddate) == 0) { $msg .= "Please provide: Add Date<br>\n"; }
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
					$sql .= "v_id, ";
					//$sql .= "username, ";
					//$sql .= "password, ";
					$sql .= "usertype, ";
					$sql .= "usercategory, ";
					$sql .= "userfirstname, ";
					$sql .= "userlastname, ";
					$sql .= "usercompanyname, ";
					$sql .= "userphysicaladdress1, ";
					$sql .= "userphysicaladdress2, ";
					$sql .= "userphysicalcity, ";
					$sql .= "userphysicalstateprovince, ";
					$sql .= "userphysicalpostalcode, ";
					$sql .= "userphysicalcountry, ";
					$sql .= "usermailingaddress1, ";
					$sql .= "usermailingaddress2, ";
					$sql .= "usermailingcity, ";
					$sql .= "usermailingstateprovince, ";
					$sql .= "usermailingpostalcode, ";
					$sql .= "usermailingcountry, ";
					$sql .= "userbillingaddress1, ";
					$sql .= "userbillingaddress2, ";
					$sql .= "userbillingcity, ";
					$sql .= "userbillingstateprovince, ";
					$sql .= "userbillingpostalcode, ";
					$sql .= "userbillingcountry, ";
					$sql .= "usershippingaddress1, ";
					$sql .= "usershippingaddress2, ";
					$sql .= "usershippingcity, ";
					$sql .= "usershippingstateprovince, ";
					$sql .= "usershippingpostalcode, ";
					$sql .= "usershippingcountry, ";
					$sql .= "userphone1, ";
					$sql .= "userphone1ext, ";
					$sql .= "userphone2, ";
					$sql .= "userphone2ext, ";
					$sql .= "userphonemobile, ";
					$sql .= "userphonefax, ";
					$sql .= "userphoneemergencymobile, ";
					$sql .= "useremailemergency, ";
					$sql .= "useremail, ";
					$sql .= "userurl, ";
					$sql .= "usernotes, ";
					$sql .= "useroptional1, ";
					$sql .= "useradduser, ";
					$sql .= "useradddate ";
					$sql .= ")";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'$v_id', ";
					//$sql .= "'$username', ";
					//$sql .= "'$password', ";
					$sql .= "'$usertype', ";
					$sql .= "'$usercategory', ";
					$sql .= "'$userfirstname', ";
					$sql .= "'$userlastname', ";
					$sql .= "'$usercompanyname', ";
					$sql .= "'$userphysicaladdress1', ";
					$sql .= "'$userphysicaladdress2', ";
					$sql .= "'$userphysicalcity', ";
					$sql .= "'$userphysicalstateprovince', ";
					$sql .= "'$userphysicalpostalcode', ";
					$sql .= "'$userphysicalcountry', ";
					$sql .= "'$usermailingaddress1', ";
					$sql .= "'$usermailingaddress2', ";
					$sql .= "'$usermailingcity', ";
					$sql .= "'$usermailingstateprovince', ";
					$sql .= "'$usermailingpostalcode', ";
					$sql .= "'$usermailingcountry', ";
					$sql .= "'$userbillingaddress1', ";
					$sql .= "'$userbillingaddress2', ";
					$sql .= "'$userbillingcity', ";
					$sql .= "'$userbillingstateprovince', ";
					$sql .= "'$userbillingpostalcode', ";
					$sql .= "'$userbillingcountry', ";
					$sql .= "'$usershippingaddress1', ";
					$sql .= "'$usershippingaddress2', ";
					$sql .= "'$usershippingcity', ";
					$sql .= "'$usershippingstateprovince', ";
					$sql .= "'$usershippingpostalcode', ";
					$sql .= "'$usershippingcountry', ";
					$sql .= "'$userphone1', ";
					$sql .= "'$userphone1ext', ";
					$sql .= "'$userphone2', ";
					$sql .= "'$userphone2ext', ";
					$sql .= "'$userphonemobile', ";
					$sql .= "'$userphonefax', ";
					$sql .= "'$userphoneemergencymobile', ";
					$sql .= "'$useremailemergency', ";
					$sql .= "'$useremail', ";
					$sql .= "'$userurl', ";
					$sql .= "'$usernotes', ";
					$sql .= "'$useroptional1', ";
					$sql .= "'$useradduser', ";
					$sql .= "'$useradddate' ";
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
					$sql .= "usertype = '$usertype', ";
					$sql .= "usercategory = '$usercategory', ";
					$sql .= "userfirstname = '$userfirstname', ";
					$sql .= "userlastname = '$userlastname', ";
					$sql .= "usercompanyname = '$usercompanyname', ";
					$sql .= "userphysicaladdress1 = '$userphysicaladdress1', ";
					$sql .= "userphysicaladdress2 = '$userphysicaladdress2', ";
					$sql .= "userphysicalcity = '$userphysicalcity', ";
					$sql .= "userphysicalstateprovince = '$userphysicalstateprovince', ";
					$sql .= "userphysicalpostalcode = '$userphysicalpostalcode', ";
					$sql .= "userphysicalcountry = '$userphysicalcountry', ";
					$sql .= "usermailingaddress1 = '$usermailingaddress1', ";
					$sql .= "usermailingaddress2 = '$usermailingaddress2', ";
					$sql .= "usermailingcity = '$usermailingcity', ";
					$sql .= "usermailingstateprovince = '$usermailingstateprovince', ";
					$sql .= "usermailingpostalcode = '$usermailingpostalcode', ";
					$sql .= "usermailingcountry = '$usermailingcountry', ";
					$sql .= "userbillingaddress1 = '$userbillingaddress1', ";
					$sql .= "userbillingaddress2 = '$userbillingaddress2', ";
					$sql .= "userbillingcity = '$userbillingcity', ";
					$sql .= "userbillingstateprovince = '$userbillingstateprovince', ";
					$sql .= "userbillingpostalcode = '$userbillingpostalcode', ";
					$sql .= "userbillingcountry = '$userbillingcountry', ";
					$sql .= "usershippingaddress1 = '$usershippingaddress1', ";
					$sql .= "usershippingaddress2 = '$usershippingaddress2', ";
					$sql .= "usershippingcity = '$usershippingcity', ";
					$sql .= "usershippingstateprovince = '$usershippingstateprovince', ";
					$sql .= "usershippingpostalcode = '$usershippingpostalcode', ";
					$sql .= "usershippingcountry = '$usershippingcountry', ";
					$sql .= "userphone1 = '$userphone1', ";
					$sql .= "userphone1ext = '$userphone1ext', ";
					$sql .= "userphone2 = '$userphone2', ";
					$sql .= "userphone2ext = '$userphone2ext', ";
					$sql .= "userphonemobile = '$userphonemobile', ";
					$sql .= "userphonefax = '$userphonefax', ";
					$sql .= "userphoneemergencymobile = '$userphoneemergencymobile', ";
					$sql .= "useremailemergency = '$useremailemergency', ";
					$sql .= "useremail = '$useremail', ";
					$sql .= "userurl = '$userurl', ";
					$sql .= "usernotes = '$usernotes', ";
					$sql .= "useroptional1 = '$useroptional1' ";
					//$sql .= "useradduser = '$useradduser', ";
					//$sql .= "useradddate = '$useradddate' ";
					$sql .= "where v_id = '$v_id' ";
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
			$sql .= "where v_id = '$v_id' ";
			$sql .= "and id = '$id' ";
			$prepstatement = $db->prepare(check_sql($sql));
			$prepstatement->execute();
			$result = $prepstatement->fetchAll();
			foreach ($result as &$row) {
				//$username = $row["username"];
				//$password = $row["password"];
				$usertype = $row["usertype"];
				$usercategory = $row["usercategory"];
				$userfirstname = $row["userfirstname"];
				$userlastname = $row["userlastname"];
				$usercompanyname = $row["usercompanyname"];
				$userphysicaladdress1 = $row["userphysicaladdress1"];
				$userphysicaladdress2 = $row["userphysicaladdress2"];
				$userphysicalcity = $row["userphysicalcity"];
				$userphysicalstateprovince = $row["userphysicalstateprovince"];
				$userphysicalpostalcode = $row["userphysicalpostalcode"];
				$userphysicalcountry = $row["userphysicalcountry"];
				$usermailingaddress1 = $row["usermailingaddress1"];
				$usermailingaddress2 = $row["usermailingaddress2"];
				$usermailingcity = $row["usermailingcity"];
				$usermailingstateprovince = $row["usermailingstateprovince"];
				$usermailingpostalcode = $row["usermailingpostalcode"];
				$usermailingcountry = $row["usermailingcountry"];
				$userbillingaddress1 = $row["userbillingaddress1"];
				$userbillingaddress2 = $row["userbillingaddress2"];
				$userbillingcity = $row["userbillingcity"];
				$userbillingstateprovince = $row["userbillingstateprovince"];
				$userbillingpostalcode = $row["userbillingpostalcode"];
				$userbillingcountry = $row["userbillingcountry"];
				$usershippingaddress1 = $row["usershippingaddress1"];
				$usershippingaddress2 = $row["usershippingaddress2"];
				$usershippingcity = $row["usershippingcity"];
				$usershippingstateprovince = $row["usershippingstateprovince"];
				$usershippingpostalcode = $row["usershippingpostalcode"];
				$usershippingcountry = $row["usershippingcountry"];
				$userphone1 = $row["userphone1"];
				$userphone1ext = $row["userphone1ext"];
				$userphone2 = $row["userphone2"];
				$userphone2ext = $row["userphone2ext"];
				$userphonemobile = $row["userphonemobile"];
				$userphonefax = $row["userphonefax"];
				$userphoneemergencymobile = $row["userphoneemergencymobile"];
				$useremailemergency = $row["useremailemergency"];
				$useremail = $row["useremail"];
				$userurl = $row["userurl"];
				$usernotes = $row["usernotes"];
				$useroptional1 = $row["useroptional1"];
				//$useradduser = $row["useradduser"];
				//$useradddate = $row["useradddate"];
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
			echo "	<select style='width: 80%;' class='formfld' name='usertype'>\n";
			echo "	<option value=''></option>\n";
			if ($usertype == "Individual") {
				echo "	<option value='Individual' selected>Individual</option>\n";
			}
			else {
				echo "	<option value='Individual'>Individual</option>\n";
			}
			if ($usertype == "Organization") {
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
			echo "	<input style='width: 80%;' class='formfld' type='text' name='usercategory' maxlength='255' value=\"$usercategory\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	First Name:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' style='' type='text' name='userfirstname' maxlength='255' value=\"$userfirstname\"> <span class='smalltext'>vcard</span>\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Last Name:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='userlastname' maxlength='255' value=\"$userlastname\"> <span class='smalltext'>vcard</span>\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Organization:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='usercompanyname' maxlength='255' value=\"$usercompanyname\"> <span class='smalltext'>vcard</span>\n";
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
			echo "	<input style='width: 80%;' class='formfld' type='text' name='userphone1' maxlength='255' value=\"$userphone1\"> <span class='smalltext'>vcard</span>\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Ext 1:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='userphone1ext' maxlength='255' value=\"$userphone1ext\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Phone 2:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='userphone2' maxlength='255' value=\"$userphone2\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Ext 2:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='userphone2ext' maxlength='255' value=\"$userphone2ext\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Mobile:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='userphonemobile' maxlength='255' value=\"$userphonemobile\"> <span class='smalltext'>vcard</span>\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	FAX:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='userphonefax' maxlength='255' value=\"$userphonefax\"> <span class='smalltext'>vcard</span>\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Emergency Mobile:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='userphoneemergencymobile' maxlength='255' value=\"$userphoneemergencymobile\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Emergency Email:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='useremailemergency' maxlength='255' value=\"$useremailemergency\">\n";
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
			echo "	<input style='width: 80%;' class='formfld' type='text' name='useremail' maxlength='255' value=\"$useremail\"> <span class='smalltext'>vcard</span>\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	URL:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='userurl' maxlength='255' value=\"$userurl\"> <span class='smalltext'>vcard</span>\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Notes:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<textarea style='width: 80%;' class='formfld' type='text' name='usernotes' rows='5'>$usernotes</textarea> <span class='smalltext'>vcard</span>\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			//echo "<tr>\n";
			//echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			//echo "	Optional 1:\n";
			//echo "</td>\n";
			//echo "<td class='vtable' align='left'>\n";
			//echo "	<input style='width: 80%;' class='formfld' type='text' name='useroptional1' maxlength='255' value=\"$useroptional1\">\n";
			//echo "<br />\n";
			//echo "\n";
			//echo "</td>\n";
			//echo "</tr>\n";

			//echo "<tr>\n";
			//echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			//echo "	Optional 1:\n";
			//echo "</td>\n";
			//echo "<td class='vtable' align='left'>\n";
			//echo "	<input style='width: 80%;' class='formfld' type='text' name='useroptional2' maxlength='255' value=\"$useroptional2\">\n";
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
			echo "	<input style='width: 80%;' class='formfld' type='text' name='userphysicaladdress1' maxlength='255' value=\"$userphysicaladdress1\"> <span class='smalltext'>vcard</span>\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Address 2:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='userphysicaladdress2' maxlength='255' value=\"$userphysicaladdress2\"> <span class='smalltext'>vcard</span>\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	City:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='userphysicalcity' maxlength='255' value=\"$userphysicalcity\"> <span class='smalltext'>vcard</span>\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	State/Province:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='userphysicalstateprovince' maxlength='255' value=\"$userphysicalstateprovince\"> <span class='smalltext'>vcard</span>\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Postal Code:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='userphysicalpostalcode' maxlength='255' value=\"$userphysicalpostalcode\"> <span class='smalltext'>vcard</span>\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Country:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='userphysicalcountry' maxlength='255' value=\"$userphysicalcountry\"> <span class='smalltext'>vcard</span>\n";
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
			echo "	<input style='width: 80%;' class='formfld' type='text' name='usermailingaddress1' maxlength='255' value=\"$usermailingaddress1\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Address 2:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='usermailingaddress2' maxlength='255' value=\"$usermailingaddress2\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	City:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='usermailingcity' maxlength='255' value=\"$usermailingcity\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	State/Province:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='usermailingstateprovince' maxlength='255' value=\"$usermailingstateprovince\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Postal Code:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='usermailingpostalcode' maxlength='255' value=\"$usermailingpostalcode\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Country:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='usermailingcountry' maxlength='255' value=\"$usermailingcountry\">\n";
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
			echo "	<input style='width: 80%;' class='formfld' type='text' name='userbillingaddress1' maxlength='255' value=\"$userbillingaddress1\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Address 2:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='userbillingaddress2' maxlength='255' value=\"$userbillingaddress2\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	City:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='userbillingcity' maxlength='255' value=\"$userbillingcity\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	State/Province:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='userbillingstateprovince' maxlength='255' value=\"$userbillingstateprovince\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Postal Code:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='userbillingpostalcode' maxlength='255' value=\"$userbillingpostalcode\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Country:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='userbillingcountry' maxlength='255' value=\"$userbillingcountry\">\n";
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
			echo "	<input style='width: 80%;' class='formfld' type='text' name='usershippingaddress1' maxlength='255' value=\"$usershippingaddress1\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Address 2:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='usershippingaddress2' maxlength='255' value=\"$usershippingaddress2\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	City:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='usershippingcity' maxlength='255' value=\"$usershippingcity\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	State/Province:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='usershippingstateprovince' maxlength='255' value=\"$usershippingstateprovince\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Postal Code:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='usershippingpostalcode' maxlength='255' value=\"$usershippingpostalcode\">\n";
			echo "<br />\n";
			echo "\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			echo "	Country:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input style='width: 80%;' class='formfld' type='text' name='usershippingcountry' maxlength='255' value=\"$usershippingcountry\">\n";
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
			//echo "	<input style='width: 80%;' class='formfld' type='text' name='useradduser' maxlength='255' value=\"$useradduser\">\n";
			//echo "<br />\n";
			//echo "\n";
			//echo "</td>\n";
			//echo "</tr>\n";

			//echo "<tr>\n";
			//echo "<td class='vncell' valign='top' align='left' nowrap>\n";
			//echo "	Add Date:\n";
			//echo "</td>\n";
			//echo "<td class='vtable' align='left'>\n";
			//echo "	<input style='width: 80%;' class='formfld' type='text' name='useradddate' maxlength='255' value=\"$useradddate\">\n";
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
