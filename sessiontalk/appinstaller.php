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
	Copyright (C) 2008-2016 All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
	Michael S <michaelasuiter@gmail.com>
*/

    //generates the sessioncloud.appinstaller file for the ms-appinstaller to install. Needs to be dynamically generated.

	//includes
	require_once "root.php";
	require_once "resources/require.php";
	require_once "resources/functions/functions.php";

    //check if logged in so we don't clobber the user's current session.
    if (!isset($_SESSION['domain_name'])) {
        $domain_name = $_REQUEST['HTTP_HOST'];
        load_defaults($domain_name);
    }
    $windows_softphone_url = $_SESSION['sessiontalk']['windows_softphone_url']['text'];
    $version = $_SESSION['sessiontalk']['windows_softphone_version']['text'];

    //generate the .appinstaller XML
    $xmlstr = "<?xml version='1.0' encoding='UTF-8'?><AppInstaller/>";
    $appinstaller = new SimpleXMLElement($xmlstr);
    $appinstaller->addAttribute('Version', '1.0.0.0');
    $appinstaller->addAttribute('xmlns', "http://schemas.microsoft.com/appx/appinstaller/2017");

    //generate the uri
    $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $url = $protocol . $_SERVER['HTTP_HOST'] . "/app/sessiontalk/?" . $_SESSION['sessiontalk']['windows_softphone_name']['text'] . ".appinstaller";
    $appinstaller->addAttribute('Uri', $url);
    $bundle = $appinstaller->addChild('MainBundle');
    $bundle->addAttribute('Name', "SessionTalkLtd.SessionCloudSoftphone");
    $bundle->addAttribute('Version', $version);
    $bundle->addAttribute('Publisher','CN=SessionTalk Ltd, OU=R&D, O=SessionTalk Ltd, STREET=2 Cariocca Business Park Sawley Road Miles Platting, STREET=Cariocca Business Park, L=Manchester, PostalCode=M40 8BB, C=GB');
    $bundle->addAttribute('Uri', $windows_softphone_url);
    $output = $appinstaller->asXML();

    // $dom = new DOMDocument("1.0");
    // $dom->preserveWhiteSpace = false;
    // $dom->formatOutput = true;
    // $dom->loadXML($appinstaller->asXML());

    //return the file
    Header("Content-Disposition: attachment; filename=".$_SESSION['sessiontalk']['windows_softphone_name']['text'].".appinstaller");
    Header('Content-type: text/xml');
    Header("Content-length: " . strlen($output)); // tells file size
    // print($dom->saveXML());
    echo $output;
?>