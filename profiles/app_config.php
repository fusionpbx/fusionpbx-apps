<?php
	//application details
		$apps[$x]['name'] = "SIP Profiles";
		$apps[$x]['uuid'] = '5414b2d9-fd7c-f4fa-3c31-eecc387bd1e4';
		$apps[$x]['category'] = 'Switch';;
		$apps[$x]['subcategory'] = '';
		$apps[$x]['version'] = '';
		$apps[$x]['license'] = 'Mozilla Public License 1.1';
		$apps[$x]['url'] = 'http://www.fusionpbx.com';
		$apps[$x]['description']['en'] = 'Use this to configure your SIP profiles.';

	//menu details
		$apps[$x]['menu'][0]['title']['en'] = 'SIP Profiles';
		$apps[$x]['menu'][0]['uuid'] = '3fe562d4-b9d2-74d2-7def-bff4707831e2';
		$apps[$x]['menu'][0]['parent_uuid'] = '594d99c5-6128-9c88-ca35-4b33392cec0f';
		$apps[$x]['menu'][0]['category'] = 'internal';
		$apps[$x]['menu'][0]['path'] = '/app/profiles/v_profiles.php';
		$apps[$x]['menu'][0]['groups'][] = 'superadmin';

	//permission details
		$apps[$x]['permissions'][0]['name'] = 'sip_profiles_view';
		$apps[$x]['permissions'][0]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][1]['name'] = 'sip_profiles_add';
		$apps[$x]['permissions'][1]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][2]['name'] = 'sip_profiles_edit';
		$apps[$x]['permissions'][2]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][3]['name'] = 'sip_profile_delete';
		$apps[$x]['permissions'][3]['groups'][] = 'superadmin';
?>