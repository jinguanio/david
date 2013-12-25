<?php
error_reporting(E_ALL);

/********************/
/*   ini setting    */
/********************/
define('PATH_BASE', '/home/libo/git/cli/src');

$path = [
    'search' => [
        'etc',
        'implements',
        'inc',
        'shell',
    ],
    'desc' => [
        'lib'
    ],
];

$search = [
    'file' => [
        '/.php$/',
    ],
    'key' => [
        'new',
        'require_once',
        'require',
        'include',
        'include_once',
    ],
];

$exclude = [
    'path' => [
        '/test/',
        '/tpl/',
    ],
    'file' => [
        '/Makefile*/',
        '/^\.$/',
        '/^\.\.$/',
        '/^\.(.*).swp$/',
    ],
    'key' => [
        '/Event*/',
    ],
];

$replace = [
    '/\(.*\)/' => '',
    '/EMBASE_PATH_EYOU_TOOLMAIL_CONF \. /' => '',
    '/PATH_EYOUM_IMPLEMENTS \. /' => '',
];

/********************/
/*      search      */
/********************/

// {{{ function get_dir_list()

function get_dir_list($base)
{
    global $exclude;

    $r = array(); 

    $ritit = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base), RecursiveIteratorIterator::LEAVES_ONLY); 
    foreach ($ritit as $splFileInfo) { 
        // 过滤文件
        $file = $splFileInfo->getFilename();
        foreach ($exclude['file'] as $ef) {
            if (preg_match($ef, $file)) {
                continue 2;
            }
        }

        // 过滤路径
        $path = dirname($splFileInfo->getPathname());
        foreach ($exclude['path'] as $p) {
            if (0 !== preg_match($p, $path)) {
                continue 2;
            }
        }

        $r[$path][] = $file;
    } 

    return $r;
}
//print_r(get_dir_list(PATH_BASE . "/implements"));
//exit;

// }}}
// {{{ function search_files()

function search_files($base)
{
    global $search, $exclude, $replace;

    $file_list = get_dir_list($base);
    $ret = array(); 

    foreach ($file_list as $p => $af) {
        foreach ($af as $f) {
            // 过滤掉不是 php 的文件
            foreach ($search['file'] as $pat) {
                if (!preg_match($pat, $f)) {
                    continue 2;
                }
            }

            $path = "{$p}/{$f}";
            $cont = file_get_contents($path);
            foreach ($search['key'] as $sk) {
                $pat_sk = "/{$sk} (.*)/i";
                if (preg_match_all($pat_sk, $cont, $match_all)) {
                    $match_all = array_unique($match_all[1]);
                    foreach ($match_all as $ma) {
                        foreach ($exclude['key'] as $ek) {
                            if (!preg_match($ek, $ma)) {
                                foreach ($replace as $s => $r) {
                                    $ma = preg_replace($s, $r, $ma);
                                }
                                $ret[$path][] = trim($ma);
                            }
                        }
                    }
                }
            }
        }
    }

    return $ret;
}
print_r(search_files(PATH_BASE . "/implements"));
exit;

// }}}


$r = [];
foreach ($path['desc'] as $path) {
    $desc_list = get_dir_list(PATH_BASE . '/' . $path); 
}

