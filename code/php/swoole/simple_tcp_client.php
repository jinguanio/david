<?php
error_reporting(E_ALL);

require_once __DIR__ . '/../daemon/daemon.php';

dl('swoole.so');

$url = 'localhost'; $port = 8000;
$fork_num = 10;
$max_send_num = 50;
$send_interval = 2;

function send_data()
{
    global $count, $max_send_num, $send_interval;
    global $url, $port;

    $pid = posix_getpid();

    while (1) {
        if ($max_send_num < $count) {
            $count = 1;
            sleep($send_interval);
            continue;
            //exit(0);
        }

        $client = new swoole_client(SWOOLE_TCP, SWOOLE_SOCK_ASYNC);
        $ret = $client->connect($url, $port, 0.5, 1);

        $client->send("HELLO WORLD\n");
        $clients[$client->sock] = $client;

        while(!empty($clients))
        {
            $write = $error = array();
            $read = array_values($clients);
            $n = swoole_client_select($read, $write, $error, 0.6);
            if($n > 0)
            {
                foreach($read as $index=>$c)
                {
                    echo "Recv #{$c->sock}: ".$c->recv()."\n";
                    unset($clients[$c->sock]);
                }
            }
        }

        /*
        $fp = @stream_socket_client("tcp://{$url}:{$port}", $errno, $errstr, 30);
        if (!$fp) {
            exit(1);
        }

        $rand = random();
        $data = time() . "-{$rand}-{$pid}-{$count}";
        fwrite($fp, $data . "\r\n");
        lg("c: $data");
        fgets($fp, 1024);
        fclose($fp);

        $count++;
         */

        usleep(5000);
    }
}

function random()
{
    return md5(microtime(true) . mt_rand(0, 99999999));
}

function lg($msg)
{
    $file = '/tmp/cli';
    file_put_contents($file, $msg . "\n", FILE_APPEND | LOCK_EX);
}

function help()
{
    echo './' . basename(__FILE__) . ' [r $1|k|s|h]' . PHP_EOL;
    echo <<<HELP
    r      run daemon
            $1 进程数
    k      kill daemon
    h      help
HELP;
}

function status()
{
    exec("ps -ef | grep 'php r'", $out);
    print_r(implode("\n", $out)."\n");
}

$daemon = new Daemon([ 'func' => 'send_data' ]);

if (isset($argv[1])) {
    if ('r' === $argv[1]) {
        if (isset($argv[2])) {
            $daemon->set_option('fork_num', $argv[2]);
        } else {
            $daemon->set_option('fork_num', $fork_num);
        }

        $daemon->fork_proc();
    } elseif ('k' === $argv[1]) {
        $daemon->kill_proc();
    } elseif ('s' === $argv[1]) {
        status();
    } else {
        help();
    }
} else {
    help();
}



/*
$serv = new swoole_server("127.0.0.1", 8000);

$serv->set(array(
    'worker_num' => 2,
    //'open_eof_check' => true,
    //'package_eof' => "\r\n",
    'task_worker_num' => 2,
    //'dispatch_mode' => 2,
    'daemonize' => 1,
    //'heartbeat_idle_time' => 5,
    //'heartbeat_check_interval' => 5,
));
function my_onStart($serv)
{
    global $argv;
    swoole_set_process_name("php {$argv[0]}: master");
    echo "MasterPid={$serv->master_pid}|Manager_pid={$serv->manager_pid}\n";
    echo "Server: start.Swoole version is [".SWOOLE_VERSION."]\n";
    //$serv->addtimer(1000);
}

function my_onShutdown($serv)
{
    echo "Server: onShutdown\n";
}

function my_onTimer($serv, $interval)
{
    echo "Server:Timer Call.Interval=$interval\n";
}

function my_onClose($serv, $fd, $from_id)
{
    //echo "Client: fd=$fd is closed.\n";
}

function my_onConnect($serv, $fd, $from_id)
{
    //throw new Exception("hello world");
    //      echo "Client:Connect.\n";
}

function my_onWorkerStart($serv, $worker_id)
{
    global $argv;
    if($worker_id >= $serv->setting['worker_num']) {
        swoole_set_process_name("php {$argv[0]}: task_worker");
    } else {
        swoole_set_process_name("php {$argv[0]}: worker");
    }
    //echo "WorkerStart|MasterPid={$serv->master_pid}|Manager_pid={$serv->manager_pid}|WorkerId=$worker_id\n";
    //$serv->addtimer(500); //500ms
}

function my_onWorkerStop($serv, $worker_id)
{
    echo "WorkerStop[$worker_id]|pid=".posix_getpid().".\n";
}

function my_onReceive(swoole_server $serv, $fd, $from_id, $data)
{
    $cmd = trim($data);
    if($cmd == "reload")
    {
        $serv->reload($serv);
    }
    elseif($cmd == "task")
    {
        $task_id = $serv->task("hello world", 0);
        echo "Dispath AsyncTask: id=$task_id\n";
    }
    elseif($cmd == "taskwait")
    {
        $result = $serv->taskwait("hello world");
        echo "SyncTask: result=$result\n";
    }
    elseif($cmd == "info")
    {
        $info = $serv->connection_info($fd);
        $serv->send($fd, 'Info: '.var_export($info, true).PHP_EOL);
    }
    elseif($cmd == "broadcast")
    {
        $start_fd = 0;
        while(true)
        {
            $conn_list = $serv->connection_list($start_fd, 10);
            if($conn_list === false)
            {
                break;
            }
            $start_fd = end($conn_list);
            foreach($conn_list as $conn)
            {
                if($conn === $fd) continue;
                $serv->send($conn, "hello from $fd\n");
            }
        }
    }
    //这里故意调用一个不存在的函数
    elseif($cmd == "error")
    {
        hello_no_exists();
    }
    elseif($cmd == "shutdown")
    {
        $serv->shutdown();
    }
    else
    {
        $serv->send($fd, 'Swoole: '.$data, $from_id);
        //$serv->close($fd);
    }
    //echo "Client:Data. fd=$fd|from_id=$from_id|data=$data";
    //$serv->deltimer(800);
    //swoole_server_send($serv, $other_fd, "Server: $data", $other_from_id);
}

function my_onTask(swoole_server $serv, $task_id, $from_id, $data)
{
    echo "AsyncTask[PID=".posix_getpid()."]: task_id=$task_id.".PHP_EOL;
    $serv->finish("OK");
}

function my_onFinish(swoole_server $serv, $data)
{
    echo "AsyncTask Finish:Connect.PID=".posix_getpid().PHP_EOL;
}

function my_onWorkerError(swoole_server $serv, $data)
{
    echo "worker abnormal exit. WorkerId=$worker_id|Pid=$worker_pid|ExitCode=$exit_code\n";
}

$serv->on('Start', 'my_onStart');
$serv->on('Connect', 'my_onConnect');
$serv->on('Receive', 'my_onReceive');
$serv->on('Close', 'my_onClose');
$serv->on('Shutdown', 'my_onShutdown');
$serv->on('Timer', 'my_onTimer');
$serv->on('WorkerStart', 'my_onWorkerStart');
$serv->on('WorkerStop', 'my_onWorkerStop');
$serv->on('Task', 'my_onTask');
$serv->on('Finish', 'my_onFinish');
$serv->on('WorkerError', 'my_onWorkerError');
$serv->on('ManagerStart', function($serv) {
    global $argv;
    swoole_set_process_name("php {$argv[0]}: manager");
});
$serv->start();
 */

