<?php

function lg($msg)
{
    $msg = date('c') . " $msg\n";
    file_put_contents('/tmp/b', $msg, FILE_APPEND | LOCK_EX);
}

class Server
{
    function run()
    {
        $serv = new swoole_server("127.0.0.1", 8002);
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
        // 关闭会导致连接的客户端报错
    }

    function onReceive($serv, $fd, $from_id, $data)
    {
        //lg($data);
        echo $data . "\n";
        $serv->send($fd, $data);
    }
}

$serv = new Server();
$serv->run();

