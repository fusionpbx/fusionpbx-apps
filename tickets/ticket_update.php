<div align='center'>
<table width='100%' border='0' cellpadding='0' cellspacing='2'>

<tr class='border'>
  <td align=\"left\">
      <br>

<form method='post' name='ifrm' action=''>

<div align='center'> 
<table width='100%'  border='0' cellpadding='6' cellspacing='0'> 
<tr> 
<td colspan='2'> 
<table width="100%" border="0" cellpadding="0" cellspacing="0"> 
	<tr> 
		<td align='left' width="50%"> 
			<strong>Update Ticket</strong><br> 
		</td>		
		<td width='50%' align='right'> 
			<input type='submit' name='submit' class='btn' value='Save'> 
			<input type='button' class='btn' name='' alt='back' onclick="window.location='v_tickets.php'" value='Back'> 
		</td> 
	</tr>
</table> 
</td> 
</tr> 
<tr>
	<td colspan="2">
		<table width='100%' border="0" cellpadding="0" cellspacing="0">
		<tr><td width="47%" valign="top">
			<table width='100%' border="0" cellpadding="0" cellspacing="0">
<tr><th colspan='2'>Ticket Info</th></tr>
<tr> 
<td width="30%" class='vncell' valign='top' align='left' nowrap='nowrap'> 
	Subject: 
</td> 
<td width="70%" class='vtable' align='left'> 
	<?php echo $ticket_header['subject']; ?>
</td> 
</tr> 
<tr> 
<td class='vncell' valign='top' align='left' nowrap='nowrap'> 
	Requestor: 
</td> 
<td class='vtable' align='left'> 
	<?php echo $ticket_header['username']; ?>
</td> 
</tr> 
<tr> 
<td class='vncell' valign='top' align='left' nowrap='nowrap'> 
	Created: 
</td> 
<td class='vtable' align='left'> 
	<?php echo $ticket_header['create_stamp']; ?> by <?php echo $ticket_header['create_username']; ?>
</td> 
</tr> 
<tr> 
<td class='vncell' valign='top' align='left' nowrap='nowrap'> 
	Last Update: 
</td> 
<td class='vtable' align='left'> 
	<?php echo $ticket_header['last_update_stamp']; ?> by <?php echo $ticket_header['last_update_username']; ?>
</td> 
</tr> 
<tr> 
<td class='vncell' valign='top' align='left' nowrap='nowrap'> 
	Ticket Number: 
</td> 
<td class='vtable' align='left'> 
	<?php echo $ticket_header['ticket_number']; ?>
</td> 
</tr> 
<tr> 
<td class='vncell' valign='top' align='left' nowrap='nowrap'> 
	Customer Ticket Number: 
</td> 
<td class='vtable' align='left'> 
	<input class='formfld' type='text' name='customer_ticket_number' maxlength='255' value="<?php echo $ticket_header['customer_ticket_number']; ?>">
</td> 
</tr> 
	</table>
	</td>
	</td>
	<td width="6%">

	</td>
	<td width="47%" valign="top">
		<table width='100%' border="0" cellpadding="0" cellspacing="0">
<tr><th colspan='2'>Ticket Status</th></tr>
<?php if ($isadmin) { ?>
<tr> 
<td class='vncell' valign='top' align='left' nowrap='nowrap'> 
	Ticket Owner: 
</td> 
<td class='vtable' align='left'> 
	<select name="ticket_owner">
		<option value=''>--</option>
		<?php foreach ($queue_members as $qm) {
			echo "<option value='" . $qm['user_id'] . "' ";
			if ($qm['user_id'] == $ticket_header['ticket_owner']) echo "selected='selected'";
			echo ">" . $qm['username'] . "</option>\n";
		}
		?>
	</select>
</td> 
</tr> 
<?php } ?>
<tr> 
<td class='vncell' valign='top' align='left' nowrap='nowrap'> 
	Ticket status: 
</td> 
<td class='vtable' align='left'> 
	<?php if ($isadmin) { ?>
	<select name="ticket_status">
		<?php foreach ($ticket_statuses as $ts) {
			echo "<option value='" . $ts['status_id'] . "' ";
			if ($ts['status_id'] == $ticket_header['ticket_status']) echo "selected='selected'";
			echo ">" . $ts['status_name'] . "</option>\n";
		}
		?>
	</select>
	<?php } else { 
		foreach($ticket_statuses as $ts) {
			if ($ts['status_id'] == $ticket_header['ticket_status']) echo $ts['status_name'];
		}
	      } ?>
</td> 
</tr> 
<tr> 
<td class='vncell' valign='top' align='left' nowrap='nowrap'> 
	Ticket Queue: 
</td> 
<td class='vtable' align='left'>
	<?php if ($isadmin) { ?>
	<select name="queue_id">
		<?php foreach ($ticket_queues as $tq) {
			echo "<option value='" . $tq['queue_id'] . "' ";
			if ($tq['queue_id'] == $ticket_header['queue_id']) echo "selected='selected'";
			echo ">" . $tq['queue_name'] . "</option>\n";
		}
		?>
	</select>
	<?php } else {
		foreach ($ticket_queues as $tq) {
			if ($tq['queue_id'] == $ticket_header['queue_id']) echo $tq['queue_name'];
		}
	} ?>
</td> 
</tr> 
		</table>
	</td>
	</table>
	</td>
</tr>
	<tr> 
		<td colspan='2' align='right'> 
				<input type='hidden' name='id' value='<?php echo $ticket_header['ticket_id']; ?>'/> 
				<input type='submit' name='submit' class='btn' value='Save'/> 
		</td> 
	</tr>
<tr><td colspan='2'><table width='100%' border="0" cellpadding="0" cellspacing="0">
	<tr><th colspan='2'>Ticket Notes</th></tr>
	<tr>
	<td width="30%" class='rowstyle0' valign='top' align='left' nowrap='nowrap'>
			<input type="checkbox" name="alert_user" value="1"/>Alert User<br>
			Attach File<br />
			<input type="file" name="file" id="file" />
	</td>
	<td class='rowstyle0' align='left'>
		<textarea rows='5' cols='80' class='formfld' type='text' name='new_note'></textarea>
	</td>
	</tr>
	<?php 
$rowstyle[0] = 'rowstyle0';
$rowstyle[1] = 'rowstyle1';
$rs = 0;
foreach($ticket_notes as $tn) { 
if ($rs == 1) { $rs = 0; } else { $rs = 1; }
?>
	<tr>
		<td width="30%" class="<?php echo $rowstyle[$rs]; ?>" valign='top' align='left' nowrap='nowrap'>
			Updated: <?php echo $tn['create_stamp']; ?><br/>By: <?php echo $ticket_header['username']; ?>
			<?php if (strlen($tn['file_pointer']) > 1) echo "<br>File Attachment: " . $tn['file_pointer']; ?>
		</td>
		<td class="<?php echo $rowstyle[$rs]; ?>" align='left'>
			<?php echo base64_decode($tn['ticket_note']); ?>
		</td>
	</tr>
	<?php }?>
</table></td></tr>
	<tr> 
		<td colspan='2' align='right'> 
				<input type='hidden' name='id' value='<?php echo $ticket_header['ticket_id']; ?>'/>
				<input type='submit' name='submit' class='btn' value='Save'/> 
		</td> 
	</tr>
</table></form>	</td>	</tr></table></div>

