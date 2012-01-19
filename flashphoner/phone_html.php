<script type="text/javascript">

function start_phone()
{
	window.open("/flashphoner/phone.php?key=<?php echo $key;?>&extension_uuid=<?php echo $extension_uuid;?>&username=<?php echo $_SESSION['username'] ?>", "FlashPhoner", "height=300, width=230, location=0, menubar=0");
}

</script>
<div>
<a href='javascript:start_phone();'>
<img src="phone.jpg" /><br /> Click here to Open Your Phone</a>
<div>
