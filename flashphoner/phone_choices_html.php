<script type="text/javascript">
function submitform()
{
	if(document.flashform.onsubmit &&
	!document.flashform.onsubmit())
	{
		return;
	}
	document.flashform.submit();
}

function start_phone()
{
	var testvar = document.getElementById("extension_id").value; 
	window.open("/flashphoner/phone.php?extension_id="+testvar+"&key=<?php echo $key;?>&username=<?php echo $_SESSION['username'] ?>", 
		"FlashPhoner", "height=300, width=230");
}

</script>

<div>
<form action="/flashphoner/phone.php" name="flashform">
<select id="extension_id">
<?php 
foreach($extension_array as $row)
printf('<option value="%s">%s</option>'."\n", $row['extension_id'], $row['extension']);
?>
</select>
</form>
<a href='javascript:start_phone();'>
<img src="phone.jpg" /><br /> Click here to Open Your Phone</a>
<div>
