<?php
error_reporting(E_ALL);

$path = '/home/libo/my/git/elephant_cli/src/implements/process/emimp_process_agent_client.class.php';
$cont = file_get_contents($path);
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

$pat_sk = "/require_once (.*)/i";
preg_match_all($pat_sk, $cont, $match);
print_r(array_unique($match[1]));
exit;
foreach ($search['key'] as $sk) {
    if (preg_match($pat_sk, $cont, $match)) {
        var_dump($match);
        echo PHP_EOL;
        foreach ($exclude['key'] as $ek) {
            if (!preg_match($ek, $match[1])) {
                $m = $match[1];
                foreach ($replace as $s => $r) {
                    $m = preg_replace($s, $r, $m);
                }
                $ret[$path][] = trim($m);
            }
        }
    }
}

