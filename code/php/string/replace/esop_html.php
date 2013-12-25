<?php
// 替换运维模板

$path = '/home/libo/git/eagleeye/src/web/tpl/';
$files = [
    'hardware_cpu.html',
    'hardware_disk.html',
    'hardware_mem.html',
    'hardware_network.html',
    'hardware_other.html',
    'hardware_overview.html',
    'hardware_raid.html',
    ];

$maps = [
    'js' => 'js',
    'css' => 'css',
    'assets' => 'assets',
    'img' => 'images',
    ];
foreach ($files as $f) {
    $cont = file_get_contents($path.$f);
    foreach ($maps as $search => $replace) {
        $cont = str_replace(['"'.$search.'/', '\''.$search.'/'], ['"{{$URL_TPL_PUBLIC}}'.$replace.'/', '\'{{$URL_TPL_PUBLIC}}'.$replace.'/'], $cont);
    }
    file_put_contents($path.$f, $cont);
}

