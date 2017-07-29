<?php
	
	//application details
		$apps[$x]['name'] = "Domain Counts";
		$apps[$x]['uuid'] = "21df0a64-8665-4c7c-839f-2bc4663f9651";
		$apps[$x]['category'] = "System";
		$apps[$x]['subcategory'] = "";
		$apps[$x]['version'] = "";
		$apps[$x]['license'] = "Mozilla Public License 1.1";
		$apps[$x]['url'] = "http://www.fusionpbx.com";
		$apps[$x]['description']['en-us'] = "Wizard";
		
		
	//permission details
		$y = 0;
		$apps[$x]['permissions'][$y]['name'] = "domain_counts_view";
		$apps[$x]['permissions'][$y]['menu']['uuid'] = "8db32ec2-85dc-4782-a7b1-d0caf8a4e44e";
		$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
		$apps[$x]['permissions'][$y]['groups'][] = "admin";
		$y++;		
		$apps[$x]['permissions'][$y]['name'] = "domain_counts_view_domain";
		$apps[$x]['permissions'][$y]['groups'][] = "admin";
		$y++;
		$apps[$x]['permissions'][$y]['name'] = "domain_counts_view_all";
		$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
		$y++;			
		
