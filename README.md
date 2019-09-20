# alert_queue_on_php_public

## Set discord.php

Set your DISCORD server's webhookURL as the ALERT_WEBHOOK endpoint.

## Set CRON
```
# alert
* * * * * cd /home/alert_queue_on_php_public; /usr/bin/php ping.php > /home/alert_queue_on_php_public/ping.log 2>&1
*/10 * * * * cd /home/alert_queue_on_php_public; /usr/bin/php push.php > /home/alert_queue_on_php_public/push.log 2>&1
```

## get.php
### When file_get_contents is used and the response code is other than 200, it is loaded in the alert queue.

## ping.php
If you can not check the communication using ping, you can load it into the alert queue.

## push.php
Notify all the information stored in the alert queue.

## queue.php
A library function that packs data into the alert queue.

## queues.json
This file stores alert queues.