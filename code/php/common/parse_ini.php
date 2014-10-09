<?php
define('QUOTE', '"');

$aInifile = parse_ini_file('helper/cfg.ini', 'yoursectionhere');
print_r($aInifile);
echo 'test : ' . $aInifile['yoursectionhere']['somevalue'];

