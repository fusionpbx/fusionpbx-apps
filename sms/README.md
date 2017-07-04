Install HOW TO


1) cd /usr/src/
2) git clone https://github.com/fusionpbx/fusionpbx-apps
3) cd fusionpbx-apps/; cp -R sms /var/www/fusionpbx/app/
4) cd /var/www/fusionpbx/resources/install/scripts/app/
5) ln -s /var/www/fusionpbx/app/sms/resources/install/scripts/app/sms
6) Go to GUI
7) Upgrades -> APP DEFAULTS; MENU DEFAULTS; PERMISSION DEFAULTS
8) Log out and back in
9) ADV -> Default Settings
10) add CARRIER_access_key and CARRIER_secret_key for whatever carrier you want to use
11) Go to apps -> SMS and add the DID's that you can accept SMS on
DID needs to match exactly what your carrier will be sending you. Just like any other destination
12) Go to Extensions and in the outbound_caller_id_number field add the same DID
Note: Your outbound Caller ID should match the did you placed in App -> SMS DID list. 
13) Add your carriers IPs in an ACL
14) Add your callback URL on your carrier to IE for twillio it would be:
https://YOURDOMAIN/app/sms/hook/sms_hook_twilio.php
You will need to have a valid certificate to use twilio. If you need a certificate, consider using Let’s Encrypt and certbot. It’s fast and free. 


Send and receive.
