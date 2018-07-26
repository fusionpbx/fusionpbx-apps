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

//includes
	require_once "root.php";
	require_once "resources/require.php";

//check permissions
	require_once "resources/check_auth.php";

//show the header
	require_once "resources/header.php";

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

?>

<style type="text/css">
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
   <div style="position: absolute;"><video id="remote_video" width="640" height="480" muted="muted"></video></div>
   <div style="position: absolute;"><video id="local_video" width="160" height="120" muted="muted"></video></div>
	<!--<input id='send' name="send" type="button" class='btn btn-success' onclick="send();" value="Send" />-->
</div>
<div class="buttons">
	<input id='answer' name="answer" type="button" class='btn btn-success' style="display: none;" onclick="answer();" value="Answer" />
	<input id='decline' name="decline" type="button" class='btn btn-danger' style="display: none;" onclick="" value="Decline" />

	<input id='mute_audio' name="mute_audio" type="button" class='btn btn-danger' style="display: none;" onclick="mute_audio();" value="Mute Audio" />
	<input id='mute_video' name="mute_video" type="button" class='btn btn-danger' style="display: none;" onclick="mute_video();" value="Mute Video" />

	<input id='unmute_audio' name="unmute_audio" type="button" class='btn btn-danger' style="display: none;" onclick="unmute_audio();" value="Unmute Audio" />
	<input id='unmute_video' name="unmute_video" type="button" class='btn btn-danger' style="display: none;" onclick="unmute_video();" value="Unmute Video" />

	<input id='end' name="end" type="button" class='btn btn-danger' style="display: none;" onclick="hangup();" value="End" />
	<br />
</div>

<script src="resources/sip-0.7.8.min.js"></script>
<script language="JavaScript" type="text/javascript">

<?php
echo "	var session;\n";
echo "	var config = {\n";
echo "		uri: '".$user_extension."@".$domain_name."',\n";
echo "		ws_servers: 'wss://".$domain_name.":7443',\n";
echo "		authorizationUser: '".$user_extension."',\n";
echo "		password: atob('".base64_encode($user_password)."')\n";
echo "	};\n";
?>

	var user_agent = new SIP.UA(config);

	//here you determine whether the call has video and audio
	var options = {
		media: {
			constraints: {
				audio: true,
				video: true
			},
			render: {
				remote: document.getElementById('remote_video'),
				local: document.getElementById('local_video')
			}
		}
	};
	//makes the call
	//session = user_agent.invite('sip:1020@pbx.fusionpbx.com', options);

	//answer
	user_agent.on('invite', function (session) {
		document.getElementById('dialplad').style.display = "none";
		document.getElementById('answer').style.display = "inline";
		document.getElementById('decline').style.display = "inline";
		document.getElementById('end').style.display = "none";
		document.getElementById('mute_audio').style.display = "inline";
		document.getElementById('mute_video').style.display = "inline";
		var answer = document.getElementById('answer');
		answer.addEventListener("click", function () {
			session.accept({
				media: {
					render: {
						remote: document.getElementById('remote_video'),
						local: document.getElementById('local_video')
					}
				}
			});

		}, false);

		var decline = document.getElementById('decline');
		decline.addEventListener("click", function () {
			session.cancel();
			document.getElementById('dialplad').style.display = "grid";
			document.getElementById('answer').style.display = "none";
			document.getElementById('decline').style.display = "none";
			document.getElementById('end').style.display = "none";
		}, false);

	});

	user_agent.on('cancel', function (session) {
		document.getElementById('dialplad').style.display = "grid";
		document.getElementById('answer').style.display = "none";
		document.getElementById('decline').style.display = "none";
		document.getElementById('end').style.display = "none";
		document.getElementById('dialpad').style.display = "inline";
	});

	user_agent.on('bye', function (session) {
		hangup();
	});

	function answer() {
		document.getElementById('dialplad').style.display = "none";
		document.getElementById('answer').style.display = "none";
		document.getElementById('decline').style.display = "none";
		document.getElementById('end').style.display = "inline";
	}

	function hangup() {
		//session.bye();
		session.terminate();
		//session.cancel();
		end();
	}

	function send() {
		destination = document.getElementById('destination').value;
		document.getElementById('dialplad').style.display = "none";
		document.getElementById('answer').style.display = "none";
		document.getElementById('decline').style.display = "none";
		document.getElementById('end').style.display = "inline";
		document.getElementById('local_video').style.display = "inline";
		document.getElementById('remote_video').style.display = "inline";
		document.getElementById('mute_audio').style.display = "inline";
		document.getElementById('mute_video').style.display = "inline";
		session = user_agent.invite('sip:'+destination+'<?php echo $domain_name; ?>', options);
		
		var remote_video = document.getElementById("remote_video");
		remote_video.setAttribute("controls","controls");

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

	function end() {
		document.getElementById('dialplad').style.display = "grid";
		document.getElementById('answer').style.display = "none";
		document.getElementById('decline').style.display = "none";
		document.getElementById('end').style.display = "none";

		document.getElementById('local_video').style.display = "none";
		document.getElementById('remote_video').style.display = "none";
		
		document.getElementById('mute_audio').style.display = "none";
		document.getElementById('mute_video').style.display = "none";
		document.getElementById('unmute_audio').style.display = "none";
		document.getElementById('unmute_video').style.display = "none";
		
		document.getElementById('dialpad').style.display = "grid";
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

</script>

<div id='dialplad' class='dialpad' style='position:absolute;z-index:999;'>
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

</style>

<?php

//show the footer
	require_once "resources/footer.php";

?>
