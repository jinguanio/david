<?php


$cmd = 'sudo -u eyou /usr/local/eyou/mail/app/sbin/em_fork_exec "nohup /usr/local/eyou/mail/app/bin/em_phpd 2>&1 | sudo  /usr/local/eyou/mail/opt/sbin/cronolog /usr/local/eyou/mail/log/stdout/archive/%Y%m/em_phpd_%Y%m%d.out &"';

exec($cmd, $rev, $status);
print_r($rev);

