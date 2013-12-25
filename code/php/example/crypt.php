<?php
error_reporting(E_ALL);
define('PHP_EXEC_ROOT', true);

require_once 'conf_global.php';
require_once PATH_EYOUM_LIB . 'em_password.class.php';

$pwd = new em_password(em_password::concat_public_label($this->__attributes['password']));
if ($algo) {
    $pwd->set_encode_algo($algo);
}
$this->__attributes['password'] = $pwd->encode();
$this->set_password_encoded(true);
return $this->__attributes['password'];
