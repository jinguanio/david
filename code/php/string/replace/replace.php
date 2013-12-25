<?php
$path = '/home/libo/git/eagleeye/src/web/tpl/user';
$files = glob($path . "/*.html");
foreach ($files as $f) {
    $nf = str_replace('moni_', '', $f);
    rename($f, $nf);
}
