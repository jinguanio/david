<?php

function _e($msg, $line)
{
    echo "$msg, line: $line\n";
}

class ProxyServer
{
    function run()
    {
        $serv = new swoole_server("127.0.0.1", 8001);
        $serv->set(array(
            'timeout' => 1, //select and epoll_wait timeout.
            'poll_thread_num' => 1, //reactor thread num
            'worker_num' => 1, //reactor thread num
            'backlog' => 128, //listen backlog
            'max_conn' => 10000,
            'dispatch_mode' => 2,
            //'open_tcp_keepalive' => 1,
            //'log_file' => '/tmp/swoole.log', //swoole error log
        ));

        $serv->on('Receive', array($this, 'onReceive'));
        $serv->on('Close', array($this, 'onClose'));
        //swoole_server_addtimer($serv, 2);
        #swoole_server_addtimer($serv, 10);
        $serv->start();
    }

    function onClose($serv, $fd, $from_id)
    {
        //$serv->close($fd);
    }

    function onReceive($serv, $fd, $from_id, $data)
    {
        $socket = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
       
        $socket->on('connect', function (swoole_client $socket) use ($data) {
            $socket->send($data);
        });
        $socket->on('error', function (swoole_client $socket) use ($serv, $fd) {
            _e("connect to backend server fail", __LINE__);
            $serv->send($fd, "backend server not connected. please try reconnect.");
            $socket->close();
        });

        $socket->on('close', function (swoole_client $socket) use ($serv, $fd) {
            $serv->close($fd);
        });

        $socket->on('receive', function (swoole_client $socket, $data) use ($serv, $from_id, $fd) {
            $serv->send($fd, $data, $from_id);
            $socket->close();
        });
        
        $socket->connect('127.0.0.1', 8002, 0.2);
    }
}

$serv = new ProxyServer();
$serv->run();

