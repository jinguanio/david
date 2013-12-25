<?php
class Test
{
    protected static $__new = null;

    protected function __construct()
    {
    }

    protected static function lg($msg, $line)
    {
        echo "[DEBUG] $msg, line: $line\n";
    }

    public static function instance()
    {
        if (!self::$__new) {
            self::lg('create new object', __LINE__);
            self::$__new = new self();
        }

        return self::$__new; 
    }
    
    public function helo()
    {
        echo "hello david!\n\n";
    }
}

$t = Test::instance();
$t->helo();

$t = Test::instance();
$t->helo();

