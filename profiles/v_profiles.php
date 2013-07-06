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
require_once "includes/require.php";
require_once "resources/check_auth.php";
if (permission_exists('sip_profiles_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

if ($_GET['a'] == "default" && permission_exists('sip_profiles_edit')) {

	//get the contents of the sip profile
	$sip_profile = file_get_contents($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/templates/conf/sip_profiles/'.$_GET['f']);

	//write the default config fget
	$fd = fopen($_SESSION['switch']['conf']['dir']."/sip_profiles/".$_GET['f'], "w");
	fwrite($fd, $sip_profile);
	fclose($fd);

	$save_msg = "Restored ".$_GET['f'];
}

if ($_POST['a'] == "save" && permission_exists('sip_profiles_edit')) {
	$v_content = $_POST['code'];
	$fd = fopen($_SESSION['switch']['conf']['dir']."/sip_profiles/".$_POST['f'], "w");
	fwrite($fd, $v_content);
	fclose($fd);
	$save_msg = "Saved ".$_POST['f'];
}

if ($_GET['a'] == "del" && permission_exists('sip_profiles_edit')) {
	if ($_GET['type'] == 'profile') {
		unlink($_SESSION['switch']['conf']['dir']."/sip_profiles/".$_GET['f']);
		header("Location: v_profiles.php");
		exit;
	}
}

require_once "includes/header.php";

$c = 0;
$row_style["0"] = "row_style0";
$row_style["1"] = "row_style1";

if (strlen($save_msg) > 0) {
	echo "<div align=\"center\">\n";
	echo "	<table width=\"40%\">\n";
	echo "		<tr>\n";
	echo "			<th align=\"left\">Message</th>\n";
	echo "		</tr>\n";
	echo "		<tr>\n";
	echo "			<td class=\"row_style1\">\n";
	echo "				<strong>$save_msg</strong>\n";
	echo "			</td>\n";
	echo "		</tr>\n";
	echo "	</table>\n";
	echo "</div>\n";
}

?>

<div align='center'>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
	 <td class="tabcont" align='left'>

<form action="v_profiles.php" method="post" name="iform" id="iform">
	<table width="100%" border="0" cellpadding="6" cellspacing="0">
		<tr>
		<td align='left'><p><span class="vexpl"><span class="red"><strong>Profiles<br>
			</strong></span>
			Use this to configure your SIP profiles.
			</p></td>
		</tr>
	</table>
	<br />
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<th width="25%" class="">Name</th>
		<th width="70%" class="">Description</th>
		<td width="5%" class="list"></td>
		</th>
	</tr>

<?php
	foreach (ListFiles($_SESSION['switch']['conf']['dir'].'/sip_profiles') as $key=>$file){
		if (substr($file, -4) == ".xml") {
			echo "<tr>\n";
			echo "	<td class='".$row_style[$c]."' ondblclick=\"document.location='v_profile_edit.php?f=".$file."'\";\" valign='middle'>\n";
			echo $file;
			echo "&nbsp;\n";
			echo "	</td>\n";
			echo "	<td class='row_stylebg' ondblclick=\"document.location='v_profile_edit.php?f=".$file."\">\n";
			switch ($file) {
			case "internal.xml":
				echo "The Internal profile by default requires registration which is most often used for extensions. ";
				echo "By default the Internal profile binds to the WAN IP which is accessible to the internal network. ";
				echo "A rule can be set from PFSense -> Firewall -> Rules -> WAN to the the WAN IP for port 5060 which ";
				echo "enables phones register from outside the network.";
				echo "";
				echo "&nbsp;";
				break; 
			case "internal-ipv6.xml":
				echo "The Internal IPV6 profile binds to the IP version 6 address and is similar to the Internal profile.\n";
				echo "&nbsp;";
				break;
			case "external.xml":
				echo "The External profile handles outbound registrations to a SIP provider or other SIP Server. The SIP provider sends calls to you, and you ";
				echo "send calls to your provider, through the external profile. The external profile allows anonymous calling, which is ";
				echo "required as your provider will never authenticate with you to send you a call. Calls can be sent using a SIP URL \"my.domain.com:5080\" ";
				echo "&nbsp;";
				break;
			case "lan.xml":
				echo "The LAN profile is the same as the Internal profile except that it is bound to the LAN IP.\n";
				echo "&nbsp;";
				break;
			default:
				//echo "<font color='#FFFFFF'>default</font>&nbsp;";
			}
			echo "	</td>\n";
			echo "	<td valign='middle' nowrap class='list' valign='top'>\n";
			echo "	  <table border='0' cellspacing='2' cellpadding='1'>\n";
			echo "		<tr>\n";
			if (permission_exists('sip_profiles_edit')) {
				echo "		  <td valign='middle'><a href='v_profile_edit.php?type=profile&f=".$file."' alt='edit'>$v_link_label_edit</a></td>\n";
			}
			if (permission_exists('sip_profiles_delete')) {
				echo "		  <td><a href='v_profiles.php?type=profile&a=del&f=".$file."'  alt='delete' onclick=\"return confirm('Do you really want to delete this?')\">$v_link_label_delete</a></td>\n";
			}
			echo "		</tr>\n";
			echo "	 </table>\n";
			echo "	</td>\n";
			echo "</tr>\n";
			if ($c==0) { $c=1; } else { $c=0; }
			$i++;
		}
	}
?>
	</table>
</form>

<?php
if ($v_path_show) {
	echo "<br />\n";
	echo $_SESSION['switch']['conf']['dir']."/sip_profiles\n";
}
?>

<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>

</td>
</tr>
</table>

</div>

<?php 
//show the footer
	require_once "includes/footer.php";
?>