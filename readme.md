# WebPi
This is a web frontend for the raspberry pi with the aim of using a raspberry pi independently to run tasks which can be offloaded from more powerhungry machines.

## Setting Up the Web Pi
### 1. Clone Repo
- git clone git@github.com:christopher-paul-shaw/WebPi.git && cd ./WebPi
### 2. Install Dependencies
- composer install
### 3. Creating Cron Jobs (Commans for Debian Based Systems)

You will need to enable cronjobs or scheduled tasks to run the scripts in the /cron/ directory, with the following you will need to replace WEBPI with the full url to the directory you have installed WebPi in.

The following example runs the speed test script every 10 mins.
*/10 * * * * cd /WEBPI/cron/ && php speedtest.php

You can also tell the WebPI to used a cached version of the hardware stats by modifying the config.ini and setting [RPI] useCached to 1 and setting up the following cron. 
*/10 * * * * cd /WEBPI/cron/ && php rpi-stats.php

### 4. Configuring WebPi
The file config.ini stores basic configuration, editing values in here will alter the function if scripts within WebPi.
Example being the readOnly flag. Setting this to 1 will prevent anything being changed.

# Default User
- Email: admin@web-pi
- Password: password

# Requirements
- php 7.1 or greater
- php-xml
- php-mbstring
- apache rewrite
