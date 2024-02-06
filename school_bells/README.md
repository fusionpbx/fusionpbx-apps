# School Bells App

Idea of app to give periodic signals to phones or SIP-Speakers with `cron`-style way

---
## Install HOW TO
1. `# cd /usr/src/`
2. `# git clone https://github.com/fusionpbx/fusionpbx-apps`
3. `# cd fusionpbx-apps/`
4. `# cp -R school_bells /var/www/fusionpbx/app/`
5. Add `* * * * * php //var/www/fusionpbx/app/school_bells/school_bells_cron.php` to your *crontab*
6. Go to GUI
7. Upgrades -> *SCHEMA*; *APP DEFAULTS*; *MENU DEFAULTS*; *PERMISSION DEFAULTS*
8. Log out and back in
9. *Apps* -> *School Bells*
---
By initial design, app allows you or to ring extension for a certain amount of time (via Ring Timeout parameter), or playback recoding after extension answers.