<?php
error_reporting(E_ALL);

$path = '/home/libo/git/admin/src/apps/mailadmin/tpl/ckeditor';

$patt = '_check_os_admin_login';
$replace = '_check_admin_login';

foreach (new DirectoryIterator($path) as $fileinfo) {
    if ($fileinfo->isFile()) {
        $filter = [ 'Makefile', '.' ];
        $file = $fileinfo->getFilename();
        foreach ($filter as $f) {
            if (0 === strpos($file, $f)) {
                continue 2;
            }
        }

        $cont = file_get_contents("$path/$file");
        $cont = str_replace($patt, $replace, $cont);
        file_put_contents("$path/$file", $cont);
        echo "$file [+OK]" . PHP_EOL;
    }
}

