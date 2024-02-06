*********
SessionTalk
*********

SessionTalk's CloudPhone is a softphone available for iOS and Android devices with PUSH notification available. This app is designed to work with their cloud provisioning services. 

Prerequisites
^^^^^^^^^^^^^^

* Working install of FusionPBX
* Create an App at https://cloud.sessiontalk.co.uk/
* Use "Username, Password & Subdomain" for the Login Fields
* Provider Name (Same as FusionPBX Default Settings)
* External Provisioning URL: https://mydomain.com/app/sessiontalk/provision.php
* Set everything else up according to your needs


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
 Menu Defaults and Permission Defaults.
 Log out and back in.
 
::

 Set your Sessiontalk provider ID in Default Settings
 Advanced -> Default Settings
 Provision :sessiontalk_provider_id
 
::

 Create an API for each user that needs access to the softphone
 Accounts -> Users
 Generate API Key
