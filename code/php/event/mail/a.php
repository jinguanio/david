<?php
error_reporting(E_ALL);

$curr_stat = -1;
$sub_stat = [];
$stat = [
    'STAT_READY' => 1,
    'STAT_AUTH' => 2,
    'STAT_USER' => 3,
    'STAT_PASSWD' => 4,
    'STAT_FROM' => 5,
    'STAT_TO' => 6,
    'STAT_DATA' => 7,
    'STAT_MAIL' => 8,
    'STAT_QUIT' => 9,
    'STAT_END' => 10,
    'STAT_INIT' => -1,
    ];
$CRLF = "\r\n";
$timeout = 3;
$resp = '';
$args = [
    'host' => "mail.eyou.net:465",
    'passwd' => "eYouGaoJing!nb",
    'email' => "monitor_alert@eyou.net",

    //'host' => "smtp.126.com:25",
    //'passwd' => "aaaaa123",
    //'email' => "eyoudemo@126.com",

    //'host' => "smtp.126.com:465",
    //'passwd' => "aaaaa123",
    //'email' => "eyoudemo@126.com",
    ];

$fd = stream_socket_client($args['host'], $errno, $errstr, 3, STREAM_CLIENT_CONNECT | STREAM_CLIENT_ASYNC_CONNECT);
$opt = EventBufferEvent::OPT_CLOSE_ON_FREE | EventBufferEvent::OPT_DEFER_CALLBACKS;
$base = new EventBase();
if (false !== strpos($args['host'], '465')) {
    $ctx = new EventSslContext(EventSslContext::SSLv3_CLIENT_METHOD, []);
    $bev = EventBufferEvent::sslSocket($base, $fd, $ctx, EventBufferEvent::SSL_CONNECTING, $opt);
} else {
    $bev = new EventBufferEvent($base, $fd, $opt);
}

$bev->setCallbacks('readcb', null, null, $base);
$bev->enable(Event::READ|Event::WRITE);
$bev->setTimeouts($timeout, $timeout);
$base->dispatch();

$i = 0;

function readcb($bev, $base)
{
    global $curr_stat, $stat, $sub_stat, $resp, $args;
    global $i;

    $resp = '';
    while(null !== ($msg = $bev->read(512))) {
        $resp .= $msg;
    }
    $resp = trim($resp);
    _lg("r: {$resp}".PHP_EOL);

    if (++$i > 2) {
        //exit;
    }

    switch (true) {
    case (check_resp(220)):
        $curr_stat = $stat['STAT_READY'];
        $req = "EHLO eyou.net";
        write_bev($bev, $req);
        break;

    case (check_resp(334)):
        list(,$resp) = explode(' ', trim($resp));

        $resp = strtolower(base64_decode($resp));
        switch (true) {
        case (false !== strpos($resp, 'username')):
            $curr_stat = $stat['STAT_USER'];
            $req = base64_encode($args['email']);
            write_bev($bev, $req);
            break;

        case (false !== strpos($resp, 'password')):
            $curr_stat = $stat['STAT_PASSWD'];
            $req = base64_encode($args['passwd']);
            write_bev($bev, $req);
            break;
        }
        break;

    case (check_resp(235)):
        $curr_stat = $stat['STAT_FROM'];
        $req = "MAIL FROM:<{$args['email']}>";
        write_bev($bev, $req);
        break;

    case (check_resp([ 250, 251 ])):
        if (!isset($sub_stat['auth'])) {
            $curr_stat = $stat['STAT_AUTH'];
            $sub_stat['auth'] = true;
            $req = "AUTH LOGIN";
            write_bev($bev, $req);
        } elseif (!isset($sub_stat['from'])) {
            $curr_stat = $stat['STAT_TO'];
            $sub_stat['from'] = true;
            $req = "RCPT TO:<libo@eyou.net>";
            write_bev($bev, $req);
        } elseif (!isset($sub_stat['to'])) {
            $curr_stat = $stat['STAT_DATA'];
            $sub_stat['to'] = true;
            $req = "DATA";
            write_bev($bev, $req);
        } else {
            $curr_stat = $stat['STAT_QUIT'];
            $sub_stat['quit'] = true;
            $req = "QUIT";
            write_bev($bev, $req);
        }
        break;

    case (check_resp(354)):
        $curr_stat = $stat['STAT_MAIL'];
        $date = date('Y-m-d H:i:s');
        $mail = <<<EMAIL
from:<{$args['email']}>
to:<libo@eyou.net>
subject: hello libo {$date}

test
.

EMAIL;
        $req = "{$mail}";
        write_bev($bev, $req);
        break;

    case (check_resp(221)):
        $curr_stat = $stat['STAT_END'];
        _lg('SMTP quit.');
        return;
        //break;

    default:
        $curr_stat = $stat['STAT_INIT'];
        _lg('unexpect response code. msg: ' . $resp);
        return;
    }
}

function write_bev($bev, $req)
{
    global $curr_stat, $stat, $CRLF;

    if ($stat['STAT_INIT'] !== $curr_stat) {
        _lg("w: {$req}".PHP_EOL.PHP_EOL);
        $bev->write($req . $CRLF);
    }

    $curr_stat = $stat['STAT_INIT'];
}

function check_resp($expect)
{
    global $resp;

    if (empty($expect)) {
        return false;
    }

    foreach ((array) $expect as $st) {
        $code = substr($resp, 0, 3);
        if ($code == $st) {
            return true;
        }
    }

    return false;
}

function _lg($msg)
{
    //return;
    print_r($msg);
}

