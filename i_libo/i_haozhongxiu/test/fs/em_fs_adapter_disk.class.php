<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 *  
 * 
 * @package 
 * @version $_EYOUMBR_VERSION_$
 * @copyright $_EYOUMBR_COPYRIGHT_$
 * @author haozhongxiu <haozhongxiu@eyou.net> 
 */

/**
 *  
 * require
 *
 */
require_once  'em_fs_adapter_abstract.class.php';


/**
 * fs adapter disk 处理类 
 * 
 * @uses em
 * @uses _fs_adapter_abstract
 * @package 
 * @version $_EYOUMBR_VERSION_$
 * @copyright $_EYOUMBR_COPYRIGHT_$
 * @author haozhongxiu <haozhongxiu@eyou.net> 
 */
class em_fs_adapter_disk extends em_fs_adapter_abstract
{

    // {{{ functions
    // {{{ public function __construct() 

    /**
     * 
     * 初始化类，生成一个文件名
     *
     * @param integer $delete_mode 当文件引用次数为0时，删除模式选择（默认是不删除目录的，删除目录置为1）可选 
     * @access public
     * @return object 实例化一个对象
     */
    public function __construct($delete_mode = 0)
    {
        parent::__construct($delete_mode);
    }

    // }}} 

    // {{{ public function save_file()

    /**
     * 保存文件 
     * 
     * @param string $filesrc 将要保存的源文件的路劲 
     * @param string $count   文件引用次数（可选）默认0 
     * @access public
     * @return boolean|string 成功返回文件名，失败返回false
     */
    public function save_file($filesrc, $count = "0")
    {

        //创建一个目录
        $is_create_dir = $this->_create_dir();
        if (!$is_create_dir) {
            return false;
        }
        
        $count_file = $this->file_path($this->__filename) . '.c';
 

        // 获取 文件内容或拷贝文件

        $is_copy = $this->_copy_file($filesrc);
        if (!$is_copy) {
            return false;
        }

        $fp = fopen($count_file, 'x');
        if (!$fp){
            $fp = fopen($count_file, 'w');
            if (!$fp) {
                return false;
            }
        }
                
        if (flock($fp, LOCK_EX)) {
            fwrite($fp, $count);
            flock($fp, LOCK_UN);
            return $this->__filename;
        }
        return false;
    }
    
    // }}}
    // {{{ public function add_count()

    /**
     * 增加引用计数
     * 
     * @param string  $filesrc为格式  时间戳.随机数 
     * @access public
     * @return integer|boolean  成功返回引用个数，失败返回false
     */
    public function add_count($filesrc)
    { 
        $is_count_exists = $this->_count_exists($filesrc);

        if (!$is_count_exists) {
            return false;    
        }

        $count_file = $this->_get_countfile($filesrc);
        $is_file_exists = file_exists($count_file);
        if (!$is_file_exists) {
            return false;
        }

        $file_size = filesize($count_file);
        $fp = fopen($count_file, "r+");
        if (!$fp) {
            return false;
        }
        if (flock($fp, LOCK_EX)) {
            
            //获取文件的引用次数        
            $count_num = intval(fread($fp, $file_size));
            if (false === $count_num ) {
                return false;
            }
            
            $count_num ++;
            fseek($fp,0);
            ftruncate($fp,0);
            
            fwrite($fp, $count_num);
            flock($fp, LOCK_UN);
            fclose($fp);
            return $count_num;
        }
        return false;
    }

    // }}}
    // {{{ public function del_count()

    /**
     * 减少一个引用计数 
     * 
     * @param string $filesrc 文件名
     * @access public
     * @return integer|boolean 成功返回引用个数，失败返回false
     */
    public function del_count($filesrc)
    {
        $is_count_exists = $this->_count_exists($filesrc);
        if (!$is_count_exists) {
            return false;
        }

        $count_file = $this->_get_countfile($filesrc);
        
        //如果读取文件错误
        $is_file_exists = file_exists($count_file);
        if (!$is_file_exists) {
            return false;
        }
        $file_size = filesize($count_file);
        $fp = fopen($count_file, 'r+');

        if (!$fp) {
            return false;
        }
        
        
        if (flock($fp, LOCK_EX)) {
            
            //获取文件的引用次数        
            $count_num = intval(fread($fp, $file_size));
            if (false === $count_num) {
                return false;
            }
            $count_num--;
            if (0 >= $count_num) {

                //删除文件，判断目录是否有其他文件，如果没有的话直接递归删除目录
                $this->_delete_file($filesrc);
                return $count_num;
            }

            fseek($fp,0);
            ftruncate($fp,0);
            fwrite($fp, $count_num);
            flock($fp, LOCK_UN);
            fclose($fp); 
            return $count_num;
        }
        return false;
    }

    // }}}
    // {{{ public funct ion get_count()

    /**
     * 获取一个文件的引用次数
     * 
     * @param string $filesrc 文件名 
     * @access public 
     * @return boolean|integer 成功返回引用个数，失败返回false
     */
    public function get_count($filesrc)
    {   
        $is_count_exists = $this->_count_exists($filesrc);   
        if (!$is_count_exists) {
            return false;       
        }

        $count_file = $this->_get_countfile($filesrc);

        //判断是否真正的返回了引用文件的路径,其实一般读取错误是返回   .c 字符串
        $is_file_exists = file_exists($count_file);
        if (!$is_file_exists) {
            return false;
        }
        $fp = fopen($count_file, 'r');
        if (!$fp) {
            return false;            
        }
        $file_size = filesize($count_file);
        $count_num = fread($fp, 1024);
        fclose($fp);
        return $count_num;
    }

    // }}}

    // {{{ private function _get_countfile()

    /**
     * 给定一个文件获取对应的文件引用计数文件 
     * 
     * @param string $filesrc 文件名 
     * @access private
     * @return string 返回引用计数文件的路劲
     */
    private function _get_countfile($filesrc)
    {
        $count_file = $this->file_path($filesrc) . '.c';
        return $count_file;        
    }

    // }}} 
    // {{{ private function  _delete_file()

    /**
     * 删除文件，并且删除对应目录（当目录中没有文件时) 
     * 
     * @param string $filesrc  文件名
     * @access private
     * @return boolean 删除文件成功返回true，失败返回false
     */
    private function _delete_file($filesrc)
    {
        $filename = $this->file_path($filesrc);
        $dir_path = substr($filename, 0, strripos($filename, "/"));

        if (!is_file($filename)) {
            return false;
        }
        $is_unlink = unlink($filename);
        $is_unlink_c = unlink($filename . ".c");
        if ($is_unlink && $is_unlink_c) {
            if ($this->__delete_mode) { 
                $this->_delete_dir($dir_path);
            }
            return true;
        }
        return false;
    }

    // }}}
    // {{{ protected function _count_exists()

    /**
     *
     *判断引用计数文件是否存在  
     * 
     * @param string  $filesrc 文件名
     * @access protected
     * @return boolean 如果计数文件存在返回true,否则返回false
     */
    protected  function _count_exists($filesrc)
    {
        $countfile = $this->file_path($filesrc) . '.c';
        
        if (file_exists($countfile)) {
            return true;
        }

        return false;
    }
    
    // }}}
    // }}} end functions
}   
