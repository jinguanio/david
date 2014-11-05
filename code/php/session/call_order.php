<?php
set_time_limit(0);
ini_set('session.save_path', '/tmp');

ob_start();
abstract class Base 
{
    protected $__time = 0;
}

class FileSessionHandler extends Base implements SessionHandlerInterface
{
    private $savePath;

    function open($savePath, $sessionName)
    {
        var_dump('------------' . __METHOD__);
        $this->savePath = $savePath;
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0777);
        }

        return true;
    }

    function close()
    {
        var_dump('------------' . __METHOD__);
        return true;
    }

    function read($id)
    {
        var_dump('------------' . __METHOD__);
        return (string)@file_get_contents("$this->savePath/sess_$id");
    }

    function write($id, $data)
    {
        var_dump('------------' . __METHOD__);
        var_dump(func_get_args());
        $this->__time = date('Ymd H:i:s ss');
        return file_put_contents("$this->savePath/sess_$id", $data) === false ? false : true;
    }

    function destroy($id)
    {
        var_dump('------------' . __METHOD__);
        $file = "$this->savePath/sess_$id";
        if (file_exists($file)) {
            unlink($file);
        }

        return true;
    }

    function gc($maxlifetime)
    {
        var_dump('------------' . __METHOD__);
        foreach (glob("$this->savePath/sess_*") as $file) {
            if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
                unlink($file);
            }
        }

        return true;
    }

    function get_time()
    {
        return $this->__time;
    }
}

var_dump('---------' . '1');
$handler = new FileSessionHandler();
session_set_save_handler($handler, true);

var_dump('---------' . '3');
session_start();

var_dump('---------' . '4');
$_SESSION['name'] = 'phpunit';
// 这个函数会调用 $handle::write() && $handle::close()
// 如果不显示调用 session_write_close(), session 数据会在
// 脚本执行完毕执行 session_write_close()
session_write_close();

var_dump('---------' . '5');
var_export( $_SESSION );

var_dump('---------' . '6');
var_dump($handler->get_time());

var_dump('---------' . '7');
var_dump('session_id:' . session_id());


ob_flush();

