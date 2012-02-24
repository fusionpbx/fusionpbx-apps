<?php
/* $Id$ */
/*
	call.php
	Copyright (C) 2008, 2009 Mark J Crane
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice,
	   this list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGE.
*/

include "root.php";
require_once "includes/require.php";
/*
 require_once "includes/checkauth.php";
if (permission_exists('flashphoner_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
} */
$extension_uuid = $_REQUEST['extension_uuid'];
$key = $_REQUEST['key'];
$username = $_REQUEST['username'];

// make sure they atleast have a KEY from before
$sql = sprintf("select * from v_flashphone_auth where auth_key = '%s' and hostaddr = '%s' and username = '%s';",
		urldecode($key),
		$_SERVER['REMOTE_ADDR'],
		$username);
	
$prep_statement = $db->prepare(check_sql($sql));
if (!$prep_statement) {
	echo "\nPDO::errorInfo():\n";
	print_r($db->errorInfo());
}

$prep_statement->execute();
$x = 0;
$result = $prep_statement->fetchAll();

// There is probably a better way to do this but this will work on anything
foreach ($result as &$row) {
	$auth_array[$x] = $row;
	$x++;
}
if ($x < 1) {
	die("<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n<ERROR>UNAUTHORIZED ACCESS</ERROR>");
}
unset ($prep_statement);

//get a list of assigned extensions for this user
$sql = "";
$sql .= " select * from v_extensions ";
$sql .= "where domain_uuid = '$domain_uuid' ";
$sql .= "and enabled = 'true' ";
if (count($_SESSION['user']['extension']) > 0) {
	$sql .= "and (";
	$x = 0;
	foreach($_SESSION['user']['extension'] as $row) {
		if ($x > 0) { $sql .= "or "; }
		$sql .= "extension = '".$row['user']."' ";
		$x++;
	}
	$sql .= ")";
}
else {
	//hide any results when a user has not been assigned an extension
	$sql .= "and extension = 'disabled' ";
}
$sql .= "order by extension asc ";
$prep_statement = $db->prepare(check_sql($sql));
$prep_statement->execute();
$x = 0;
$result = $prep_statement->fetchAll();
foreach ($result as &$row) {
	$extension_array[$x]['extension_uuid'] = $row["extension_uuid"];
	$extension_array[$x]['extension'] = $row["extension"];
	$extension_array[$x]['password'] = $row["password"];
	$extension_array[$x]['user_context'] = $row["user_context"];
	$x++;
}

unset ($prep_statement);

if ($x == 1) {
header('Content-Type: text/xml');

?>
<?xml version="1.0" encoding="utf-8" ?>
<fusionpbx version="1.0">
	<flashphoner>
		<login><?php echo $extension_array[0]['extension']; ?></login>
		<password><?php echo $extension_array[0]['password']; ?></password>
		<proxy><?php echo $extension_array[0]['user_context']; ?></proxy>
		<port>5060</port>
	</flashphoner>
</fusionpbx>
<?php
} 
?>
