*********
Bulk Import Extensions/Voicemails/Devices
*********

Bulk import extensions with voicemails and devices to your server.
Just upload CSV file from your computer to FusionPBX server and select fields you want
to populate from this file. Other this app will do for you.
This app version is working with FusionPBX 4.4 branch.


Prerequisites
^^^^^^^^^^^^^^

* Working install of FusionPBX
* CSV file with your extensions
* Bit of luck

Install Steps
^^^^^^^^^^^^^^

On your server

::

  cd /usr/src
  git clone https://github.com/fusionpbx/fusionpbx-apps
  Move the directory 'bulk_import_extensions' into your main FusionPBX directory
  mv fusionpbx-apps/bulk_import_extensions /var/www/fusionpbx/app
  chown -R www-data:www-data /var/www/fusionpbx/app/bulk_import_extensions

::

 Log into the FusionPBX webpage
 Advanced -> Upgrade
 Menu Defaults and Permission Defaults.
 Log out and back in.
