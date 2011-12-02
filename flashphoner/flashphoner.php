<?php
/* $Id$ */
/*
	flashphoner.php
	Copyright (C) 2008, 2009 Ken Rice
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
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (permission_exists('flashphoner_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}
require_once "includes/header.php";

$extension_id = $_SESSION['user_extension_array'][0]['extension_id'];
$extension = $_SESSION['user_extension_array'][0]['extension'];

//get a list of assigned extensions for this user
$sql = "";
$sql .= "select * from v_extensions ";
$sql .= "where v_id = '$v_id' ";
$sql .= "and user_list like '%|".$_SESSION["username"]."|%' ";
$prepstatement = $db->prepare(check_sql($sql));
$prepstatement->execute();

$x = 0;
$result = $prepstatement->fetchAll();
foreach ($result as &$row) {
	$extension_array[$x]['extension_id'] = $row["extension_id"];
	$extension_array[$x]['extension'] = $row["extension"];
	$x++;
}

unset ($prepstatement);

if ($x > 0) {
	$key = guid();
	$client_ip = $_SERVER['REMOTE_ADDR'];
	$sql = sprintf("INSERT INTO v_flashphone_auth (auth_key, hostaddr, createtime, username) values ('%s', '%s', now(), '%s')",
			$key, $client_ip, $_SESSION["username"]);
	$db->exec(check_sql($sql));
}

// Abort here if we dont have an extension for them and tell them to get one assigned
if ($x < 1) {
	echo "This user does not have an extension assigned, please Contact your system adminstrator if you feel this is in error<br />\n";
} else if ($x == 1) {
	// DISPLAY THE PHONE HERE
	$extension = $extension_array[0]['extension'];
	$extension_id = $extension_array[0]['extension_id'];
	include "phone_html.php";
} else {
	include "phone_choices_html.php";
}

//show the footer
require_once "includes/footer.php";
?>
