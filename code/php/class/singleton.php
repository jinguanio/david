<?php
class Test
{
    protected static $__new = null;
    protected $__name = '';

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

            self::$__new->__name = 'libo';
            self::$__new->_helo();
            //$this->_helo(); // error
        }

        return self::$__new; 
    }
    
    public function helo()
    {
        $this->__name = 'bnn'; // right

        echo __METHOD__ . "\n";
        echo $this->__name . PHP_EOL;
        echo "\n";
    }

    protected function _helo()
    {
        echo __METHOD__ . "\n";
        echo "\n";
    }
}

$t = Test::instance();
$t->helo();

$t = Test::instance();
$t->helo();

