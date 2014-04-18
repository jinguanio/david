<?php
error_reporting(E_ALL);

$cmd = 'sudo -u eyou /usr/local/eyou/toolmail/opt/bin/php /home/libo/git/eagleeye/src/app/lib/monitor/operator/test/test_em_monitor_property_operator_user.php';
//$cmd = 'sudo -u eyou /usr/local/eyou/toolmail/opt/bin/php /home/libo/git/elephant_tk/src/lib/api/action/test/test_em_httpapi_action_api_user.class.php';
$i = 1;
do {
    sleep(5*$i);

    $out = [];
    $start = microtime(true);
    echo "start: $start\n";
    exec($cmd, $out, $ret);
    $out = implode(' ', $out);
    $eclipse = microtime(true) - $start;

    echo "eclipse: $eclipse\n";
    echo "result: $out\n";
    echo "interval: " . (5*$i) . "s\n";
    if (false !== strpos($out, '2006 MySQL server has gone away')) {
        break;
    }

    ++$i;
    echo "\n";

} while (true);

