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
include "root.php";
require_once "resources/require.php";
require_once "resources/check_auth.php";
if (permission_exists('contact_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

require_once "resources/classes/vcard.php";
$vc = new vcard();

if (count($_GET)>0) {
	$id = $_GET["id"];
	$sql = "";
	$sql .= "select * from v_users ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and id = '$id' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
	foreach ($result as &$row) {

		$vc->data[display_name] = $row["user_first_name"]." ".$row["user_last_name"];
		//$vc->data[zzz] = $row["user_type"];
		//$vc->data[zzz] = $row["user_category"];
		$vc->data[first_name] = $row["user_first_name"];
		$vc->data[last_name] = $row["user_last_name"];
		$vc->data[company] = $row["user_company_name"];

		$vc->data[work_address] = $row["user_physical_address_1"];
		$vc->data[work_extended_address] = $row["user_physical_address_2"];
		$vc->data[work_city] = $row["user_physical_city"];
		$vc->data[work_state] = $row["user_physical_state_province"];
		$vc->data[work_postal_code] = $row["user_physical_postal_code"];
		$vc->data[work_country] = $row["user_physical_country"];

		$vc->data[home_address] = $row["user_physical_address_1"];
		$vc->data[home_extended_address] = $row["user_physical_address_2"];
		$vc->data[home_city] = $row["user_physical_city"];
		$vc->data[home_state] = $row["user_physical_state_province"];
		$vc->data[home_postal_code] = $row["user_physical_postal_code"];
		$vc->data[home_country] = $row["user_physical_country"];

		//$vc->data[zzz] = $row["user_mailing_address_1"];
		//$vc->data[zzz] = $row["user_mailing_address_2"];
		//$vc->data[zzz] = $row["user_mailing_city"];
		//$vc->data[zzz] = $row["user_mailing_state_province"];
		//$vc->data[zzz] = $row["user_mailing_postal_code"];
		//$vc->data[zzz] = $row["user_mailing_country"];
		//$vc->data[zzz] = $row["user_billing_address_1"];
		//$vc->data[zzz] = $row["user_billing_address_2"];
		//$vc->data[zzz] = $row["user_billing_city"];
		//$vc->data[zzz] = $row["user_billing_state_province"];
		//$vc->data[zzz] = $row["user_billing_postal_code"];
		//$vc->data[zzz] = $row["user_billing_country"];
		//$vc->data[zzz] = $row["user_shipping_address_1"];
		//$vc->data[zzz] = $row["user_shipping_address_2"];
		//$vc->data[zzz] = $row["user_shipping_city"];
		//$vc->data[zzz] = $row["user_shipping_state_province"];
		//$vc->data[zzz] = $row["user_shipping_postal_code"];
		//$vc->data[zzz] = $row["user_shipping_country"];
		$vc->data[office_tel] = $row["user_phone_1"];
		$vc->data[home_tel] = $row["user_phone_1"];
		//$vc->data[zzz] = $row["user_phone_1_ext"];
		//$vc->data[zzz] = $row["user_phone_2"];
		//$vc->data[zzz] = $row["user_phone_2_ext"];
		$vc->data[cell_tel] = $row["user_phone_mobile"];
		$vc->data[fax_tel] = $row["user_phone_fax"];
		//$vc->data[zzz] = $row["user_phone_emergency_mobile"];
		//$vc->data[zzz] = $row["user_email_emergency"];
		$vc->data[email1] = $row["user_email"];
		$vc->data[url] = $row["user_url"];
		$vc->data[note] = $row["user_notes"];

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
	unset ($prep_statement);
}

$vc->download();

?>