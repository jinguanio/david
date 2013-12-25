<?php
$db = mysql_connect('localhost:/usr/local/eyou/mail/run/em_mysql.sock', 'root' ,'');
mysql_select_db("test");
$a = "\x91\x5c";//"慭"的gbk编码, 低字节为5c, 也就是ascii中的"\"
//$a = "\xE6\xB5\xAD";//"慭"的gbk编码, 低字节为5c, 也就是ascii中的"\"

var_dump(addslashes($a));
var_dump(mysql_real_escape_string($a, $db));

mysql_query("set names gbk");
var_dump(mysql_real_escape_string($a, $db));

mysql_set_charset("gbk");
var_dump(mysql_real_escape_string($a, $db));

