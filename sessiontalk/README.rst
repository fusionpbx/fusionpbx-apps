***********
SessionTalk
***********

SessionTalk's CloudPhone is a softphone available for iOS and Android devices with PUSH notification available. This app is designed to work with their cloud provisioning services. 

Prerequisites
^^^^^^^^^^^^^^

* Working install of FusionPBX
* Customer Domains in the format of subdomain.example.com
* Create an App at https://cloud.sessiontalk.co.uk/ and apply the following settings. The rest of the settings are to your preference/requirements
* In Provisioning, Use "Username, Password & Subdomain" for the Login Fields
* Domain set to example.com where your customers are "subdomain.example.com"
* Set the Incoming Calls mode to "Push" for most use cases.
* Select your Region (I have had problems with the Auto Region setting)
* Under the Additional tab, enable "Use Subdomains"
* Under Misc, Enable Provisioning and set the External Provisioning URL: https://mydomain.com/app/sessiontalk/provision.php
* Optional if using a port other than 5060: You can set the port in the domain name field like "example.com:5070", or use SRV records for more flexibility. Each full customer domain will need SRV records to specify the SIP port or the API does not set the SIP Port for the app during provisioning. Remember that SRV records don't support wildcard, so even if you are using *.example.com for your DNS, you need to use the full _sip._tcp.customer.example.com for an individual SRV record per customer.


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


Usage
^^^^^^^^^^^^^^^^
Navigate to Applications>Sessiontalk and select the extension you wish to activate.

The app generates a single activation QR Code for the selected extension. The QR Code is good for a single activation of the SessionCloud or the White Label version of the app. Re-Using the same QR before it expires will automatically de-activate any app that may already have been activated with this QR Code.

If enabled, it also generates a Windows Softphone Link that uses the same key as the visible QR code, so if you use the Windows softphone link to activate the Windows App, the QR code is considered "used". To activate a mobile app and a softphone, you will need to make sure that the sessiontalk_max_activations is not limited to 1, and that you refresh the page before activating the second device.

Install the SessionCloud (or your company's whitelabel version) app and scan the QR Code from the app login screen.

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
