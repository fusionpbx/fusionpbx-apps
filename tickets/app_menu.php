<?php

	$apps[$x]['menu'][0]['title']['en'] = 'Ticket Tracker';
	$apps[$x]['menu'][0]['uuid'] = '77048e9f-b946-ad35-5d6b-7838dd9ea81e';
	$apps[$x]['menu'][0]['parent_uuid'] = '';
	$apps[$x]['menu'][0]['category'] = 'internal';
	$apps[$x]['menu'][0]['path'] = '/app/tickets/v_tickets.php';
	//$apps[$x]['menu'][0]['groups'][] = 'user';
	//$apps[$x]['menu'][0]['groups'][] = 'admin';
	//$apps[$x]['menu'][0]['groups'][] = 'superadmin';

	$apps[$x]['menu'][1]['title']['en'] = 'My Tickets';
	$apps[$x]['menu'][1]['uuid'] = '5bfa588d-5880-5a9d-206e-c876403d9161';
	$apps[$x]['menu'][1]['parent_uuid'] = '77048e9f-b946-ad35-5d6b-7838dd9ea81e';
	$apps[$x]['menu'][1]['category'] = 'internal';
	$apps[$x]['menu'][1]['path'] = '/app/tickets/v_tickets.php';
	$apps[$x]['menu'][1]['groups'][] = 'user';
	$apps[$x]['menu'][1]['groups'][] = 'admin';
	$apps[$x]['menu'][1]['groups'][] = 'superadmin';

	$apps[$x]['menu'][2]['title']['en'] = 'Create Ticket';
	$apps[$x]['menu'][2]['uuid'] = '87a8f1e7-cf47-29db-8a5f-46318e119d67';
	$apps[$x]['menu'][2]['parent_uuid'] = '77048e9f-b946-ad35-5d6b-7838dd9ea81e';
	$apps[$x]['menu'][2]['category'] = 'internal';
	$apps[$x]['menu'][2]['path'] = '/app/tickets/v_ticket_create.php';
	$apps[$x]['menu'][2]['groups'][] = 'user';
	$apps[$x]['menu'][2]['groups'][] = 'admin';
	$apps[$x]['menu'][2]['groups'][] = 'superadmin';

	$apps[$x]['menu'][3]['title']['en'] = 'Ticket System Manager';
	$apps[$x]['menu'][3]['uuid'] = 'a4f3b307-bd62-1d04-e3ad-43da091fa2f8';
	$apps[$x]['menu'][3]['parent_uuid'] = '77048e9f-b946-ad35-5d6b-7838dd9ea81e';
	$apps[$x]['menu'][3]['category'] = 'internal';
	$apps[$x]['menu'][3]['path'] = '/app/tickets/v_manager.php';
	$apps[$x]['menu'][3]['groups'][] = 'admin';
	$apps[$x]['menu'][3]['groups'][] = 'superadmin';

?>