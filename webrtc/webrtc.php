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
	Giovanni Maruzzelli <gmaruzz@opentelecom.it>
	Len Graham <Len.PGH@gmail.com>
*/

//includes
	require_once "root.php";
	require_once "resources/require.php";

//check permissions
	require_once "resources/check_auth.php";
	if (permission_exists('webrtc')) {
		//access granted
	}
	else {
		echo "access denied";
		exit;
	}

//add multi-lingual support
	$language = new text;
	$text = $language->get();

//get variables used to control the order
	$order_by = $_GET["order_by"];
	$order = $_GET["order"];

//additional includes
	require_once "resources/header.php";
	require_once "resources/paging.php";

	echo "  <meta charset=\"utf-8\">\n"; 
	echo "  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n"; 
	echo "  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n"; 
	echo "  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->\n"; 
	echo "  <meta name=\"description\" content=\"A WebRTC client for Verto FreeSWITCH module\">\n"; 
	echo "  <meta name=\"author\" content=\"Giovanni Maruzzelli\">\n"; 
	echo "  <link rel=\"icon\" href=\"favicon.ico\">\n"; 
	echo "  <!-- Bootstrap core CSS -->\n"; 
	echo "  <link href=\"css/bootstrap.min.css\" rel=\"stylesheet\">\n"; 
	echo "  <!-- Custom styles for this template -->\n"; 
	//echo "  <link href=\"high.css\" rel=\"stylesheet\">\n"; 

//prepare to page the results
	$sql = "select count(*) as num_rows from v_webrtc ";
	if (strlen($order_by)> 0) { $sql .= "order by $order_by $order "; }
	$prep_statement = $db->prepare($sql);
	if ($prep_statement) {
		$prep_statement->execute();
		$row = $prep_statement->fetch(PDO::FETCH_ASSOC);
		if ($row['num_rows'] > 0) {
				$num_rows = $row['num_rows'];
		}
		else {
				$num_rows = '0';
		}
	}

//prepare to page the results
	$rows_per_page = ($_SESSION['domain']['paging']['numeric'] != '') ? $_SESSION['domain']['paging']['numeric'] : 50;
	$param = "";
	$page = $_GET['page'];
	if (strlen($page) == 0) { $page = 0; $_GET['page'] = 0; }
	list($paging_controls, $rows_per_page, $var3) = paging($num_rows, $param, $rows_per_page);
	$offset = $rows_per_page * $page;

//get the user ID
	$sql = "SELECT extension,v_extensions.password,effective_caller_id_name FROM ";
	$sql .= "v_extension_users, v_extensions, v_users ";
	$sql .= "WHERE v_users.user_uuid = v_extension_users.user_uuid ";
	$sql .= "AND v_extensions.extension_uuid = v_extension_users.extension_uuid ";
	$sql .= "AND v_users.user_uuid = '" . $_SESSION['user_uuid'] . "' ";
	$sql .= "AND v_extensions.domain_uuid = '" . $_SESSION["domain_uuid"] . "' LIMIT 1";
	
	$prep_statement = $db->prepare($sql);
	if ($prep_statement) {
		$prep_statement->execute();
		$row = $prep_statement->fetch(PDO::FETCH_ASSOC);
		$user_extension = $row['extension'];
		$user_password = $row['password'];
		$effective_caller_id_name = $row['effective_caller_id_name'];
	}

//show the content
	echo "  <div id=\"conference\">\n"; 
	echo "   <input type=\"hidden\" id=\"hostName\" value=\"" . $_SESSION['domain_name'] . "\"/>\n"; 
	echo "   <input type=\"hidden\" id=\"wsURL\" value=\"wss://" . $_SESSION['domain_name'] . ":8082\"/>\n"; 
	echo "   <input type=\"hidden\" id=\"login\" value=\"" . $user_extension . "\"/>\n"; 
	echo "   <input type=\"hidden\" id=\"passwd\" value=\"" . $user_password . "\"/>\n"; 
	echo "   <input type=\"hidden\" id=\"cidnumber\" value=\"" . $effective_caller_id_name . "\"/>\n"; 
	echo "   <div class=\"form-signin\">\n";
	echo "    <h2 class=\"form-signin-heading\">" . $_SESSION['theme']['webrtc_title']['text'] . "</h2>\n"; 
	echo "    <div id=\"content\" class=\"form-signin-content\">\n"; 
	echo "     <input type=\"text\" id=\"ext\" class=\"form-control\" placeholder=\"Number to dial ? (eg: 3000)\" required autofocus>\n"; 
	echo "     <button class=\"btn btn-lg btn-primary btn-success\" data-inline=\"true\" id=\"extbtn\">Call</button>\n"; 
	echo "     <input type=\"text\" id=\"cidname\" class=\"form-control\" placeholder=\"insert here your NAME (eg: Squidward)\" required autofocus>\n"; 
	echo "     <button class=\"btn btn-lg btn-primary btn-success\" data-inline=\"true\" id=\"callbtn\">Call</button>\n"; 
	echo "     <button class=\"btn btn-lg btn-primary btn-danger\" data-inline=\"true\" id=\"backbtn\">Back</button>\n"; 
	echo "    </div>\n";
	echo "    <div id=\"video1\" align=\"center\" class=\"embed-responsive embed-responsive-4by3\">\n"; 
	echo "     <video id=\"webcam\" autoplay=\"autoplay\" class=\"embed-responsive-item\"> </video>\n"; 
	echo "    </div>\n";
	echo "    <button class=\"btn btn-lg btn-primary btn-danger\" data-inline=\"true\" id=\"hupbtn\">Hangup</button>\n"; 
	echo "    <br id=\"br\"/>\n"; 
	echo "    <textarea id=\"chatwin\" class=\"form-control\" rows=\"5\" readonly></textarea>\n"; 
	echo "    <br id=\"br\"/>\n"; 
	echo "    <textarea id=\"chatmsg\" class=\"form-control\" rows=\"1\" placeholder=\"type here your chat msg\" autofocus></textarea>\n"; 
	echo "    <button class=\"btn btn-primary btn-success\" data-inline=\"true\" id=\"chatsend\">Send Msg</button>\n"; 
	echo "   </div>\n"; 
	echo "  </div>\n"; 
	echo "  <script type=\"text/javascript\" src=\"js/jquery.min.js\"></script>\n"; 
	echo "  <script type=\"text/javascript\" src=\"js/jquery.json-2.4.min.js\"></script>\n"; 
	echo "  <script type=\"text/javascript\" src=\"js/verto-min.js\"></script>\n"; 
	echo "  <script type=\"text/javascript\" src=\"high.js\"></script>\n"; 
	echo " </body> </html>\n"; 
	echo "\n";

//include the footer
	require_once "resources/footer.php";

?>
