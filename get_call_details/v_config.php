<?php
	//application details
		$apps[$x]['name'] = "Get Call Details";
		$apps[$x]['uuid'] = 'a1200636-cc9e-4636-852c-3ac4ad1bbaa6';
		$apps[$x]['category'] = 'PBX';
		$apps[$x]['subcategory'] = '';
		$apps[$x]['version'] = '';
		$apps[$x]['license'] = 'Mozilla Public License 1.1';
		$apps[$x]['url'] = 'http://www.fusionpbx.com';
		$apps[$x]['description']['en'] = 'Get call details for active calls results are in json.';

	//permission details
		$apps[$x]['permissions'][0]['name'] = 'get_call_details';
		$apps[$x]['permissions'][0]['groups'][] = 'superadmin';

?>