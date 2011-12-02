<?php
	//application details
		$apps[$x]['name'] = "FlashPhoner";
		$apps[$x]['guid'] = 'FE45C76C-1A6E-0F0E-73DD-5B542AED2DD5';
		$apps[$x]['category'] = '';
		$apps[$x]['subcategory'] = '';
		$apps[$x]['version'] = '';
		$apps[$x]['license'] = 'Mozilla Public License 1.1';
		$apps[$x]['url'] = 'http://www.fusionpbx.com';
		$apps[$x]['description']['en'] = 'Allow User to Open a Flash Phone for his Extension.';

	//menu details
		$apps[$x]['menu'][0]['title']['en'] = 'FlashPhoner';
		$apps[$x]['menu'][0]['guid'] = '55E19438-63B9-DA36-415B-B0219F304426';
		$apps[$x]['menu'][0]['parent_guid'] = 'FD29E39C-C936-F5FC-8E2B-611681B266B5';
		$apps[$x]['menu'][0]['category'] = 'internal';
		$apps[$x]['menu'][0]['path'] = '/mod/flashphoner/flashphoner.php';

	//permission details
		$apps[$x]['permissions'][0]['name'] = 'flashphoner_view';
		$apps[$x]['permissions'][0]['groups'][] = 'user';
		$apps[$x]['permissions'][0]['groups'][] = 'admin';
		$apps[$x]['permissions'][0]['groups'][] = 'superadmin';

	// CREATE TABLE v_flashphone_auth 
		$apps[$x]['db'][0]['table'] = 'v_flashphone_auth';
		$apps[$x]['db'][0]['fields'][0]['name'] = 'auth_serial';
		$apps[$x]['db'][0]['fields'][0]['type']['pgsql'] = 'serial';
		$apps[$x]['db'][0]['fields'][0]['type']['sqlite'] = 'integer PRIMARY KEY';
		$apps[$x]['db'][0]['fields'][0]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY';
		$apps[$x]['db'][0]['fields'][0]['description'] = '';
		$apps[$x]['db'][0]['fields'][1]['name'] = 'auth_key';
		$apps[$x]['db'][0]['fields'][1]['type'] = 'text';
		$apps[$x]['db'][0]['fields'][1]['description'] = '';
		$apps[$x]['db'][0]['fields'][2]['name'] = 'hostaddr';
		$apps[$x]['db'][0]['fields'][2]['type'] = 'text';
		$apps[$x]['db'][0]['fields'][2]['description'] = '';
		$apps[$x]['db'][0]['fields'][3]['name'] = 'createtime';
		$apps[$x]['db'][0]['fields'][3]['type']['pgsql'] = 'timestamp';
		$apps[$x]['db'][0]['fields'][3]['type']['sqlite'] = 'date';
		$apps[$x]['db'][0]['fields'][3]['type']['mysql'] = 'timestamp';
		$apps[$x]['db'][0]['fields'][3]['description'] = '';
		$apps[$x]['db'][0]['fields'][4]['name'] = 'username';
		$apps[$x]['db'][0]['fields'][4]['type'] = 'text';
		$apps[$x]['db'][0]['fields'][4]['description'] = '';

?>
