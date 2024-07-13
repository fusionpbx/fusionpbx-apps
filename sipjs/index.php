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
	Portions created by the Initial Developer are Copyright (C) 2018
	the Initial Developer. All Rights Reserved.
*/

//includes files
	require_once dirname(__DIR__, 2) . "/resources/require.php";
	require_once "resources/check_auth.php";

//check permissions
	if (permission_exists('extension_add') || permission_exists('extension_edit')) {
		//access granted
	}
	else {
		echo "access denied";
		exit;
	}

//add multi-lingual support
	$language = new text;
	$text = $language->get();

//get the user ID
	$sql = "SELECT d.domain_name,e.extension,e.password FROM ";
	$sql .= "v_extension_users as t, v_extensions as e, v_users as u, v_domains as d ";
	$sql .= "WHERE u.user_uuid = t.user_uuid ";
	$sql .= "AND e.extension_uuid = t.extension_uuid ";
	$sql .= "AND e.domain_uuid = d.domain_uuid ";
	$sql .= "AND u.user_uuid = '" . $_SESSION['user_uuid'] . "' ";
	$sql .= "AND e.domain_uuid = '" . $_SESSION["domain_uuid"] . "' LIMIT 1";
	$prep_statement = $db->prepare($sql);
	if ($prep_statement) {
		$prep_statement->execute();
		$row = $prep_statement->fetch(PDO::FETCH_ASSOC);
		$domain_name = $row['domain_name'];
		$user_extension = $row['extension'];
		$user_password = $row['password'];
	}

//show the header
	require_once "resources/header.php";

?>


<style type="text/css">
body {
	background-color: #333;
}

.inner {
	position: absolute;
}

.buttons{
	position: fixed;
	bottom: 0;
	right: 0;
	/*width: 200px;*/
	border: 10px solid rgba(255, 255, 255, 0);
}
</style>

<div class="">
	<div style="position: absolute;"><video id="remote_video" width="640" height="480" style="display: none;"></video></div>
	<div style="position: absolute;display: none;"><video id="local_video" width="160" height="120" style="display: none;"></video></div>
	<!--<input id='send' name="send" type="button" class='btn btn-success' onclick="send();" value="Send" />-->
</div>

<audio id="ringtone" preload="auto" style="display: none;">
	<source src="resources/ringtones/ringtone.mp3" type="audio/mpeg" loop="loop" />
</audio>

<script src="sip-0.7.8.min.js"></script>
<!--<script src="./resources/sip-0.21.2.js"></script>-->
<!--<script type="module" src="./resources/app.php"></script>-->


<script language="JavaScript" type="text/javascript">

function sanitize_string(str) {
	let temp = document.createElement('div');
	temp.textContent = str;
	return temp.innerHTML;
}

<?php
echo "	let user_agent;\n";
echo "	let session;\n";
echo "	let answer_time;\n";
echo "	var config = {\n";
echo "		uri: '".$user_extension."@".$domain_name."',\n";
echo "		ws_servers: 'wss://".$domain_name.":7443',\n";
echo "		authorizationUser: '".$user_extension."',\n";
echo "		password: atob('".base64_encode($user_password)."'),\n";
echo "		registerExpires: 120,\n";
echo "		displayName: \"".$user_extension."\"\n";
echo "	};\n";
?>

	user_agent = new SIP.UA(config);

	//here you determine whether the call has video and audio
	var options = {
		media: {
			constraints: {
				audio: true,
				video: false
			},
			render: {
				remote: document.getElementById('remote_video'),
				local: document.getElementById('local_video')
			},
			RTCConstraints: {
				"optional": [{ 'DtlsSrtpKeyAgreement': 'true'} ]
			}
		}
	};

	//answer
	user_agent.on('invite', function (s) {

		if (typeof session !== "undefined" && session.display_name != s.remoteIdentity.displayName) {
			return;
		}

		//save the session to the global session
		session = s;
		session.display_name = session.remoteIdentity.displayName;
		session.uri_user = session.remoteIdentity.uri.user;

		//send the object to the browser console
		//console.log(session);

		//play the ringtone
		document.getElementById('ringtone').play();

		//add the caller ID
		document.getElementById('ringing_caller_id').innerHTML = sanitize_string(session.display_name) + "<br /><a href='https://<?php echo $_SESSION['domain_name']; ?>/app/contacts/contacts.php?search=" + sanitize_string(session.uri_user) + "' target='_blank'>" + sanitize_string(session.uri_user) + "</a>";
		document.getElementById('active_caller_id').innerHTML = sanitize_string(session.display_name) + "<br /><a href='https://<?php echo $_SESSION['domain_name']; ?>/app/contacts/contacts.php?search=" + sanitize_string(session.uri_user) + "' target='_blank'>" + sanitize_string(session.uri_user) + "</a>";

		//show or hide the panels
		document.getElementById('dialpad').style.display = "none";
		document.getElementById('ringing').style.display = "inline";

		//show or hide the buttons
		document.getElementById('answer').style.display = "inline";
		document.getElementById('decline').style.display = "inline";
		document.getElementById('hangup').style.display = "none";
		document.getElementById('mute_audio').style.display = "inline";
		document.getElementById('mute_video').style.display = "none";

		session.on('cancel', function (s) {
			//play the ringtone
			document.getElementById('ringtone').pause();

			//show or hide the panels
			document.getElementById('dialpad').style.display = "grid";
			document.getElementById('ringing').style.display = "none";
			document.getElementById('active').style.display = "none";

			//show or hide the buttons
			document.getElementById('answer').style.display = "none";
			document.getElementById('decline').style.display = "none";
			document.getElementById('hangup').style.display = "none";

			//clear the caller id
			document.getElementById('ringing_caller_id').innerHTML = '';
			document.getElementById('active_caller_id').innerHTML = '';

			//clear the answer time
			answer_time = null;
		});

		session.on('bye', function (s) {
			//play the ringtone
			document.getElementById('ringtone').pause();

			//show or hide the panels
			document.getElementById('dialpad').style.display = "grid";
			document.getElementById('ringing').style.display = "none";
			document.getElementById('active').style.display = "none";

			//show or hide the buttons
			document.getElementById('answer').style.display = "none";
			document.getElementById('decline').style.display = "none";
			document.getElementById('hangup').style.display = "none";

			//clear the answer time
			answer_time = null;

			//end the call
			hangup();
		});

		session.on('failed', function (s) {
			//play the ringtone
			document.getElementById('ringtone').pause();

			//show or hide the panels
			document.getElementById('dialpad').style.display = "grid";
			document.getElementById('ringing').style.display = "none";
			document.getElementById('active').style.display = "none";

			//show or hide the buttons
			document.getElementById('answer').style.display = "none";
			document.getElementById('decline').style.display = "none";
			document.getElementById('hangup').style.display = "none";

			//clear the answer time
			answer_time = null;

			//end the call
			hangup();
		});

		session.on('rejected', function (s) {
			//play the ringtone
			document.getElementById('ringtone').pause();

			//show or hide the panels
			document.getElementById('dialpad').style.display = "grid";
			document.getElementById('ringing').style.display = "none";
			document.getElementById('active').style.display = "none";

			//show or hide the buttons
			document.getElementById('answer').style.display = "none";
			document.getElementById('decline').style.display = "none";
			document.getElementById('hangup').style.display = "none";

			//clear the answer time
			answer_time = null;

			//end the call
			hangup();
		});

	});

	function answer() {

		//continue if the session exists
		if (!session) {
			return false;
		}

		//start the answer time
		answer_time = Date.now();

		//pause the ringtone
		document.getElementById('ringtone').pause();

		//answer the call
		session.accept({
			media: {
				constraints: {
					audio: true,
					video: false
				},
				render: {
					remote: document.getElementById('remote_video'),
					local: document.getElementById('local_video')
				},
				RTCConstraints: {
					"optional": [{ 'DtlsSrtpKeyAgreement': 'true'} ]
				}
			}
		});

		//show the or hide the panels
		document.getElementById('dialpad').style.display = "none";
		document.getElementById('ringing').style.display = "none";
		document.getElementById('active').style.display = "grid";

		//show or hide the buttons
		document.getElementById('answer').style.display = "none";
		document.getElementById('decline').style.display = "none";
		document.getElementById('unhold').style.display = "none";
		document.getElementById('hangup').style.display = "inline";
	}

	// Function to pad numbers with leading zeros
	function pad(number, length) {
		return (number < 10 ? '0' : '') + number;
	}

	//function to get the current time in seconds
	function get_session_time() {
		if (answer_time) {
			// get the elapsed time using the answer time
			elapsed_time = Date.now() - answer_time;

			// Calculate hours, minutes, and seconds
			var hours = Math.floor(elapsed_time / (1000 * 60 * 60));
			var minutes = Math.floor((elapsed_time % (1000 * 60 * 60)) / (1000 * 60));
			var seconds = Math.floor((elapsed_time % (1000 * 60)) / 1000);

			// Format the time with leading zeros if necessary
			var formatted_time = pad(hours, 2) + ":" + pad(minutes, 2) + ":" + pad(seconds, 2);

			// Update the element with id="elapsed-time" to display the formatted elapsed time
			document.getElementById("answer_time").textContent = "Time Elapsed: " + formatted_time;
		} else {
			console.log('Call has not been answered yet');
			return null;
		}
	}

	//update elapsed time every second
	setInterval(get_session_time, 1000);

	//function used to end the session
	function hangup() {

		//session.bye();
		session.terminate();

		//show or hide the panels
		document.getElementById('dialpad').style.display = "grid";
		document.getElementById('ringing').style.display = "none";
		document.getElementById('active').style.display = "none";

		//show or hide the buttons
		document.getElementById('answer').style.display = "none";
		document.getElementById('decline').style.display = "none";
		document.getElementById('hangup').style.display = "none";

		document.getElementById('local_video').style.display = "none";
		document.getElementById('remote_video').style.display = "none";

		document.getElementById('mute_audio').style.display = "none";
		document.getElementById('mute_video').style.display = "none";
		document.getElementById('unmute_audio').style.display = "none";
		document.getElementById('unmute_video').style.display = "none";

		//clear the caller id
		document.getElementById('ringing_caller_id').innerHTML = '';
		document.getElementById('active_caller_id').innerHTML = '';
	}

	function hold() {
		document.getElementById('hold').style.display = "none";
		document.getElementById('unhold').style.display = "grid";
		session.hold();
	}

	function unhold() {
		document.getElementById('hold').style.display = "grid";
		document.getElementById('unhold').style.display = "none";
		session.unhold();
	}

	function send() {

		//get the destination number
		destination = document.getElementById('destination').value;

		//return immediately if there is no destination
		if (destination.length == 0) {
			return;
		}

		//show or hide the panels
		document.getElementById('dialpad').style.display = "none";
		document.getElementById('ringing').style.display = "none";
		document.getElementById('active').style.display = "grid";

		document.getElementById('answer').style.display = "none";
		document.getElementById('decline').style.display = "none";
		document.getElementById('hangup').style.display = "inline";
		//document.getElementById('local_video').style.display = "inline";
		//document.getElementById('remote_video').style.display = "inline";
		document.getElementById('mute_audio').style.display = "inline";
		//document.getElementById('mute_video').style.display = "inline";

		//make a call using a sip invite
		session = user_agent.invite('sip:'+destination+'@<?php echo $domain_name; ?>', options);

		var remote_video = document.getElementById("remote_video");
		remote_video.setAttribute("controls","controls");

		//start the answer time
		answer_time = Date.now();

		//set the caller ID to the destination
		document.getElementById('ringing_caller_id').innerHTML = destination;
		document.getElementById('active_caller_id').innerHTML = destination;

	}

	function mute_audio(destination) {
		session.mute({audio: true});
		document.getElementById('mute_audio').style.display = "none";
		document.getElementById('unmute_audio').style.display = "inline";
	}

	function mute_video(destination) {
		session.mute({video: true});
		document.getElementById('local_video').style.display = "none";
		document.getElementById('mute_video').style.display = "none";
		document.getElementById('unmute_video').style.display = "inline";
	}

	function unmute_audio(destination) {
		session.unmute({audio: true});
		document.getElementById('mute_audio').style.display = "inline";
		document.getElementById('unmute_audio').style.display = "none";
	}

	function unmute_video(destination) {
		session.unmute({video: true});
		document.getElementById('local_video').style.display = "inline";
		document.getElementById('mute_video').style.display = "inline";
		document.getElementById('unmute_video').style.display = "none";
	}

	function digit_add($digit) {
		document.getElementById('destination').value = document.getElementById('destination').value + $digit;
	}

	function digit_delete($digit) {
		destination = document.getElementById('destination').value;
		document.getElementById('destination').value = destination.substring(0, destination.length -1);
	}

	function digit_clear($digit) {
		document.getElementById('destination').value = '';
	}

    //function to check for Enter key press
	function send_enter_key(event) {
		if (event.key === "Enter") {
			send();
		}
	}

	//add event listener for keydown event on input field
	document.addEventListener("DOMContentLoaded", function() {
		document.getElementById("destination").addEventListener("keydown", send_enter_key);
	});

</script>

<div id='dialpad' class='dialpad' style='position:absolute;z-index:999;'>
	<div style="align: left">
		<input type="text" id="destination" name="destination" class="destination" value="" />
	</div>
	<div class="dialpad_wrapper">
		<div class="dialpad_box" onclick="digit_add('1');"><strong>1</strong><sup>&nbsp;&nbsp;&nbsp;</sup></div>
		<div class="dialpad_box"onclick="digit_add('2');"><strong>2</strong><sup>ABC</sup></div>
		<div class="dialpad_box" onclick="digit_add('3');"><strong>3</strong><sup>DEF</sup></div>

		<div class="dialpad_box" onclick="digit_add('4');"><strong>4</strong><sup>GHI</sup></div>
		<div class="dialpad_box" onclick="digit_add('5');"><strong>5</strong><sup>JKL</sup></div>
		<div class="dialpad_box" onclick="digit_add('6');"><strong>6</strong><sup>MNO</sup></div>

		<div class="dialpad_box" onclick="digit_add('7');"><strong>7</strong><sup>PQRS</sup></div>
		<div class="dialpad_box" onclick="digit_add('8');"><strong>8</strong><sup>TUV</sup></div>
		<div class="dialpad_box" onclick="digit_add('9');"><strong>9</strong><sup>WXYZ</sup></div>

		<div class="dialpad_box" onclick="digit_add('*');"><strong>*</strong><sup></sup></div>
		<div class="dialpad_box" onclick="digit_add('0');"><strong>0</strong><sup></sup></div>
		<div class="dialpad_box" onclick="digit_add('#');"><strong>#</strong><sup></sup></div>

		<div class="dialpad_box" onclick="digit_clear();"><strong></strong><sup>CLEAR</sup></div>
		<div class="dialpad_box" onclick="digit_delete();"><strong></strong><sup>DELETE</sup></div>
		<div class="dialpad_box" onclick="send();"><strong></strong><sup>SEND</sup></div>
	</div>
</div>

<div id='ringing' class='dialpad' style='position:absolute;z-index:100;display: none;'>
	<div id="ringing_caller_id" class="caller_id"></div>
	<div class="dialpad_wrapper">
		<div id='answer' class="button_box" onclick="answer();"><sup>Answer</sup></div>
		<div id='decline' class="button_box" onclick="hangup();"><sup>Decline</sup></div>
	</div>
</div>

<div id='active' class='dialpad' style='position:absolute;z-index:100;display: none;'>
	<div id="active_caller_id" class="caller_id"></div>
	<div id="answer_time" class="button_box"></div>
	<div class="dialpad_wrapper">
		<div id='mute_audio' class="button_box" onclick="mute_audio();">Mute Audio</div>
		<div id='unmute_audio' style='display: none;' class="button_box" onclick="unmute_audio();">Unmute Audio</div>

		<div id='hold' class="button_box" onclick="hold();">Hold</div>
		<div id='unhold' class="button_box" style='display: none;' onclick="unhold();">Unhold</div>

		<div id='hangup' class="button_box" onclick="hangup();">Hangup</div>

		<div id='mute_video' class="button_box" style='display: none;' onclick="mute_video()">&nbsp;</div>
		<div id='umute_video' class="button_box" style='display: none;' onclick="unmute_video()">&nbsp;</div>

	</div>
</div>

<style type="text/css" style="display: none;">

	.destination {
		/*background-color: #333333;*/
		background-color: rgba(255, 255, 255, 0.5);
		color: #333333;
		border-radius: 5px;
		padding: 10px;
		margin: 3px;
		width: 305px;
	}

	.dialpad_wrapper {
		display: grid;
		grid-template-columns: 100px 100px 100px;
		grid-gap: 5px;
		color: #444444;
	}

	.dialpad_box {
		/*background-color: #333333;*/
		background-color: rgba(0, 0, 0, 0.7);
		color: #FFFFFF;
		border-radius: 5px;
		padding: 10px;
	}

	strong {
		padding: 5px 5px;
		color: #FFFFFF;
		font-size: 30px;
	}

	sup {
		padding: 3px 3px;
		color: #FFFFFF;
		font-size: 10px;
	}

	.button_box {
		/*background-color: #333333;*/
		background-color: rgba(0, 0, 0, 0.7);
		color: #FFFFFF;
		border-radius: 5px;
		padding: 10px;
		font-size: 12px;
	}

	.caller_id {
		/*background-color: #333333;*/
		text-align: center;
		background-color: rgba(0, 0, 0, 0.7);
		color: #FFFFFF;
		border-radius: 5px;
		padding: 100px;
		font-size: 12px;
		margin-bottom: 50px;
	}

</style>

<?php

//show the footer
	require_once "resources/footer.php";
?>
