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
	Portions created by the Initial Developer are Copyright (C) 2008-2016
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
	Igor Olhovskiy <igorolhovskiy@gmail.com>
*/
require_once "root.php";
require_once "resources/require.php";
require_once "resources/check_auth.php";

if (permission_exists('import_extensions')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//add multi-lingual support
$text = (new text)->get();

require_once "resources/header.php";

$document['title'] = $text['title-import_extensions'];

require_once "resources/paging.php";

//$csv_file_path = '/var/www/fusionpbx/app/bulk_import_extensions/';
$csv_file_path = '/tmp/';
$csv_file_name = $csv_file_path . "import.csv";

// Get variables here

$rows_to_show =isset($_SESSION['import_extensions']['rows_to_show']['numeric']) ? (int) $_SESSION['import_extensions']['rows_to_show']['numeric'] : 4;
$is_import = isset($_REQUEST['is_import']) ? filter_var($_REQUEST['is_import'], FILTER_VALIDATE_BOOLEAN) : False;
$skip_first_line = isset($_REQUEST['skip_first_line']) ? filter_var($_REQUEST['skip_first_line'], FILTER_VALIDATE_BOOLEAN) : False;
$csv_fields_order = isset($_REQUEST['csv_field']) ? $_REQUEST['csv_field'] : False;

// Show the content

// Get table row width. 90 - cause 10% is always to show selector.
$table_row_width = (int) 90 / $rows_to_show;

$c = 0;
$row_style["0"] = "row_style0";
$row_style["1"] = "row_style1";


// File operations

if (isset($_FILES['file'])) {
	if ($_FILES["file"]["error"] == UPLOAD_ERR_OK && $_FILES["file"]["type"] == 'text/csv') {
		move_uploaded_file($_FILES["file"]["tmp_name"], $csv_file_name);
	}
}


// Check if we have CSV file on place
$import_file = new csv_file_process($csv_file_name);

$action = '';
if ($import_file->is_valid() && $is_import && $csv_fields_order) {
	$action = 'import';
} elseif ($import_file->is_valid()) {
	$action = 'show';
}

// Check if fields are all set before show content

if ($action == 'import') {

	if (!in_array('extension', $csv_fields_order)) {
		$_SESSION['message_mood'] = 'negative';
		$_SESSION['message'] = $text['message-extensions_not_found'];
		header('Location: import_extensions.php');
		return;
	}
}

//show the content

// Javascript function for file upload
echo "<script language='JavaScript' type='text/javascript'>\n";

echo "  function check_filetype(file_input) {\n";
echo "          file_ext = file_input.value.substr((~-file_input.value.lastIndexOf('.') >>> 0) + 2);\n";
echo "          if (file_ext != 'csv' && file_ext != '') {\n";
echo "                  display_message(\"".$text['message-unsupported_file_type']."\", 'negative', '2750');\n";
echo "          }\n";
echo "          var selected_file_path = file_input.value;\n";
echo "          selected_file_path = selected_file_path.replace(\"C:\\\\fakepath\\\\\",'');\n";
echo "          document.getElementById('file_label').innerHTML = selected_file_path;\n";
echo "          document.getElementById('button_reset').style.display='inline';\n";
echo "  }\n";

echo "</script>";
echo "<script language='JavaScript' type='text/javascript' src='".PROJECT_PATH."/resources/javascript/reset_file_input.js'></script>\n";

echo "<table width='100%' cellpadding='0' cellspacing='0 border='0'>\n";
echo "	<tr>\n";
echo "		<td width='50%' align='left' nowrap='nowrap'><b>".$text['header-import_extensions']."</b></td>\n";
echo "		<td width='50%' align='right'>&nbsp;</td>\n";
echo "	</tr>\n";
echo "	<tr>\n";
echo "		<td align='left'>\n";
echo "			".$text['description-import_extensions']."<br /><br />\n";
echo "		</td>\n";
echo "		<td>";
echo "			<form name='frmimport' method='POST' enctype='multipart/form-data' style='float: right;' action=''>\n";
echo "				<input name='file' id='file' type='file' style='display: none;' onchange='check_filetype(this);'>";
echo "				<label id='file_label' for='file' class='txt' style='width: 200px; overflow: hidden; white-space: nowrap;'>".$text['label-select_a_file']."</label>\n";
echo " 				<input id='button_reset' type='reset' class='btn' style='display: none;' value='".$text['button-reset']."' onclick=\"reset_file_input('file'); document.getElementById('file_label').innerHTML = '".$text['label-select_a_file']."'; this.style.display='none'; return true;\">\n";
echo "				<input name='upload' type='submit' class='btn' id='upload' value=\"".$text['button-import']."\">\n";
echo "			</form>";
echo "		</td>";
echo "	</tr>\n";
echo "</table>\n";
echo "<br/>\n";


if ($action == 'import') {
	// Import data to database

	$csv_fields_order = array_map('check_str', $csv_fields_order);

	
	$import_file->set_csv_fields_order($csv_fields_order);

	$process_csv_file_options = array(
		'db' => $db,
		'vm_password_length' => $_SESSION['voicemail']['password_length']['numeric'],
		'domain_uuid' => $domain_uuid,
		'domain_name' => $_SESSION['domain_name'],
		'skip_first_line' => $skip_first_line,
		'line_sip_transport' => $_SESSION['provision']['line_sip_transport']['text'],
		'line_sip_port' => $_SESSION['provision']['line_sip_port']['numeric'],
		'line_register_expires' => $_SESSION['provision']['line_register_expires']['numeric'],
	);
	

	// Show debug messages of processing file
	echo "<pre>";
	echo $import_file->process_csv_file($process_csv_file_options);
	echo "</pre>";

	unset($import_file);
	unlink($csv_file_name); 

} elseif ($action == 'show') {

	// Here we got first 4 lines of file. As usual, CSV holds first line as a fields desccription.
	// And we will use it to count number of fields in file.
	$import_lines = $import_file->read_first();
	$row_count = count($import_lines[0]);

	// Initialize array if not full for normal show after.
	for ($i = 1; $i <= 3; $i++) {
		if (!isset($import_lines[$i])) {
			$import_lines[$i] = array();
		}
		for ($j = 0; $j < $row_count; $j++) {
			if (!isset($import_lines[$i][$j])) {
				$import_lines[$i][$j] = '';
			}
		}
	}

	$selector = new bulk_import_extensions_options_selector();

	// Show content in a case of valid file
	echo "<form method='post' name='frm' action=''>\n";
	echo "<table width='100%' cellpadding='0' cellspacing='0' border='0'>\n";
	echo "<tr class='" . $row_style[$c] . "'>\n";
	echo "<th width='10%' align='center' nowrap='nowrap'>" . $text['description-selector'] . "</th>\n";
	for ($i = 1; $i <= $rows_to_show; $i++) {
		echo "<th align='left' nowrap='nowrap' width='" . $table_row_width . "%'>" . $text['description-file_column'] . " " .$i . "</th>\n";
	}
	echo "</tr>\n";
	$c = 1 - $c;
	// Show table rows
	for ($row_index = 0; $row_index < $row_count; $row_index++) {
		// Show table columns. By default - show 3 first columns to check.
		echo "<tr class='" . $row_style[$c] . "'>\n";
		// Show selector
		echo "<td width='10%' align='center' nowrap='nowrap'>";
		echo $selector->draw_selector("csv_field[$row_index]", $row_index);
		echo "</td>\n";
		for ($i = 0; $i < $rows_to_show; $i++) {
			echo "<td align='left' nowrap='nowrap' width='" . $table_row_width . "%'>" . $import_lines[$i][$row_index] . "</td>\n";
		}
		echo "</tr>\n";
		$c = 1 - $c;
	}
	echo "</table>\n";
	echo "<input type='hidden' name='is_import' value='true'>\n";
	echo "<br/>\n";
	echo "<table width='100%' cellpadding='0' cellspacing='0' border='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='50%'>";
	echo "			<input type='checkbox' name='skip_first_line' id='skip_first_line' value='true' checked>&nbsp;" . $text['label-skip_first_line'] . "\n";
	echo "		</td>";
	echo "		<td width='50%'>";
	echo "			<input type='submit' name='submit' style='float: right;' class='btn' value='".$text['button-process']."'>\n";
	echo "		</td>";
	echo "	</tr>\n";
	echo "</table>\n";
	echo "<br/><br/>";
	echo "</form>";
	
 } // End show content for CSV file is present

//include the footer
require_once "resources/footer.php";
?>
