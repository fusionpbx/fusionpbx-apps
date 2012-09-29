<?php
/* $Id$ */
/*
	v_exec.php
	Copyright (C) 2008 Mark J Crane
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice,
	   this list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGE.
*/
require_once "root.php";
require_once "includes/require.php";
require_once "includes/checkauth.php";

if (count($_GET)>0) {
	$id = check_str($_GET["id"]);
	$invoice_uuid = check_str($_GET["invoice_uuid"]);
	$contact_uuid = check_str($_GET["contact_uuid"]);
}

if (strlen($id)>0) {
	$sql = "";
	$sql .= "delete from v_invoice_items ";
	$sql .= "where invoice_item_uuid = '$id' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	unset($sql);
}

require_once "includes/header.php";
echo "<meta http-equiv=\"refresh\" content=\"2;url=v_invoices_edit.php?id=$invoice_uuid&contact_uuid=$contact_uuid\">\n";
echo "<div align='center'>\n";
echo "Delete Complete\n";
echo "</div>\n";
require_once "includes/footer.php";
return;

?>