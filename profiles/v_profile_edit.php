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
if (permission_exists('sip_profiles_edit')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

$fd = fopen($_SESSION['switch']['conf']['dir']."/sip_profiles/".$_GET['f'], "r");
$v_content = fread($fd, filesize($_SESSION['switch']['conf']['dir']."/sip_profiles/".$_GET['f']));
fclose($fd);

require_once "includes/header.php";

?>

<script language="Javascript">
function sf() { document.forms[0].savetopath.focus(); }
</script>
<script language="Javascript" type="text/javascript" src="<?php echo PROJECT_PATH; ?>/includes/edit_area/edit_area_full.js"></script>
<script language="Javascript" type="text/javascript">
	// initialisation
	editAreaLoader.init({
		id: "code"	// id of the textarea to transform
		,start_highlight: true
		,allow_toggle: false
		,language: "en"
		,syntax: "html"	
		,toolbar: "search, go_to_line,|, fullscreen, |, undo, redo, |, select_font, |, syntax_selection, |, change_smooth_selection, highlight, reset_highlight, |, help"
		,syntax_selection_allow: "css,html,js,php,xml,c,cpp,sql"
		,show_line_colors: true
	});	
</script>

<div align='center'>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="tabcont" >

				<form action="v_profiles.php" method="post" name="iform" id="iform">
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td align='left'><p><span class="vexpl"><span class="red"><strong>Edit Profile<br>
							</strong></span>
							Use this to configure your SIP profiles.
							<br />
							<br />
						</td>
						<td align='right' valign='top'>Filename: <input type="text" name="f" value="<?php echo $_GET['f']; ?>" /><input type="submit" class='btn' value="save" /></td>
					</tr>

					<tr>
					<td colspan='2' class='' valign='top' align='left' nowrap>
						<textarea style="width:100%;" id="code" name="code" rows="35" class='txt'><?php echo htmlentities($v_content); ?></textarea>
					<br />
					<br />
					</td>
					</tr>

					<tr>
						<td align='left'>
						<?php
						if ($v_path_show) {
							echo "<b>location:</b> ".$_SESSION['switch']['conf']['dir']."/sip_profiles/".$_GET['f']."</td>";
						}
						?>
						<td align='right'>
							<input type="hidden" name="a" value="save" />
							<?php
							echo "<input type='button' class='btn' value='Restore Default' onclick=\"document.location.href='v_profiles.php?a=default&f=".$_GET['f']."';\" />";
							?>
						</td>
					</tr>

					<tr>
					<td colspan='2'>
						<br /><br /><br />
						<br /><br /><br />
						<br /><br /><br />
						<br /><br /><br />
						<br /><br /><br />
						<br /><br /><br />
						<br /><br /><br />
						<br /><br /><br />
						<br /><br /><br />
						<br /><br /><br />
					</td>
					</tr>

					</table>
				</form>

			</td>
		</tr>
	</table>
</div>

<?php
//show the footer
	require_once "includes/footer.php";
?>