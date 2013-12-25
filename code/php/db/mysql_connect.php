<?php
// mysqli
$mysqli = new mysqli('localhost', 'root', null, 'eyou_mail', null, '/usr/local/eyou/mail/run/em_mysql.sock');
$result = $mysqli->query('SELECT \'Hello, dear MySQL user!\' AS _message FROM DUAL');
$row = $result->fetch_assoc();
echo 'mysqli: ', htmlentities($row['_message']), PHP_EOL;

// PDO
$pdo = new PDO('mysql:host=localhost;unix_socket=/usr/local/eyou/mail/run/em_mysql.sock;dbname=eyou_mail', 'root');
$statement = $pdo->query('SELECT \'Hello, dear MySQL user!\' AS _message FROM DUAL');
$row = $statement->fetch(PDO::FETCH_ASSOC);
echo 'PDO: ', htmlentities($row['_message']), PHP_EOL;

// mysql
$c = mysql_connect('localhost:/usr/local/eyou/mail/run/em_mysql.sock', 'root', '');
mysql_select_db('eyou_mail');
$result = mysql_query('SELECT \'Hello, dear MySQL user!\' AS _message FROM DUAL');
$row = mysql_fetch_assoc($result);
echo 'mysql: ', htmlentities($row['_message']), PHP_EOL;

