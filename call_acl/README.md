# Install HOW TO
1. ```cd /usr/src/```
2. ```git clone https://github.com/fusionpbx/fusionpbx-apps```
3. ```cd fusionpbx-apps/```
4. ```cp -R call_acl /var/www/fusionpbx/app/```
5. ```cp -R call_acl/resources/install/scripts/app/call_acl /usr/share/freeswitch/scripts/app/```
6. ```cp call_acl/templates/conf/dialplan/041_call_acl.xml /var/www/fusionpbx/app/dialplans/resources/switch/conf/dialplan/```
7. Go to GUI
8. Upgrades -> SCHEMA; APP DEFAULTS; MENU DEFAULTS; PERMISSION DEFAULTS
9. Log out and back in
10. **Dialplan** -> **Dialplan Manager**
11. You will find new dialplan entry **call_acl**, order **041**, to enable Call ACL need to turn it to Enabled.
12. **Apps** -> **Call ACL**.

Allow and Reject!

Note, by default call_acl in dialplan is turned off. It's done cause it's used regular expressions and it's not needed on every domain