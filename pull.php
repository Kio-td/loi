<?php

echo shell_exec("cd /var/www/loi && /usr/bin/git pull 2>&1");
echo shell_exec("cd /var/www/loi && /usr/bin/npm install")
echo shell_exec("cd /home/kio_thedev_gmail_com/loi && /usr/bin/git pull 2>&1");
