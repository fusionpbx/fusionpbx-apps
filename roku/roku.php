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
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ); //hide notices and warnings

//roku commands
	//press up
	//press down
	//press left
	//press right
	//press select
	//press home
	//press fwd
	//press back
	//press pause 

if (strlen($_SERVER['argv'][1]) > 0) { 
	$cmd = $_SERVER['argv'][1];
	switch ($cmd) {
	case "1":
		$cmd = "home";
		break;
	case "2":
		$cmd = "up";
		break;
	case "3":
		$cmd = "home";
		break;
	case "4":
		$cmd = "left";
		break;
	case "5":
		$cmd = "select";
		break;
	case "6":
		$cmd = "right";
		break;
	case "7":
		$cmd = "back";
		break;
	case "8":
		$cmd = "down";
		break;
	case "9":
		$cmd = "fwd";
		break;
	case "0":
		$cmd = "pause"; //toggle play/pause
		break;
	case "#":
		$cmd = "pause"; //toggle play/pause
		break;
	default:
		$cmd = "pause"; //toggle play/pause
	}
}
if (strlen($cmd)==0) {
	if (strlen($_GET['cmd']) > 0) { $cmd = $_GET['cmd']; }
}

$host = $_SERVER['argv'][2];
$port = $_SERVER['argv'][3];

$cmd = "press ".$cmd;

$fp = fsockopen($host, $port, $errno, $errdesc) or die("Connection to $host $errno $errdesc failed");
fputs($fp, $cmd."\r\n");

?>