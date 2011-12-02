<?php
/* $Id$ */
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
	Ken Rice <krice@tollfreegateway.com>
	Mark J Crane <markjcrane@fusionpbx.com>
*/
include "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (permission_exists('xmpp_delete')) {
        //access granted
}
else {
        echo "access denied";
        exit;
}

require_once "includes/header.php";

$v_domain = $_SESSION['domains'][$v_id]['domain'];

$profile_id = $_REQUEST['id'];

$sql = "";
$sql .= "select * from v_xmpp ";
$sql .= "where v_id = '$v_id' ";
$sql .= "and xmpp_profile_id = '$profile_id' ";

$prepstatement = $db->prepare(check_sql($sql));
$prepstatement->execute();

$x = 0;
$result = $prepstatement->fetchAll();
foreach ($result as &$row) {
	$profiles_array[$x] = $row;
	$x++;
}

$profile = $profiles_array[0];
unset ($prepstatement);

$sql = "";
$sql .= "delete from v_xmpp ";
$sql .= "where v_id = '$v_id' ";
$sql .= "and xmpp_profile_id = '$profile_id' ";

$db->exec(check_sql($sql));

$filename = $v_conf_dir . "/jingle_profiles/" . "v_" . $v_domain . "_" . 
	preg_replace("/[^A-Za-z0-9]/", "", $profile['profile_name']) . "_" . $profile_id . ".xml";

unlink($filename);

$fp = event_socket_create($_SESSION['event_socket_ip_address'], $_SESSION['event_socket_port'], $_SESSION['event_socket_password']);
if ($fp) {
	//reload the XML Configs
	$tmp_cmd = 'api reloadxml';
	$response = event_socket_request($fp, $tmp_cmd);
	unset($tmp_cmd);

	//Tell mod_dingaling to reload is config
	$tmp_cmd = 'api dingaling reload';
	$response = event_socket_request($fp, $tmp_cmd);
	unset($tmp_cmd);

	//close the connection
	fclose($fp);
}

include "update_complete.php";

end:
//show the footer
require_once "includes/footer.php";

?>
