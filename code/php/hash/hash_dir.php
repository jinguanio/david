<?php
function hash_dir($key, $level=3)
{
    $hash_str = hash('sha1', $key);
    $hash_str = hash('crc32b', $key);
    echo 'hash value: ' . $hash_str . "\n";
    $hash_dir = array();
    for($i=0; $i<$level; $i++)
    {
        $hash_dir[] = substr($hash_str, $i, 1);
    }
    return implode(DIRECTORY_SEPARATOR, $hash_dir);
}

$dir = hash_dir('libo');
print_r($dir);
echo PHP_EOL . PHP_EOL;

$dir = hash_dir('bnn');
print_r($dir);
echo PHP_EOL . PHP_EOL;

$dir = hash_dir('lipengfei');
print_r($dir);
echo PHP_EOL . PHP_EOL;

$dir = hash_dir('lvmin');
print_r($dir);
echo PHP_EOL . PHP_EOL;

