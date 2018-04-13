<?php

	//application details
		$apps[$x]['name'] = "Active Extensions";
		$apps[$x]['uuid'] = "8ce1121c-fc4b-48b3-96d3-93a399809315";
		$apps[$x]['category'] = "Switch";
		$apps[$x]['subcategory'] = "";
		$apps[$x]['version'] = "";
		$apps[$x]['license'] = "Mozilla Public License 1.1";
		$apps[$x]['url'] = "http://www.fusionpbx.com";
		$apps[$x]['description']['en-us'] = "List of active extensions.";
		$apps[$x]['description']['es-cl'] = "";
		$apps[$x]['description']['de-de'] = "";
		$apps[$x]['description']['de-ch'] = "";
		$apps[$x]['description']['de-at'] = "";
		$apps[$x]['description']['fr-fr'] = "";
		$apps[$x]['description']['fr-ca'] = "";
		$apps[$x]['description']['fr-ch'] = "";
		$apps[$x]['description']['pt-pt'] = "";
		$apps[$x]['description']['pt-br'] = "";

	//permission details
		$apps[$x]['permissions'][0]['name'] = "active_extension_view";
		$apps[$x]['permissions'][0]['menu']['uuid'] = "eba3d07f-dd5c-6b7b-6880-493b44113ade";
		$apps[$x]['permissions'][0]['groups'][] = "superadmin";
		$apps[$x]['permissions'][0]['groups'][] = "admin";

		//$apps[$x]['permissions'][2]['name'] = "active_extension_assigned_view";
		//$apps[$x]['permissions'][2]['groups'][] = "superadmin";
		//$apps[$x]['permissions'][2]['groups'][] = "admin";

?>
