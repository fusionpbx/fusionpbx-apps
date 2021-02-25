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
	Portions created by the Initial Developer are Copyright (C) 2008-2016
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/

if ($domains_processed == 1) {

    $array['device_vendors'][0]['device_vendor_uuid'] = 'e0d09235-4c1d-423f-8f38-aa477e18362b';
    $array['device_vendors'][0]['name'] = "sessiontalk";
    $array['device_vendors'][0]['enabled'] = 'true';

    $p = new permissions;

    $database = new database;
    $database->app_name = 'sessiontalk';
    $database->app_uuid = '85774108-716c-46cb-a34b-ce80b212bc82';
    $database->save($array);
    unset($array);

    $p->delete('device_vendor_add', 'temp');

}

?>
