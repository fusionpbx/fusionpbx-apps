***********
SessionTalk
***********

SessionTalk's CloudPhone is a softphone available for iOS and Android devices with PUSH notification available. This app is designed to work with their cloud provisioning services. 

Prerequisites
^^^^^^^^^^^^^^

* Working install of FusionPBX
* Customer Domains in the format of subdomain.example.com
* Create an App at https://cloud.sessiontalk.co.uk/
* Use "Username, Password & Subdomain" for the Login Fields
* Provider Name Noted
* domain name set to "example.com" where "subdomain.example.com" is the customer domain.
* Each domain will need SRV records to specify the SIP port or you should set the port in the domain name like "example.com:5070", the API does not set the SIP Port for the app.
* External Provisioning URL: https://mydomain.com/app/sessiontalk/provision.php
* Set everything else up according to your need/wants


Install Steps
^^^^^^^^^^^^^^

On your server

::

  cd /usr/src
  git clone https://github.com/fusionpbx/fusionpbx-apps
  Move the directory 'sessiontalk' into your main FusionPBX directory
  mv fusionpbx-apps/sessiontalk /var/www/fusionpbx/app
  chown -R www-data:www-data /var/www/fusionpbx/app/sessiontalk

::

 Log into the FusionPBX webpage
 Advanced -> Upgrade
 Schema Defaults
 App Defaults
 Menu Defaults and Permission Defaults.
 Log out and back in.


Settings
^^^^^^^^^^^^^^^
 
::

 Mandatory: 
 Set your Sessiontalk provider ID in Default Settings
 Advanced -> Default Settings
 Provision -> sessiontalk_provider_id
 Leave this field blank or disabled for White Label apps.

::

Optional: 

::

 sessiontalk_max_activations - How many apps can be activated per extension. It counts activations as the number of device lines assigned to any device with the vendor "sessiontalk"
 sessiontalk_qr_expiration - set how long a generated QR code is valid for activating an app in seconds.
 sessiontalk_transport - only set if you want to force all SessionTalk apps to use this instead of the device line setting

* set how long a generated QR code is valid for activating an app in seconds.

::


Permissions
^^^^^^^^^^^^^^^^^

::

 sessiontalk_view 


* Users can access QR Codes for extensions they have assigned

::

 sessiontalk_view_all 

* Administrators that you want to give access to QR Codes for any extension on the domain.

Cloud Configuration
^^^^^^^^^^^^^^^^^^^^^
Sign in to your https://cloud.sessiontalk.co.uk account and update various settings on your app.

* Provisioning:
* Set Login Fields to "Username, Passowrd, and Subdomain"
* Set the domain to "example.com" where "customer1.example.com" is the customer sudomains format.
* Set the Incoming Calls mode to "Push" for most use cases.
* Select your Region (I have had problems with the Auto Region setting)
* Still under Provision go to the Misc tab and enable "Use Subdomains"
* Additional:
* Enable "External Provisioning" and set the URL to https://example.com/app/sessiontalk/provision.php (I use provision.example.com as the domain here so my *.example.com wildcard certificate is valid)

All other settings can be set to your preference. Their cloud service makes a request the provision.php file with the details that are in the QR code, and provision.php returns the SIP credentials for the mobile app to use. An additional note about the app, we cannot pass the SIP Port number to the mobile app in this way. If you are using a port other than 5060, you must either use SRV records independently created for each customer subdomain (SRV doesn't work with wildcards), configure a SIP Proxy address that has the SRV records configured for it, or set the domain to include the port like "example.com:5070" on the main page in provisioning.


Usage
^^^^^^^^^^^^^^^^
Navigate to Applications>Sessiontalk and select the extension you wish to activate.

The app generates a single activation QR Code for the selected extension. The QR Code is good for a single activation of the SessionCloud or the White Label version of the app. Re-Using the same QR before it expires will automatically de-activate any app that may already have been activated with this QR Code.

Install the SessionCloud (or your company's rebranded version) app and scan the QR Code from the app login screen.

The Apps>Devices page is used to track the activated devices. You can de-activate or edit a device's line settings including adding additional lines that will show up as additional accounts the next time the mobile device updates.


Activation Rules
^^^^^^^^^^^^^^^^^
* New App Installation with Fresh QR Code: App Activates and creates a Device to store the settings and unique identifier.
* New App with Previously Used QR Code: If the QR Code hasn't expired yet, Update the previous Device that was created with this QR Code. If the existing app tries to re-provision, it will log out automatically.
* New App with Expired QR Code: Activation denied.
* Existing App with Fresh QR Code: Deletes the existing Device and recreates with new QR code id and fresh line settings
* Existing App with Previously Used QR Code: If this pair of apps and QR codes were used together in the past, it will activate as normal. If these 2 are both present but weren't used together, it will delete both devices and create a new one.
* Deleted Devices will De-Activate themselves, but if the end user still has a valid QR code they can re-activate until the QR has expired.
* Disabled Devices will not de-activate the app, but it will prevent any settings changes to the mobile app until device is re-enabled, including line password updates.
