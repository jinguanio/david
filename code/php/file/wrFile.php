<?php
# 练习 pack 二进制处理整数
# 练习按照字节读取文件
$file = '/tmp/txt';
$crlf = PHP_EOL;

function write()
{
    global $file, $crlf;

    $array = [];
    for ($i = 0; $i < 1000; $i++) {
        $array[] = str_repeat('=', 1024*100);
    }
    $array = [
        str_repeat('=', 2),
        str_repeat('-', 2),
    ];

    $fp = fopen($file, 'w');
    $total = 0;
    foreach ($array as $v) {
        $len = pack('L', strlen($v) + 2);
        $data = $len . ' ' . $v . $crlf;
        $total += strlen($data);
        fwrite($fp, $data, strlen($data));
    }
    fclose($fp);
    echo 'write total: ', $total, $crlf;
}

function read()
{
    global $file, $crlf;

    $total = 0;
    $fp = fopen($file, 'r');
    #fseek($fp, 4);
    #var_dump(ftell($fp));
    #var_dump(fread($fp, 1));
    #var_dump(ftell($fp));
    #return;

    while (true) {
        $len = fread($fp, 4);
        if (feof($fp)) {
            break;
        }

        $len = unpack('L', $len);
        $total += $len[1]+4;
        $data = trim(fread($fp, $len[1]));
        #var_dump(ftell($fp), $total);
        #var_dump('===========');
        #echo $data, "\n";
    }
    fclose($fp);
    echo 'read total: ', $total, $crlf;
}

function truncate()
{
    global $file, $crlf;

    $fp = fopen($file, 'r+');
    #fseek($fp, 9);
    #var_dump(ftell($fp));
    ftruncate($fp, 8);
    fclose($fp);
    echo 'file size: ', filesize($file), $crlf;
}

function readc()
{
    global $file, $crlf;

    $fp = fopen($file, 'r');
    do {
        $c = fgetc($fp);
        var_dump(ord($c));
    } while(!feof($fp));
}


#$t1 = microtime(true);
write();
#$t2 = microtime(true);
read();
#echo 'write: ', $t2-$t1, "s", "\n";
#echo 'read: ', microtime(true)-$t2, "s", "\n";
truncate();
readc();


