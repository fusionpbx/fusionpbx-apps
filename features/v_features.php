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
if (if_group("admin") || if_group("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

require_once "includes/header.php";

?><br />

<div align='center'>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
	 <td class="tabcont" align='left'>

	<table width="100%" border="0" cellpadding="6" cellspacing="0">
	  <tr>
		<td><p><span class="vexpl"><span class="red"><strong>Features<br>
			</strong></span>
			Lists some of the features available with a descriptions and link.
			</p></td>
	  </tr>
	</table>
	<br />


	<table width="100%" border="0" cellpadding="6" cellspacing="0">
	<tr>
	  <th colspan='2' align='left'>&nbsp;</th>
	</tr>
	<tr>
		<td width='20%' class="vncell" style='text-align: center;'><a href='<?php echo PROJECT_PATH; ?>/mod/auto_attendant/v_auto_attendant.php'>Auto Attendant</a></td>
		<td class="vtable">
			Auto Attendant provides callers the ability to choose between multiple options that direct calls to extensions, 
			voicemail, conferences, queues, other auto attendants, and external phone numbers.
		</td>
	</tr>
	<tr>
		<td width='10%' class="vncell" style='text-align: center;'><a href='<?php echo PROJECT_PATH; ?>/core/cdr/v_cdr.php'>Call Detail Records</a></td>
		<td class="vtable">
			Call Detail Records (CDRs) are detailed information on the calls. The information contains source, 
			destination, duration, and other useful call details. Use the fields to filter the information for
			the specific call records that are desired. Then view the calls in the list or download them as comma
			seperated file by using the 'csv' button.
		</td>
	</tr>
	<tr>
		<td width='10%' class="vncell" style='text-align: center;'><a href='<?php echo PROJECT_PATH; ?>/mod/xml_edit/' target='_blank'>XML Editor</a></td>
		<td class="vtable">
			Configuration editor enables advanced configuration changes.
		</td>
	</tr>
	<tr>
		<td width='10%' class="vncell" style='text-align: center;'>Direct Inward System Access</td>
		<td class="vtable">
			Direct Inward System Access (DISA) allows inbound callers to make internal or external calls. For security reasons 
			it is disabled by default. To enable it first set a secure pin number from the Settings->Admin PIN Number.
			Then go to Dialplan tab and find the DISA entry and edit it to set 'Enabled' to 'true'. 
			To use DISA dial *3472 (disa) enter the admin pin code and the extension or phone number you wish to call.
		</td>
	</tr>
	<?php
	if ($v_fax_show) {
	?>
	<tr>
		<td width='10%' class="vncell" style='text-align: center;'><a href='<?php echo PROJECT_PATH; ?>/mod/fax/v_fax.php'>FAX</a></td>
		<td class="vtable">
			Transmit and View Received Faxes.
		</td>
	</tr>
	<?php
	}
	?>	<tr>
		<td width='10%' class="vncell" style='text-align: center;'><a href='<?php echo PROJECT_PATH; ?>/mod/hunt_group/v_hunt_group.php'>Hunt Group</a></td>
		<td class="vtable">
			Hunt Group is a group of destinations to call at once or in succession.
		</td>
	</tr>

	<tr>
		<td width='10%' class="vncell" style='text-align: center;'><a href='<?php echo PROJECT_PATH; ?>/core/modules/v_modules.php'>Modules</a></td>
		<td class="vtable">
			Modules add additional features and can be enabled or disabled to provide the desired features.
		</td>
	 </tr>

	 <tr>
		<td width='10%' class="vncell" style='text-align: center;'><a href='<?php echo PROJECT_PATH; ?>/mod/recordings/v_recordings.php'>Music on Hold</a></td>
		<td class="vtable">
			Music on hold can be in WAV or MP3 format. To play an MP3 files you must have mod_shout enabled on the 'Modules' tab. 
			For best performance upload 16bit 8khz/16khz Mono WAV files.
		</td>
	</tr>

	<tr>
		<td width='10%' class="vncell" style='text-align: center;'><a href='<?php echo PROJECT_PATH; ?>/mod/recordings/v_recordings.php'>Recordings</a></td>
		<td class="vtable">
			To make a recording dial *732673 (record) or you can make a 16bit 8khz/16khz
			Mono WAV file then copy it to the following directory then refresh the page to play
			it back. Click on the 'Filename' to download it or the 'Recording Name' to play the audio.
		</td>
	</tr>

	<tr>
		<td width='10%' class="vncell" style='text-align: center;'><a href='<?php echo PROJECT_PATH; ?>/mod/voicemail_status/v_voicemail.php'>Voicemail Status</a></td>
		<td class="vtable">
			Provides a list of all voicemail boxes with the total number of voicemails for each box.
			Each voicemail box has a button to 'restore default preferences' this removes the greetings
			and sets the voicemail greetings back to default.
		</td>
	</tr>
	</table>
<br />
<br />
<br />
<br />
<br />
<br />
<br />
<br />

</td>
</tr>
</table>

</div>


<?php

require_once "includes/footer.php";

?>
</body>
</html>
