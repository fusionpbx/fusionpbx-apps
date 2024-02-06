<div align='center'>
<table width='100%' border='0' cellpadding='0' cellspacing='2'>

<tr class='border'>
  <td align="left">
      <br>

<form method='post' name='ifrm' action=''>

<div align='center'> 
<table width='100%'  border='0' cellpadding='6' cellspacing='0'> 
<tr> 
<td colspan='2'> 
<table width="100%" border="0" cellpadding="0" cellspacing="0"> 
	<tr> 
		<td align='left' width="50%"> 
			<strong>Create a new Ticket</strong><br> 
		</td>		<td width='50%' align='right'> 
			<input type='submit' name='submit' class='btn' value='Save'> 
			<input type='button' class='btn' name='' alt='back' onclick="window.location='v_xmpp.php'" value='Back'> 
		</td> 
	</tr>	
</table> 
<br /> 
</td> 
</tr> 
<tr> 
<td width="30%" class='vncellreq' valign='top' align='left' nowrap='nowrap'> 
    Subject:
</td> 
<td width="70%" class='vtable' align='left'> 
    <input class='formfld' type='text' name='subject' maxlength='255' value="<?php echo $profile['subject']; ?>"> 
<br /> 
1 line Description of problem
</td> 
</tr> 
<tr> 
<td width="30%" class='vncellreq' valign='top' align='left' nowrap='nowrap'> 
    Department:
</td> 
<td width="70%" class='vtable' align='left'>
	<select name="queue_id">
	<?php foreach($queues as $queue) {
		echo "<option value='" . $queue['queue_id'] . "'>". $queue['queue_name'] . "</option>\n";
	} ?>
	</select>
</td> 
</tr> 
<tr> 
<td class='vncellreq' valign='top' align='left' nowrap='nowrap'> 
    Detailed Description:
</td> 
<td class='vtable' align='left'> 
    <textarea rows='15' cols='80' class='formfld' type='text' name='problem_description'><?php echo $profile['problem_description'];?></textarea>
<br /> 
Enter the a Detailed Description of the problem here.<br/>
Please include any addition contact information as may be required for us to work this ticket.
</td> 
</tr> 
	<tr> 
		<td colspan='2' align='right'> 
				<input type='hidden' name='profile_id' value='<?php echo $profile['xmpp_profile_uuid']; ?>'> 
				<input type='submit' name='submit' class='btn' value='Save'> 
		</td> 
	</tr></table></form>	</td>	</tr></table></div>

