<?php
shell_exec("cd /var/www/loi && /usr/bin/git pull 2>&1");
shell_exec("cd /var/www/loi && rm -rf assets");
shell_exec("cd /home/loi/loi && /usr/bin/git pull 2>&1");
shell_exec("cd /home/loi/loi && /usr/bin/npm install");
shell_exec("cd /home/loi/loi && /usr/bin/npm update");
