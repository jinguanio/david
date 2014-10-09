<?php
error_reporting(0);

class Connection 
{
    public $link;
    private $server, $username, $password, $db;

    public function __construct($server, $username, $password, $db)
    {
        $this->server = $server;
        $this->username = $username;
        $this->password = $password;
        $this->db = $db;
        $this->connect();
    }

    private function connect()
    {
        $this->link = mysql_connect($this->server, $this->username, $this->password);
        mysql_select_db($this->db, $this->link);
    }

    public function __sleep()
    {
        echo 'method __sleep', PHP_EOL;
        return array('server', 'username', 'password', 'db');
    }

    public function __wakeup()
    {
        echo 'method __wakeup', PHP_EOL;
        $this->connect();
    }
}

$ori = new Connection(':/usr/local/eyou/mail/run/em_mysql.sock', 'root', '', 'eyou_mail');
$new = serialize($ori);
echo $new, PHP_EOL;
$back = unserialize($new);

$query = mysql_query('select * from acct_key limit 5', $back->link);
print_r(mysql_fetch_assoc($query));

