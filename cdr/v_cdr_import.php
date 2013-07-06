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
require "resources/require.php";
if(php_sapi_name() == 'cli') {
	//allow access for command line interface
}
else {
	//require authentication
	require_once "resources/check_auth.php";
	if (permission_exists('cdr_csv_view')) {
		//access granted
	}
	else {
		echo "access denied";
		exit;
	}
}

require "includes/lib_cdr.php";

//---- begin import cdr records -----------------------------------------------------------------------------------

	$v_file = $_SESSION['switch']['log']['dir']."/cdr-csv/Master.csv";
	//echo filesize($v_file);
	//Open file (DON'T USE a+ pointer will be wrong!)
	$fh = fopen($v_file, 'r');
	
	$read = 524288;
	//$read = 524288;
	//$read = 1048576;
	//$read = 16777216; //Read 16meg chunks
	$x = 0;
	$part = 0;
	$strcount=0;
	while(!feof($fh)) {
		$rbuf = fread($fh, $read);
		for($i=$read;$i > 0 || $n == chr(10);$i--) {
			$n=substr($rbuf, $i, 1);
			if($n == chr(10))break;
				//If we are at the end of the file, just grab the rest and stop loop
			elseif(feof($fh)) {
				$i = $read;
				$buf = substr($rbuf, 0, $i+1);
				break;
		   }
		}
		$count = $db->exec("BEGIN;"); //returns affected rows

		//This is the buffer we want to do stuff with, maybe thow to a function?
		$buf = substr($rbuf, 0, $i+1);
		$buf = str_replace("{domain_uuid}", $domain_uuid, $buf);
		$totalsize = strlen($buf)+$totalsize;

		$lnarray = explode ("\n", $buf);
		//print_r($lnarray);

		$columnvaluecount=0;
		foreach($lnarray as $sql) {

			//--- Begin SQLite -------------------------------------

					if (strlen($sql) > 0) {
						//echo $sql."<br /><br />\n";
						$count = $db->exec(check_sql($sql)); //returns affected rows

						$x++;
						if ($x > 10000) {
							$count = $db->exec("COMMIT;"); //returns affected rows
							$count = $db->exec("BEGIN;"); //returns affected rows
						}

					}
					unset($sql);

			//---EndSQLite-------------------------------------

			//if ($columnvaluecount > 10) { break; }
			$columnvaluecount++;
		}

		//Point marker back to last \n point
		$part = ftell($fh)-($read-($i+1));
		fseek($fh, $part);
		if ($strcount >= 5000) { break; } //handle up to a gig file
		$strcount++;

	}

	$count = $db->exec("COMMIT;"); //returns affected rows
	fclose($fh);

	//truncate the file now that it has been processed
		$fh = fopen($v_file, 'w');
		fclose($fh);


//---- begin import cdr records -----------------------------------------------------------------------------------

?>
