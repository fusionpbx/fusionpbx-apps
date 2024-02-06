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
		<td align='left' colspan="2">
			<strong>Ticket Module Manager</strong><br> 
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
<!-- <tr><td colspan='2'>Queues</td></tr> -->
<tr><th>ID</th><th>Queue Name</th><th>Queue Email</th><td></td></tr>
<?php 
$row_style[0] = 'row_style0';
$row_style[1] = 'row_style1';
$rs = 0;
foreach ($queues as $queue) { 
if ($rs == 1) { $rs = 0; } else { $rs = 1; }
?>
<form method='post' name='ifrm' action=''>
<tr>
<td width="10%" class='<?php echo $row_style[$rs]; ?>' align="center"><?php echo $queue['queue_id']; ?>
	<input type="hidden" name="queue_id" value="<?php echo $queue['queue_id']; ?>"/>
</td>
<td width="20%" class='<?php echo $row_style[$rs]; ?>' align='left' nowrap='nowrap'> 
	<input class='formfld' style="width:100%" type="text" name="queue_name" value="<?php echo $queue['queue_name']; ?>"/>
</td> 
<td width="70%" class='<?php echo $row_style[$rs]; ?>' align='left'> 
	<input class='formfld' style="width:100%" type="text" name="queue_email" value="<?php echo $queue['queue_email']; ?>"/>
</td>
<td align='left' nowrap='nowrap'>
	<input type="submit" value="update">
</td>
</tr> 
</form>
<?php } 
if ($rs == 1) { $rs = 0; } else { $rs = 1; }
?>
<form method='post' name='ifrm' action=''>
<tr>
<td width="10%" class='<?php echo $row_style[$rs]; ?>' align="center" nowrap='nowrap'>
</td>
<td width="20%" class='<?php echo $row_style[$rs]; ?>' align='left' nowrap='nowrap'> 
	<input class='formfld' style="width:100%" type="text" name="queue_name" />
</td> 
<td width="70%" class='<?php echo $row_style[$rs]; ?>' align='left'> 
	<input class='formfld' style="width:100%" type="text" name="queue_email" />
</td>
<td align='left' nowrap='nowrap'>
	<input type="submit" value="Add">
</td>
</tr> 
</form>
	</table>
	</td>
	</td>
	<td width="6%">

	</td>
	<td width="47%" valign="top">
		<table width='100%' border="0" cellpadding="0" cellspacing="0">
<!-- <tr><th colspan='2'>Statuses</th></tr> -->
<tr><th>Status ID</th><th>Status Name</th><td></td></tr>
<?php 
foreach ($statuses as $status) { 
if ($rs == 1) { $rs = 0; } else { $rs = 1; }
?>
<form method='post' name='ifrm' action=''>
<tr> 
<td class='<?php echo $row_style[$rs]; ?>' align='left' nowrap='nowrap' width="20%"> 
	<input type="hidden" name="status_id" value="<?php echo $status['status_id']; ?>"/>
	<?php echo $status['status_id']; ?>
</td> 
<td class='<?php echo $row_style[$rs]; ?>' align='left'> 
	<input class='formfld' type="text" style="width:100%" name="status_name" value="<?php echo $status['status_name']; ?>"/>
</td> 
<td align='left' nowrap='nowrap'>
	<input type="submit" value="update">
</td>
</tr>
</form>
<?php } 
if ($rs == 1) { $rs = 0; } else { $rs = 1; }
?>
<form method='post' name='ifrm' action=''>
<tr> 
<td class='<?php echo $row_style[$rs]; ?>' align='left' nowrap='nowrap' width="20%"> 
</td> 
<td class='<?php echo $row_style[$rs]; ?>' align='left'> 
	<input class='formfld' type="text" style="width:100%" name="status_name"/>
</td> 
<td align='left' nowrap='nowrap'>
	<input type="submit" value="Add">
</td>
</tr>
</form>
		</table>
	</td>
	</table>
	</td>
</table></form>	</td>	</tr></table></div>
