<?php
error_reporting(E_ALL);

$facilities = array(
    LOG_AUTH,
    LOG_AUTHPRIV,
    LOG_CRON,
    LOG_DAEMON,
    LOG_KERN,
    LOG_LOCAL0,
    LOG_LPR,
    LOG_MAIL,
    LOG_NEWS,
    LOG_SYSLOG,
    LOG_USER,
    LOG_UUCP,
);

for ($i = 0; $i < 10; $i++) {
    foreach ($facilities as $facility) {
        openlog('test', LOG_PID, $facility);
        syslog(LOG_ERR, "This is a test: " . memory_get_usage(true));
        closelog();
    }
}

