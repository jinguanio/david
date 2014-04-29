<?php

/**
 * 文件储存类工厂--单件模式 
 * 
 * @package 
 * @version $_EYOUMBR_VERSION_$
 * @copyright $_EYOUMBR_COPYRIGHT_$
 * @author haozhongxiu <haozhongxiu@eyou.net> 
 */
class em_fs
{
    // {{{ members
    
    /**
     *  
     * 每种存储方式的一个单件 
     *  
     * @var array
     */
    protected static  $__fs = array();

    // }}}
    // {{{ functions
    // {{{ public static function factory()

    /**
     * 工厂模式，创建操作各种文件存储方式的对象 
     * 
     * @param string $fs_type  文件存储的方式 分别为 disk|db
     * @param int $delete_mode 文件删除的模式，默认不删除目录，置为1则删除目录
     * @static
     * @access public
     * @return object 操作相应文件存储的对象
     */
    public static function factory($fs_type = 'disk',$delete_mode = 0)
    {
        $class_name = 'em_fs_adapter_' . $fs_type;

        if (!class_exists($class_name)) {
            require_once 'fs/' . $class_name . '.class.php';
        }

        if (!class_exists($class_name)) {
            require_once 'fs/' .$class_name . '.class.php';
            return false;
        }

        return new $class_name($delete_mode);
    }
    // }}}
    // {{{ public static fucntion singleton() 

    /**
     * 由于是单件模式，所以引用单件的时候，$delete_mode参数将会失去意义，也就是说 
     * 只有在创建单件的第一次调用的时候，$delete_mode才有意义 
     * 
     * 
     * @param string $fs_type 文件存储方式
     * @param int $delete_mode 文件删除模式
     * @static
     * @access public
     * @return object 操作相应文件存储方式的对象
     */
    public static function singleton($fs_type = 'disk', $delete_mode = 0)
    {
        //实现每种单件
        if(isset(self::$__fs[$fs_type]) && self::$__fs[$fs_type] instanceof em_fs_adapter_abstract) {
            return self::$__fs[$fs_type];
        }

        self::$__fs[$fs_type] = self::factory($fs_type, $delete_mode);

        //debug
       // echo "test......................\n";
        return self::$__fs[$fs_type];
    }
    // }}}
    // {{{ public static function singleton_clear()

    /**
     * 清空单件，多进程下不能共用一个连接 
     * 
     * @static
     * @access public
     * @return void
     */
    public static function singleton_clear()
    {
        self::$__fs = array();
    }
    // }}}
    // }}}
}
