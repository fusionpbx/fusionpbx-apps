<?php
	//application details
		$apps[$x]['name'] = "Ticket Tracker";
		$apps[$x]['guid'] = '375715DC-F852-4A7B-B5C4-32FF163B3953';
		$apps[$x]['category'] = '';
		$apps[$x]['subcategory'] = '';
		$apps[$x]['version'] = '';
		$apps[$x]['license'] = 'Mozilla Public License 1.1';
		$apps[$x]['url'] = 'http://www.fusionpbx.com';
		$apps[$x]['description']['en'] = 'Simple Ticket Tracking System';

	//menu details
		$apps[$x]['menu'][0]['title']['en'] = 'Ticket Tracker';
		$apps[$x]['menu'][0]['guid'] = '77048E9F-B946-AD35-5D6B-7838DD9EA81E';
		$apps[$x]['menu'][0]['parent_guid'] = '';
		$apps[$x]['menu'][0]['category'] = 'internal';
		$apps[$x]['menu'][0]['path'] = '/mod/tickets/v_tickets.php';
		//$apps[$x]['menu'][0]['groups'][] = 'user';
		//$apps[$x]['menu'][0]['groups'][] = 'admin';
		//$apps[$x]['menu'][0]['groups'][] = 'superadmin';

		$apps[$x]['menu'][1]['title']['en'] = 'My Tickets';
		$apps[$x]['menu'][1]['guid'] = '5BFA588D-5880-5A9D-206E-C876403D9161';
		$apps[$x]['menu'][1]['parent_guid'] = '77048E9F-B946-AD35-5D6B-7838DD9EA81E';
		$apps[$x]['menu'][1]['category'] = 'internal';
		$apps[$x]['menu'][1]['path'] = '/mod/tickets/v_tickets.php';
		$apps[$x]['menu'][1]['groups'][] = 'user';
		$apps[$x]['menu'][1]['groups'][] = 'admin';
		$apps[$x]['menu'][1]['groups'][] = 'superadmin';

		$apps[$x]['menu'][2]['title']['en'] = 'Create Ticket';
		$apps[$x]['menu'][2]['guid'] = '87A8F1E7-CF47-29DB-8A5F-46318E119D67';
		$apps[$x]['menu'][2]['parent_guid'] = '77048E9F-B946-AD35-5D6B-7838DD9EA81E';
		$apps[$x]['menu'][2]['category'] = 'internal';
		$apps[$x]['menu'][2]['path'] = '/mod/tickets/v_ticket_create.php';
		$apps[$x]['menu'][2]['groups'][] = 'user';
		$apps[$x]['menu'][2]['groups'][] = 'admin';
		$apps[$x]['menu'][2]['groups'][] = 'superadmin';

		$apps[$x]['menu'][3]['title']['en'] = 'Ticket System Manager';
		$apps[$x]['menu'][3]['guid'] = 'A4F3B307-BD62-1D04-E3AD-43DA091FA2F8';
		$apps[$x]['menu'][3]['parent_guid'] = '77048E9F-B946-AD35-5D6B-7838DD9EA81E';
		$apps[$x]['menu'][3]['category'] = 'internal';
		$apps[$x]['menu'][3]['path'] = '/mod/tickets/v_manager.php';
		$apps[$x]['menu'][3]['groups'][] = 'admin';
		$apps[$x]['menu'][3]['groups'][] = 'superadmin';

	//permission details
		$apps[$x]['permissions'][0]['name'] = 'ticket_view';
		$apps[$x]['permissions'][0]['groups'][] = 'user';
		$apps[$x]['permissions'][0]['groups'][] = 'admin';
		$apps[$x]['permissions'][0]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][1]['name'] = 'ticket_add';
		$apps[$x]['permissions'][1]['groups'][] = 'user';
		$apps[$x]['permissions'][1]['groups'][] = 'admin';
		$apps[$x]['permissions'][1]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][2]['name'] = 'ticket_update';
		$apps[$x]['permissions'][2]['groups'][] = 'user';
		$apps[$x]['permissions'][2]['groups'][] = 'admin';
		$apps[$x]['permissions'][2]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][3]['name'] = 'ticket_delete';
		$apps[$x]['permissions'][3]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][4]['name'] = 'ticket_assign_queue';
		$apps[$x]['permissions'][4]['groups'][] = 'admin';
		$apps[$x]['permissions'][4]['groups'][] = 'superadmin';
	
		$apps[$x]['permissions'][5]['name'] = 'ticket_config';
		$apps[$x]['permissions'][5]['groups'][] = 'admin';
		$apps[$x]['permissions'][5]['groups'][] = 'superadmin';
	
		$apps[$x]['permissions'][6]['name'] = 'ticket_queue_view';
		$apps[$x]['permissions'][6]['groups'][] = 'admin';
		$apps[$x]['permissions'][6]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][7]['name'] = 'ticket_queue_add';
		$apps[$x]['permissions'][7]['groups'][] = 'admin';
		$apps[$x]['permissions'][7]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][8]['name'] = 'ticket_queue_update';
		$apps[$x]['permissions'][8]['groups'][] = 'admin';
		$apps[$x]['permissions'][8]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][9]['name'] = 'ticket_queue_delete';
		$apps[$x]['permissions'][9]['groups'][] = 'admin';
		$apps[$x]['permissions'][9]['groups'][] = 'superadmin';
	
	// CREATE TABLE v_ticket_notes 
		$apps[$x]['db'][0]['table'] = 'v_ticket_notes';
		$apps[$x]['db'][0]['fields'][0]['name'] = 'note_id';
		$apps[$x]['db'][0]['fields'][0]['type']['pgsql'] = 'serial';
		$apps[$x]['db'][0]['fields'][0]['type']['sqlite'] = 'integer PRIMARY KEY';
		$apps[$x]['db'][0]['fields'][0]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY';
		$apps[$x]['db'][0]['fields'][0]['description'] = '';
		$apps[$x]['db'][0]['fields'][1]['name'] = 'ticket_id';
		$apps[$x]['db'][0]['fields'][1]['type'] = 'integer';
		$apps[$x]['db'][0]['fields'][1]['description'] = '';
		$apps[$x]['db'][0]['fields'][2]['name'] = 'create_stamp';
		$apps[$x]['db'][0]['fields'][2]['type']['pgsql'] = 'timestamp with time zone';
		$apps[$x]['db'][0]['fields'][2]['type']['sqlite'] = 'datetime';
		$apps[$x]['db'][0]['fields'][2]['type']['mysql'] = 'timestamp';
		$apps[$x]['db'][0]['fields'][2]['description'] = '';
		$apps[$x]['db'][0]['fields'][3]['name'] = 'create_user_id';
		$apps[$x]['db'][0]['fields'][3]['type'] = 'integer';
		$apps[$x]['db'][0]['fields'][3]['description'] = '';
		$apps[$x]['db'][0]['fields'][4]['name'] = 'ticket_note';
		$apps[$x]['db'][0]['fields'][4]['type'] = 'text';
		$apps[$x]['db'][0]['fields'][4]['description'] = '';
		$apps[$x]['db'][0]['fields'][5]['name'] = 'file_pointer';
		$apps[$x]['db'][0]['fields'][5]['type']['pgsql'] = 'character varying';
		$apps[$x]['db'][0]['fields'][5]['type']['sqlite'] = 'text';
		$apps[$x]['db'][0]['fields'][5]['type']['mysql'] = 'text';
		$apps[$x]['db'][0]['fields'][5]['description'] = '';

	// CREATE TABLE v_ticket_queue_members 
		$apps[$x]['db'][1]['table'] = 'v_ticket_queue_members';
		$apps[$x]['db'][1]['fields'][0]['name'] = 'queue_member_id';
		$apps[$x]['db'][1]['fields'][0]['type']['pgsql'] = 'serial';
		$apps[$x]['db'][1]['fields'][0]['type']['sqlite'] = 'integer PRIMARY KEY';
		$apps[$x]['db'][1]['fields'][0]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY';
		$apps[$x]['db'][1]['fields'][0]['description'] = '';
		$apps[$x]['db'][1]['fields'][1]['name'] = 'queue_id';
		$apps[$x]['db'][1]['fields'][1]['type'] = 'integer';
		$apps[$x]['db'][1]['fields'][1]['description'] = '';
		$apps[$x]['db'][1]['fields'][2]['name'] = 'user_id';
		$apps[$x]['db'][1]['fields'][2]['type'] = 'integer';
		$apps[$x]['db'][1]['fields'][2]['description'] = '';

	// CREATE TABLE v_ticket_queues 
		$apps[$x]['db'][2]['table'] = 'v_ticket_queues';
		$apps[$x]['db'][2]['fields'][0]['name'] = 'queue_id';
		$apps[$x]['db'][2]['fields'][0]['type']['pgsql'] = 'serial';
		$apps[$x]['db'][2]['fields'][0]['type']['sqlite'] = 'integer PRIMARY KEY';
		$apps[$x]['db'][2]['fields'][0]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY';
		$apps[$x]['db'][2]['fields'][0]['description'] = '';
		$apps[$x]['db'][2]['fields'][1]['name'] = 'queue_name';
		$apps[$x]['db'][2]['fields'][1]['type']['pgsql'] = 'character varying';
		$apps[$x]['db'][2]['fields'][1]['type']['sqlite'] = 'text';
		$apps[$x]['db'][2]['fields'][1]['type']['mysql'] = 'text';
		$apps[$x]['db'][2]['fields'][1]['description'] = '';
		$apps[$x]['db'][2]['fields'][2]['name'] = 'queue_email';
		$apps[$x]['db'][2]['fields'][2]['type']['pgsql'] = 'character varying';
		$apps[$x]['db'][2]['fields'][2]['type']['sqlite'] = 'text';
		$apps[$x]['db'][2]['fields'][2]['type']['mysql'] = 'text';
		$apps[$x]['db'][2]['fields'][2]['description'] = '';
		$apps[$x]['db'][2]['fields'][3]['name'] = 'v_id';
		$apps[$x]['db'][2]['fields'][3]['type'] = 'integer';
		$apps[$x]['db'][2]['fields'][3]['description'] = '';

	// CREATE TABLE v_ticket_statuses 
		$apps[$x]['db'][3]['table'] = 'v_ticket_statuses';
		$apps[$x]['db'][3]['fields'][0]['name'] = 'status_id';
		$apps[$x]['db'][3]['fields'][0]['type']['pgsql'] = 'serial';
		$apps[$x]['db'][3]['fields'][0]['type']['sqlite'] = 'integer PRIMARY KEY';
		$apps[$x]['db'][3]['fields'][0]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY';
		$apps[$x]['db'][3]['fields'][0]['description'] = '';
		$apps[$x]['db'][3]['fields'][1]['name'] = 'status_name';
		$apps[$x]['db'][3]['fields'][1]['type']['pgsql'] = 'character varying';
		$apps[$x]['db'][3]['fields'][1]['type']['sqlite'] = 'text';
		$apps[$x]['db'][3]['fields'][1]['type']['mysql'] = 'text';
		$apps[$x]['db'][3]['fields'][1]['description'] = '';
		$apps[$x]['db'][3]['fields'][2]['name'] = 'v_id';
		$apps[$x]['db'][3]['fields'][2]['type'] = 'integer';
		$apps[$x]['db'][3]['fields'][2]['description'] = '';

	// CREATE TABLE v_tickets 
		$apps[$x]['db'][4]['table'] = 'v_tickets';
		$apps[$x]['db'][4]['fields'][0]['name'] = 'ticket_id';
		$apps[$x]['db'][4]['fields'][0]['type']['pgsql'] = 'serial';
		$apps[$x]['db'][4]['fields'][0]['type']['sqlite'] = 'integer PRIMARY KEY';
		$apps[$x]['db'][4]['fields'][0]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY';
		$apps[$x]['db'][4]['fields'][0]['description'] = '';
		$apps[$x]['db'][4]['fields'][1]['name'] = 'queue_id';
		$apps[$x]['db'][4]['fields'][1]['type'] = 'integer';
		$apps[$x]['db'][4]['fields'][1]['description'] = '';
		$apps[$x]['db'][4]['fields'][2]['name'] = 'v_id';
		$apps[$x]['db'][4]['fields'][2]['type'] = 'integer';
		$apps[$x]['db'][4]['fields'][2]['description'] = '';
		$apps[$x]['db'][4]['fields'][3]['name'] = 'user_id';
		$apps[$x]['db'][4]['fields'][3]['type'] = 'integer';
		$apps[$x]['db'][4]['fields'][3]['description'] = '';
		$apps[$x]['db'][4]['fields'][4]['name'] = 'customer_id';
		$apps[$x]['db'][4]['fields'][4]['type'] = 'integer';
		$apps[$x]['db'][4]['fields'][4]['description'] = '';
		$apps[$x]['db'][4]['fields'][5]['name'] = 'subject';
		$apps[$x]['db'][4]['fields'][5]['type']['pgsql'] = 'character varying';
		$apps[$x]['db'][4]['fields'][5]['type']['sqlite'] = 'text';
		$apps[$x]['db'][4]['fields'][5]['type']['mysql'] = 'text';
		$apps[$x]['db'][4]['fields'][5]['description'] = '';
		$apps[$x]['db'][4]['fields'][6]['name'] = 'create_stamp';
		$apps[$x]['db'][4]['fields'][6]['type']['pgsql'] = 'timestamp with time zone';
		$apps[$x]['db'][4]['fields'][6]['type']['sqlite'] = 'datetime';
		$apps[$x]['db'][4]['fields'][6]['type']['mysql'] = 'timestamp';
		$apps[$x]['db'][4]['fields'][6]['description'] = '';
		$apps[$x]['db'][4]['fields'][7]['name'] = 'create_user_id';
		$apps[$x]['db'][4]['fields'][7]['type'] = 'integer';
		$apps[$x]['db'][4]['fields'][7]['description'] = '';
		$apps[$x]['db'][4]['fields'][8]['name'] = 'ticket_status';
		$apps[$x]['db'][4]['fields'][8]['type'] = 'integer';
		$apps[$x]['db'][4]['fields'][8]['description'] = '';
		$apps[$x]['db'][4]['fields'][9]['name'] = 'last_update_stamp';
		$apps[$x]['db'][4]['fields'][9]['type']['pgsql'] = 'timestamp with time zone';
		$apps[$x]['db'][4]['fields'][9]['type']['sqlite'] = 'datetime';
		$apps[$x]['db'][4]['fields'][9]['type']['mysql'] = 'timestamp';
		$apps[$x]['db'][4]['fields'][9]['description'] = '';
		$apps[$x]['db'][4]['fields'][10]['name'] = 'last_update_user_id';
		$apps[$x]['db'][4]['fields'][10]['type'] = 'integer';
		$apps[$x]['db'][4]['fields'][10]['description'] = '';
		$apps[$x]['db'][4]['fields'][11]['name'] = 'ticket_uuid';
		$apps[$x]['db'][4]['fields'][11]['type']['pgsql'] = 'character varying';
		$apps[$x]['db'][4]['fields'][11]['type']['sqlite'] = 'text';
		$apps[$x]['db'][4]['fields'][11]['type']['mysql'] = 'text';
		$apps[$x]['db'][4]['fields'][11]['description'] = '';
		$apps[$x]['db'][4]['fields'][12]['name'] = 'ticket_number';
		$apps[$x]['db'][4]['fields'][12]['type']['pgsql'] = 'character varying';
		$apps[$x]['db'][4]['fields'][12]['type']['sqlite'] = 'text';
		$apps[$x]['db'][4]['fields'][12]['type']['mysql'] = 'text';
		$apps[$x]['db'][4]['fields'][12]['description'] = '';
		$apps[$x]['db'][4]['fields'][13]['name'] = 'customer_ticket_number';
		$apps[$x]['db'][4]['fields'][13]['type']['pgsql'] = 'character varying';
		$apps[$x]['db'][4]['fields'][13]['type']['sqlite'] = 'text';
		$apps[$x]['db'][4]['fields'][13]['type']['mysql'] = 'text';
		$apps[$x]['db'][4]['fields'][13]['description'] = '';
		$apps[$x]['db'][4]['fields'][14]['name'] = 'ticket_owner';
		$apps[$x]['db'][4]['fields'][14]['type'] = 'integer';
		$apps[$x]['db'][4]['fields'][14]['description'] = '';

?>
