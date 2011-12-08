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
if (permission_exists('contacts_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

require_once "includes/class_vcard.php";
$vc = new vcard();

if (count($_GET)>0) {
	$id = $_GET["id"];
	$sql = "";
	$sql .= "select * from v_users ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and id = '$id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {

		$vc->data[display_name] = $row["userfirstname"]." ".$row["userlastname"];
		//$vc->data[zzz] = $row["usertype"];
		//$vc->data[zzz] = $row["usercategory"];
		$vc->data[first_name] = $row["userfirstname"];
		$vc->data[last_name] = $row["userlastname"];
		$vc->data[company] = $row["usercompanyname"];

		$vc->data[work_address] = $row["userphysicaladdress1"];
		$vc->data[work_extended_address] = $row["userphysicaladdress2"];
		$vc->data[work_city] = $row["userphysicalcity"];
		$vc->data[work_state] = $row["userphysicalstateprovince"];
		$vc->data[work_postal_code] = $row["userphysicalpostalcode"];
		$vc->data[work_country] = $row["userphysicalcountry"];

		$vc->data[home_address] = $row["userphysicaladdress1"];
		$vc->data[home_extended_address] = $row["userphysicaladdress2"];
		$vc->data[home_city] = $row["userphysicalcity"];
		$vc->data[home_state] = $row["userphysicalstateprovince"];
		$vc->data[home_postal_code] = $row["userphysicalpostalcode"];
		$vc->data[home_country] = $row["userphysicalcountry"];

		//$vc->data[zzz] = $row["usermailingaddress1"];
		//$vc->data[zzz] = $row["usermailingaddress2"];
		//$vc->data[zzz] = $row["usermailingcity"];
		//$vc->data[zzz] = $row["usermailingstateprovince"];
		//$vc->data[zzz] = $row["usermailingpostalcode"];
		//$vc->data[zzz] = $row["usermailingcountry"];
		//$vc->data[zzz] = $row["userbillingaddress1"];
		//$vc->data[zzz] = $row["userbillingaddress2"];
		//$vc->data[zzz] = $row["userbillingcity"];
		//$vc->data[zzz] = $row["userbillingstateprovince"];
		//$vc->data[zzz] = $row["userbillingpostalcode"];
		//$vc->data[zzz] = $row["userbillingcountry"];
		//$vc->data[zzz] = $row["usershippingaddress1"];
		//$vc->data[zzz] = $row["usershippingaddress2"];
		//$vc->data[zzz] = $row["usershippingcity"];
		//$vc->data[zzz] = $row["usershippingstateprovince"];
		//$vc->data[zzz] = $row["usershippingpostalcode"];
		//$vc->data[zzz] = $row["usershippingcountry"];
		$vc->data[office_tel] = $row["userphone1"];
		$vc->data[home_tel] = $row["userphone1"];
		//$vc->data[zzz] = $row["userphone1ext"];
		//$vc->data[zzz] = $row["userphone2"];
		//$vc->data[zzz] = $row["userphone2ext"];
		$vc->data[cell_tel] = $row["userphonemobile"];
		$vc->data[fax_tel] = $row["userphonefax"];
		//$vc->data[zzz] = $row["userphoneemergencymobile"];
		//$vc->data[zzz] = $row["useremailemergency"];
		$vc->data[email1] = $row["useremail"];
		$vc->data[url] = $row["userurl"];
		$vc->data[note] = $row["usernotes"];

		/*
		//additional un accounted fields
		additional_name
		name_prefix
		name_suffix
		nickname
		title
		role
		department
		work_po_box
		home_po_box
		home_extended_address
		home_address
		home_city
		home_state
		home_postal_code
		home_country
		pager_tel
		email2
		photo
		birthday
		timezone
		sort_string
		*/

		break; //limit to 1 row
	}
	unset ($prepstatement);
}

$vc->download();

?>