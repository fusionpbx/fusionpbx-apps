<?php
	//application details
		$apps[$x]['name'] = "Voicemail Status";
		$apps[$x]['uuid'] = '9ecd085e-8c0e-92f6-e727-e90f6bb57773';
		$apps[$x]['category'] = 'Switch';;
		$apps[$x]['subcategory'] = '';
		$apps[$x]['version'] = '';
		$apps[$x]['license'] = 'Mozilla Public License 1.1';
		$apps[$x]['url'] = 'http://www.fusionpbx.com';
		$apps[$x]['description']['en-us'] = 'Shows which extensions have voicemails and how many.';
		$apps[$x]['description']['es-mx'] = '';
		$apps[$x]['description']['de-de'] = '';
		$apps[$x]['description']['de-ch'] = '';
		$apps[$x]['description']['de-at'] = '';
		$apps[$x]['description']['fr-fr'] = '';
		$apps[$x]['description']['fr-ca'] = '';
		$apps[$x]['description']['fr-ch'] = '';
		$apps[$x]['description']['pt-pt'] = 'Mostra quais extensões têm mensagens de voz e quantas.';
		$apps[$x]['description']['pt-br'] = '';

	//permission details
		$apps[$x]['permissions'][0]['name'] = 'voicemail_status_view';
		$apps[$x]['permissions'][0]['groups'][] = 'admin';
		$apps[$x]['permissions'][0]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][1]['name'] = 'voicemail_status_delete';
		$apps[$x]['permissions'][1]['groups'][] = 'admin';
		$apps[$x]['permissions'][1]['groups'][] = 'superadmin';

?>