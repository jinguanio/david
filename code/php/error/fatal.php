<?php
//禁止错误输出
error_reporting(E_ALL);
error_reporting(0);

//设置错误处理器
set_error_handler('error');
set_exception_handler('excep');
register_shutdown_function('fatal');

class Test{
    public function index(){
        //这里发生一个警告错误，出发error
        echo $undefinedVarible;
    }
}

function error($errno,$errstr,$errfile,$errline)
{
    $arr = array(
        date('c'),
        $errstr,
        "file: " . $errfile . ",",
        'line: '.$errline,
    );

    echo implode(' ',$arr)."\r\n";
}

function excep(Exception $e)
{
    $arr = [
        date('c'),
        $e->getMessage(),
        "file: " . $e->getFile() . ",",
        "line: " . $e->getLine(),
    ];
    echo implode(' ',$arr)."\r\n";
}


//捕获fatalError
function fatal()
{
    $e = error_get_last();
    switch($e['type']){
    case E_ERROR:
    case E_PARSE:
    case E_CORE_ERROR:
    case E_COMPILE_ERROR:
    case E_USER_ERROR:
        error($e['type'],$e['message'],$e['file'],$e['line']);
        break;        
    }
}

//这里发生一个警告错误,被error 捕获
$test = new Test();
$test->index();

throw new Exception('this is a Exception');

//发生致命错误，脚本停止运行触发 fatal
$test = new Tesdt();
$test->index();


