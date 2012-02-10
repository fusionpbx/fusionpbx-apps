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
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (if_group("admin") || if_group("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}
require_once "includes/header.php";


//notes
	// http://wiki.freeswitch.org/wiki/Mod_portaudio
	// http://wiki.freeswitch.org/wiki/Freeswitch_softphone
	// 


//echo "<form method='post' name='frm' action=''>\n";
//echo "</form>";

echo "<div align='center'>\n";

echo "<div id='ajax_response'>\n";

echo "</div>\n";

echo "<table border='0'>\n";
echo "<tr>\n";
echo "<td>\n";

	echo "<table border='0' width='225'>\n";

	echo "<tr>\n";
	echo "<td align='center' colspan='3'>\n";
	echo "<input style='width: 100%' class='formfld' type='text' id='dial' name='dial' maxlength='10' value=\"\">\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td align='center'>\n";
	echo "	<input type='button' style='width: 100%; height: 20px;' class='btn' name='' alt='view' onclick=\"document.getElementById('dial').value += '1';\" value='1'>\n";
	echo "</td>\n";
	echo "<td align='center'>\n";
	echo "	<input type='button' style='width: 100%; height: 20px;' class='btn' name='' alt='view' onclick=\"document.getElementById('dial').value += '2';\" value='2'>\n";
	echo "</td>\n";
	echo "<td align='center'>\n";
	echo "	<input type='button' style='width: 100%; height: 20px;' class='btn' name='' alt='view' onclick=\"document.getElementById('dial').value += '3';\" value='3'>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td align='center'>\n";
	echo "	<input type='button' style='width: 100%; height: 20px;' class='btn' name='' alt='view' onclick=\"document.getElementById('dial').value += '4';\" value='4'>\n";
	echo "</td>\n";
	echo "<td align='center'>\n";
	echo "	<input type='button' style='width: 100%; height: 20px;' class='btn' name='' alt='view' onclick=\"document.getElementById('dial').value += '5';\" value='5'>\n";
	echo "</td>\n";
	echo "<td align='center'>\n";
	echo "	<input type='button' style='width: 100%; height: 20px;' class='btn' name='' alt='view' onclick=\"document.getElementById('dial').value += '6';\" value='6'>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td align='center'>\n";
	echo "	<input type='button' style='width: 100%; height: 20px;' class='btn' name='' alt='view' onclick=\"document.getElementById('dial').value += '7';\" value='7'>\n";
	echo "</td>\n";
	echo "<td align='center'>\n";
	echo "	<input type='button' style='width: 100%; height: 20px;' class='btn' name='' alt='view' onclick=\"document.getElementById('dial').value += '8';\" value='8'>\n";
	echo "</td>\n";
	echo "<td align='center'>\n";
	echo "	<input type='button' style='width: 100%; height: 20px;' class='btn' name='' alt='view' onclick=\"document.getElementById('dial').value += '9';\" value='9'>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td align='center'>\n";
	echo "	<input type='button' style='width: 100%; height: 20px;' class='btn' name='' alt='view' onclick=\"document.getElementById('dial').value += '*';\" value='*'>\n";
	echo "</td>\n";
	echo "<td align='center'>\n";
	echo "	<input type='button' style='width: 100%; height: 20px;' class='btn' name='' alt='view' onclick=\"document.getElementById('dial').value += '0';\" value='0'>\n";
	echo "</td>\n";
	echo "<td align='center'>\n";
	echo "	<input type='button' style='width: 100%; height: 20px;' class='btn' name='' alt='view' onclick=\"document.getElementById('dial').value += '#';\" value='#'>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td align='center' colspan='3'>\n";
	echo "<input style='width: 100%' class='formfld' type='text' id='api_cmd' name='api_cmd' value=\"\">\n";
	//echo "<br \>\n";
	echo "<input style='width: 100%' class='formfld' type='hidden' id='uid' name='uid' value=\"\">\n";
	echo "<input style='width: 100%' class='formfld' type='hidden' id='rec' name='rec' value=\"\">\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>\n";

echo "</td>\n";
echo "<td width='50'>\n";
echo "	<input type='button' style='width: 100%; height: 20px;' class='btn' name='' alt='view' onclick=\"backspace();\" value='<'>\n";
echo "	<input type='button' style='width: 100%; height: 20px;' class='btn' name='' alt='view' onclick=\"clear();\" value='clear'>\n";
echo "	<input type='button' style='width: 100%; height: 20px;' class='btn' name='' alt='view' onclick=\"pa_call();\" value='call'>\n";
echo "	<input type='button' style='width: 100%; height: 20px;' disabled class='btn' name='xfer' id = 'xfer' alt='view' onclick=\"xfer();\" value='xfer'>\n";
//echo "	<input type='button' style='width: 100%; height: 100%;' class='btn' name='' alt='view' onclick=\"rec();\" value='rec'>\n";
echo "	<input type='button' style='width: 100%; height: 20px;' class='btn' name='' alt='view' onclick=\"pa_switch();\" value='switch'>\n";
echo "	<input type='button' style='width: 100%; height: 20px;' class='btn' name='' alt='view' onclick=\"pa_switch_none();\" value='hold'>\n";
echo "	<input type='button' style='width: 100%; height: 20px;' class='btn' name='' alt='view' onclick=\"cmd();\" value='cmd'>\n";
echo "	<input type='button' style='width: 100%; height: 20px;' class='btn' name='' alt='view' onclick=\"pa_hangup();\" value='end'>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";

echo "</div>\n";

?>

<script type="text/javascript">
function ajaxFunction()
{
	var xmlhttp;
	if (window.XMLHttpRequest)
	{
		// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else if (window.ActiveXObject)
	{
		// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	else
	{
		alert("Your browser does not support XMLHTTP!");
	}
	xmlhttp.onreadystatechange=function()
	{
	if(xmlhttp.readyState==4)
		{
			var dial = document.getElementById('dial').value;
			//alert('dial:'+dial);
			var response = xmlhttp.responseText;
			document.getElementById('ajax_response').innerHTML = '<pre>\n'+response+'</pre>\n';
			//alert('response: '+response);
			//response = response.replace('SUCCESS:', '');
			var response_array = response.split(':');
			if (response_array.length > 1) {
				document.getElementById('uid').value = response_array[2];
				document.getElementById('xfer').disabled = false;
			}
			//document.myForm.time.value=xmlhttp.responseText;
		}
	}

	var api_cmd = document.getElementById('api_cmd').value;
	//alert('cmd:'. api_cmd);
	xmlhttp.open("GET","<?php echo PROJECT_PATH; ?>/includes/v_cmd.php?cmd="+escape(api_cmd)+"&rdr=false",true);
	xmlhttp.send(null);
}

function backspace() {
	document.getElementById('api_cmd').value = '';
	var dial = document.getElementById('dial').value;
	document.getElementById('dial').value = dial.substr(0, (dial.length -1));
}

function clear() {
	document.getElementById('uid').value = '';
	document.getElementById('api_cmd').value = '';
	document.getElementById('dial').value = '';
}

function pa_call() {
	document.getElementById('api_cmd').value = 'api pa call '+document.getElementById('dial').value;
	var uid = document.getElementById('uid').value;
	var dial = document.getElementById('dial').value;
	if (dial.length == 0) {
		document.getElementById('api_cmd').value = 'api pa answer';
		document.getElementById('xfer').disabled = false;
	}
	else {
		if (uid.length > 0) {
			document.getElementById('api_cmd').value = 'api pa dtmf '+document.getElementById('dial').value;
		}
	}
	ajaxFunction();
	document.getElementById('dial').value = '';
}

function xfer() {
	//uuid_transfer,<uuid> [-bleg|-both] <dest-exten> [<dialplan>] [<context>],Transfer a session,mod_commands
	var uid = document.getElementById('uid').value;
	var dial = document.getElementById('dial').value;
	if (uid.length > 0) {
		document.getElementById('api_cmd').value = 'api uuid_transfer '+uid+' '+dial+' XML default';
	}
	ajaxFunction();
	//document.getElementById('dial').value = '';
}

function rec() {
	//this function is making the recording but freeswitch crashes after hanging up the call. So it is currently disabled.
	//uuid_record,<uuid> [start|stop] <path> [<limit>],session record,mod_commands
	var uid = document.getElementById('uid').value;
	var rec = document.getElementById('rec').value;
	if (uid.length > 0) {
		if (rec.length > 0) {
			//stop
			document.getElementById('api_cmd').value = 'api uuid_record '+uid+' stop C:/PortableWebAp4.0.pro/Program/FreeSWITCH/recordings/portaudio.wav';
			document.getElementById('rec').value = '';
		}
		else {
			//start
			document.getElementById('api_cmd').value = 'api uuid_record '+uid+' start C:/PortableWebAp4.0.pro/Program/FreeSWITCH/recordings/portaudio.wav';
			document.getElementById('rec').value = 'true';
		}
	}
	ajaxFunction();
	//document.getElementById('dial').value = '';
}

function pa_switch() {
	document.getElementById('api_cmd').value = 'api pa switch';
	ajaxFunction();
}

function pa_switch_none() {
	document.getElementById('api_cmd').value = 'api pa switch none';
	ajaxFunction();
}

function cmd() {
	ajaxFunction();
}

function pa_hangup () {
	//uid = document.getElementById('uid').value;
	//document.getElementById('api_cmd').value = 'api uuid_record '+uid+' stop';
	document.getElementById('uid').value = '';
	document.getElementById('api_cmd').value = 'api pa hangup';
	document.getElementById('dial').value = '';
	ajaxFunction();

}


</script>
<?php

require_once "includes/footer.php";
?>