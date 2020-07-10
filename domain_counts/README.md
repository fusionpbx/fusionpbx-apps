# Domain Counts

Totals the number of configured extensions, voicemail boxes, ring groups and the like per Domain, with the ability to export all this data to a CSV so you can open it in your favorite spreadsheet software!

![Image of Domain Counts](Domain-Counts-Overview.png)

## Installation
This was tested with FusionPBX 4.5.1 (current master at time of writing) and will likely work on newer or slightly older versions of FusionPBX.

Clone the FusionPBX-Apps repo into the working path after SSHing/Moshing into your server:

```
git clone https://github.com/fusionpbx/fusionpbx-apps.git
cd /var/www/fusionpbx/app
cp -r ~/fusionpbx-apps/domain_counts .
```

Alternative (and preferred) commands to run in SSH session:
```
cd /usr/src 
git clone https://github.com/fusionpbx/fusionpbx-apps 
cp -R /usr/src/fusionpbx-apps/domain_counts /var/www/fusionpbx/app
chown -R www-data:www-data /var/www/fusionpbx/app/domain_counts
```

Then navigate to your FusionPBX installation and choose Advanced => Upgrade. Run upgrades for Menu Defaults and for Permission Defaults.

Log out and back into your FusionPBX installation. 
Go to Status => Domain Counts.
