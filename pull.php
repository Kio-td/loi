<?php
echo shell_exec("cd /var/www/loi && /usr/bin/git pull 2>&1");
echo shell_exec("cd /var/www/loi && rm -rf assets");
echo shell_exec("cd /home/loi/loi && /usr/bin/git pull 2>&1");
echo shell_exec("cd /home/loi/loi && /usr/bin/npm install");
echo shell_exec("cd /home/loi/loi && /usr/bin/npm update");
