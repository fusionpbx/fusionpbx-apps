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
  cd /var/www/fusionpbx/resources/templates/provision/
  mkdir sessiontalk sessiontalk/windows sessiontalk/android sessiontalk/ios
  chown -R www-data:www-data /var/www/fusionpbx/app/sessiontalk /var/www/fusionpbx/resources/templates/provision/sessiontalk


::

 Log into the FusionPBX webpage
 Advanced -> Upgrade
 Schema Defaults
 App Defaults
 Menu Defaults and Permission Defaults.
 Log out and back in.


Settings
^^^^^^^^^^^^^^^

Set up your default settings in Advanced>Default Settings or per domain in the domain settings.

+-------------------+----------+-------------------------------------------------------------------------------------------------------+
|      Setting      | Default  |                                              Description                                              |
+===================+==========+=======================================================================================================+
| provider_id       |          | Sessioncloud Provider ID (Blank for white label)                                                      |
+-------------------+----------+-------------------------------------------------------------------------------------------------------+
| max_activations   | 1        | Maximum apps per extension counted by assigned lines                                                  |
+-------------------+----------+-------------------------------------------------------------------------------------------------------+
| qr_expiration     | 172800   | How long a QR code is valid in seconds. Default is 3 days.                                            |
+-------------------+----------+-------------------------------------------------------------------------------------------------------+
| transport         | udp      | Default transport for newly activated devices. Can be changed by editing the device after activation. |
+-------------------+----------+-------------------------------------------------------------------------------------------------------+
| key_rotation      | 2492000  | Encryption key rotation - 1 month.                                                                    |
|                   |          | Only change if you need QR Codes longer than 1 month, this is the upper bound of the qr_expiration.   |
+-------------------+----------+-------------------------------------------------------------------------------------------------------+
| windows_softphone | true     | Enable the windows software installation link                                                         |
+-------------------+----------+-------------------------------------------------------------------------------------------------------+
| srtp              | Disabled | Enabled or Disabled for srtp support                                                                  |
+-------------------+----------+-------------------------------------------------------------------------------------------------------+
| video             | Disabled | Enabled or Disabled for video calling support                                                         |
+-------------------+----------+-------------------------------------------------------------------------------------------------------+
| callrecording     | Disabled | Enabled or Disabled to allow users to record calls in the app                                         |
+-------------------+----------+-------------------------------------------------------------------------------------------------------+

Permissions
^^^^^^^^^^^^^^^^^

+----------------------+------------------+----------------------------------------------------------------------------------+
|      Permission      |  Default Groups  |                                   Descriptions                                   |
+======================+==================+==================================================================================+
| sessiontalk_view     | user             | User can generate QR codes only for extensions where their user is assigned      |
+----------------------+------------------+----------------------------------------------------------------------------------+
| sessiontalk_view_all | superadmin,admin | Admin's can generate QR codes for any extension in a domain.                     |
+----------------------+------------------+----------------------------------------------------------------------------------+

Usage
^^^^^^^^^^^^^^^^
Navigate to Applications>Sessiontalk and select the extension you wish to activate.

**Mobile App**

Install from the Apple or Google app store, then click the Scan QR button in the app (not your phone's QR scanner). Scan the QR code and the app will automatically activate. Each QR is good for one activation. If you activate the wrong device, you can "move" the activation by scanning the QR code on the new device and it will deactivate the original device. Once the QR code has expired (default 3 days from creation) it is locked to that device.

**Windows App**

If enabled, there is a Windows Softphone link that you can click that will automatically install the app and activate it. You must uninstall any existing version of the app including previously activated installations. If you activate the wrong device, you can "move" the activation by clicking the same link on the new device and it will deactivate the original device. Once the link has expired (default 3 days from creation) it is locked to that device.

**Admins**

* You can see activated apps by going to Accounts>Devices and searching for "Sessiontalk". An activated app auto creates a device. The template name tells you the type of app that was activated windows/android/ios. 
* To deactivate a user's app, simply delete the device.
* To add multiple accounts to a user's app, you can add extensions to the device the same way you would for a desk phone and have the user close and open the app to update.
* If you disable the device, the app will fail to update settings if they change, but it won't deactivate.
* Troubleshooting tip: If you want the user's account/accounts to be "Recreated" on the app, delete the value for the json_md5 setting on the device's page for that app. Next update will force accounts to "Recreate"

Activation Rules
^^^^^^^^^^^^^^^^^
* New App Installation with Fresh QR Code: App Activates and creates a Device to store the settings and unique identifier.
* New App with Previously Used QR Code: If the QR Code hasn't expired yet, Update the previous Device that was created with this QR Code. If the existing app tries to re-provision, it will log out automatically.
* New App with Expired QR Code: Activation denied.
* Existing App with Fresh QR Code: Deletes the existing Device and recreates with new QR code id and fresh line settings
* Existing App with Previously Used QR Code: If this pair of apps and QR codes were used together in the past, it will activate as normal. If these 2 are both present but weren't used together, it will delete both devices and create a new one.
* Deleted Devices will De-Activate themselves, but if the end user still has a valid QR code they can re-activate until the QR has expired.
* Disabled Devices will not de-activate the app, but it will prevent any settings changes to the mobile app until device is re-enabled, including line password updates.


BONUS
^^^^^^
If you want to be able to point the sessiontalk cloud external provisioning URL to be the same as the phones (https://pbx.example.com/app/provision) you can put this at the beginning of the app/provision/index.php file (After the opening comment block). I figured this out when I accidentally put the wrong URL in my cloud config for sessiontalk and didn't want to wait until they approved the correction to be able to test.

::

 // Use the sessiontalk app if it exists and the URL args match
 if (strlen($_REQUEST['deviceId']) > 0 && file_exists('/var/www/fusionpbx/app/sessiontalk')) {
 	 require_once "/var/www/fusionpbx/app/sessiontalk/provision.php";
	 exit;
 }