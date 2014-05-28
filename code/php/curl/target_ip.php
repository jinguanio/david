<?php
function get_ip()
{
    $real_ip = 'error';

    if (isset($_SERVER)) {
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $real_ip = '$_SERVER["HTTP_X_FORWARDED_FOR"]: ' . $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $real_ip = '$_SERVER["HTTP_CLIENT_IP"]: ' . $_SERVER["HTTP_CLIENT_IP"];
        } else {
            $real_ip = '$_SERVER["REMOTE_ADDR"]: ' . $_SERVER["REMOTE_ADDR"];
        }
    } else {
        if (getenv("HTTP_X_FORWARDED_FOR")) {
            $real_ip = 'getenv("HTTP_X_FORWARDED_FOR"): ' . getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("HTTP_CLIENT_IP")) {
            $real_ip = 'getenv("HTTP_CLIENT_IP"): ' . getenv("HTTP_CLIENT_IP");
        } else {
            $real_ip = 'getenv("REMOTE_ADDR"): ' . getenv("REMOTE_ADDR");
        }
    }

    return  $real_ip;
}

function get_ip_2($strict = true)
{
    $remote_ip = 'error';

    if ($strict) {
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_REAL_IP'])) {
                $remote_ip = '$_SERVER[\'HTTP_X_REAL_IP\']:' . $_SERVER['HTTP_X_REAL_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $remote_ip = '$_SERVER[\'REMOTE_ADDR\']: ' . $_SERVER['REMOTE_ADDR'];
            }
        } else {
            if (getenv('HTTP_X_REAL_IP')) {
                $remote_ip = 'getenv(\'HTTP_X_REAL_IP\'): ' . getenv('HTTP_X_REAL_IP');
            } elseif (getenv('REMOTE_ADDR')) {
                $remote_ip = 'getenv(\'REMOTE_ADDR\'): ' . getenv('REMOTE_ADDR');
            }
        }
    } else {
        if (isset($_SERVER)) {
            $remote_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        } else {
            $remote_ip = getenv('REMOTE_ADDR') ? getenv('REMOTE_ADDR') : '';
        }
    }

    return $remote_ip;
}

echo "============= get_ip() ===============\n";
echo get_ip();
echo "\n\n";

echo "============= get_ip_2() ===============\n";
echo get_ip_2();



