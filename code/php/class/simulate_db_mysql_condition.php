<?php
error_reporting(E_ALL);

class db
{
    protected static $__that = null;

    private function __construct() {}
    private function __clone() {}
    private function __sleep() {}
    private function __wakeup() {}
    //private function __destruct() {}

    public static function get_instance()
    {
        if (!self::$__that) {
            self::$__that = new self();
        }

        return self::$__that;
    }

    public function mysql($name, $age)
    {
        //var_dump(func_get_args());
        return new mysql($name, $age);
    }
}

class mysql
{
    protected $__age = 0;
    protected $__name = '';

    function __construct($name = null, $age = null)
    {
        if ($name) {
            $this->__name = $name;
        }

        if ($age) {
            $this->__age = $age;
        }
    }
}

class condition
{
    function change($db)
    {
        return $db->mysql(null, 10);
    }
}

function test()
{
    $db = db::get_instance();
    var_dump($db->mysql('libo', null));

    $th = new condition();
    var_dump($th->change($db));
}

test();

