<?php
error_reporting(E_ALL);

$path1 = '/usr/local/eyou/mail/queue/Local/Mess';
$path2 = '/usr/local/eyou/mail/queue/Local/Info';
$loop = 1;

function cal($path, $exclude = ".|..", $recursive = true) 
{
    global $c, $s;
    $path = rtrim($path, "/") . "/";
    $folder_handle = opendir($path);
    $exclude_array = explode("|", $exclude);
    $result = array();

    while(false !== ($filename = readdir($folder_handle))) {
        if(!in_array(strtolower($filename), $exclude_array)) {
            if(is_dir($path . $filename . "/")) {
                if($recursive) {
                    cal($path . $filename . "/", $exclude, true);
                }
            } else {
                $c++;
                $s += filesize($path . $filename);
            }
        }
    }
}

// test 2
echo "Mess\n";
$start = microtime(true);

$c = $s = 0;
for ($i = 0; $i < $loop; $i++) {
    cal($path1);
    var_dump($c, $s);
}

$used = microtime(true) - $start;
echo "time2: {$used} seconds.\n";
echo "=================================\n";
sleep(1);

// test 3
echo "Info\n";
$start = microtime(true);

for ($i = 0; $i < $loop; $i++) {
    $dh = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path2));
    $c = $s = 0;
    foreach ($dh as $file) {
        if ($file->isFile()) {
            $c++;
            $s += $file->getSize();
        }
    }
    var_dump($c, $s);
}

$used = microtime(true) - $start;
echo "time3: {$used} seconds.\n";
echo "=================================\n";
