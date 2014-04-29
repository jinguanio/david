<?php

/**
 * 文件存储类 
 * 
 * @package 
 * @version $_EYOUMBR_VERSION_$
 * @copyright $_EYOUMBR_COPYRIGHT_$
 * @author haozhongxiu <haozhongxiu@eyou.net> 
 */
class em_file_storage
{

    // {{{ members

    /**
     * __filename 生成 时间戳.随机数形式的文件名（不带扩展名的）
     *
     * @var string 
     * @access private
     */
    private $__filename;

    /**
     * 设置删除模式，默认是不删除文件目录的，设置为1则文件目录全部删除 
     * 
     * @var int
     * @access private
     */
    private $__delete_mode;

    // }}} end members 
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
    public function __construct($delete_mode=0)
    {
        $this->__delete_mode = $delete_mode;
        //生成随机字符串
        $time = time();
        $randnum = str_pad(mt_rand(), 10, "0", STR_PAD_LEFT);
        $this->__filename = $time . '.' . $randnum;    //生成形如1193797900.0022223422的文件名

        //防止随机数出现重复
        $file_dirname = $this->file_path($this->__filename);
        
        $is_dir_empty = $this->_dir_is_empty($file_dirname);

        //debug


        //如果目录中已经有文件了，重新生成一个文件名
        if (!$is_dir_empty) {
            //重新生成一个随机数
            $randnum = substr(str_pad(mt_rand(), 10, "0", STR_PAD_LEFT), 0, 10);
            $this->__filename = $time . '.' . $randnum;    //生成形如1193797900.0022223422的文件名
        }

    }

    // }}} 

    // {{{ public function save_file()

    /**
     * 保存文件 
     * 
     * @param string $filesrc 将要保存文件的文件名，格式为：（时间戳.随机数） 
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
        
        $dsc_file = $this->file_path($this->__filename);
        $count_file = $this->file_path($this->__filename) . '.c';
 

        //  {{{ 获取 文件内容或拷贝文件
        if (is_resource($filesrc)) {
            $content = "";
            while (!feof($filesrc)) {
                $content .= fread($filesrc, 1024);
            }
            fclose($filesrc);

            //debug

            $dfp = fopen($dsc_file,'x');
            if (!$dfp) {
                $dfp = fopen($dsc_file, 'w');
                if (!$dfp) {
                    return false;
                }
            }

            $is_fwrite = fwrite($dfp,$content);
            if (!$is_fwrite) {
                return false;
            }
        } else {
            $is_copy = copy($filesrc,$dsc_file);
            if (!$is_copy) {
                return false;
            }
        }

        // }}}

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
    // {{{ public fun ction add_count()

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
            if ($count_num < 0) {
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
    // {{{ public func tion del_count()

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
            if ($count_num < 0) {
                return false;
            }
            $count_num--;
            if ($count_num <= 0) {

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
    // {{{ public function file_path()

    /**
     * 获取一个文件名的全路径文件名的格式是(时间戳.10位随机数) 
     * 
     * @param string $filesrc 文件名 
     * @param int $mode 返回文件路径模式，默认返回全部路劲，当置为0返回文件的目录
     * @access public
     * @return boolean|string 成功返回文件的全路劲，失败返回false
     */
    public function file_path($filesrc, $mode = 1)
    {
        $pattern = '/^(\d{10})\.(\d{10})$/';
        $is_match = preg_match($pattern, $filesrc, $matches);

        if (!$is_match) {
            return false;
        }
        if ($mode) {                 
            $filepath = './fs/' . date("Ym/d/H/i/s",$matches[1]) . '/' . $filesrc;
        } else {
            $filepath = './fs/' . date("Ym/d/H/i/s", $matches[1]) . '/';
                
        }
        return $filepath;
    }

    // }}} 
    // {{{ public function get_file_content()

    /**
     * 获取文件的内容 
     * 
     * @param string $filesrc 文件名
     * @param int $mode 文件获取模式默认返回资源，当置为0或false时返回文件内容
     * @access public
     * @return resource|boolean|string 成功返回资源或文件内容，失败返回false
     */
    public function get_file_content($filesrc, $mode = 1)
    {
        $is_count_exists = $this->_count_exists($filesrc);
        if (!$is_count_exists) {
            return false;        
        }
        $fp = fopen($this->file_path($filesrc),'r');
        if (!$fp) { 
            return false;           
        }
        
        //如果第二个参数是1时则反回文件资源  
        if ($mode) {
            return $fp;
        } else {
            $contents = "";
            while (!feof($fp)) {
                $contents .= fread($fp, 1024);
            }
            fclose($fp);
            return $contents;
        }
        return false;
    }

    // }}} 
    // {{{ private function _create_dir()

    /**
     * 创建目录 
     * 
     * @access private
     * @return boolean 创建成功返回true，失败返回false
     */
    private function _create_dir()
    {
        $dirname = $this->file_path($this->__filename, 0);
        $is_dir_exists = file_exists($dirname);
        if ($is_dir_exists) {
           return true; 
        }
        
        //debug 
        $is_mkdir = mkdir($dirname, 0777, true);
        if ($is_mkdir) {
            return true;
        } 

        //清除文件状态缓存（防止读取文件状态不准确）
        clearstatcache();
        $is_dir_exists = file_exists($dirname);
        //防止并发时出现第一次判断目录不存在，反而创建时不成功（其实有其他用户已经创建）的错误，进行再次判断
        if ($is_dir_exists) {
            //debug
            return true;
        }
        return false;
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
    // {{{ private function _delete_dir()
    
    /**
     * 删除目录函数
     * 
     * @param string  $dir_path 目录路劲
     * @access private 
     * @return boolean 删除目录成功返回true，失败返回false
     */
    private function _delete_dir($dir_path)
    { 
        $dir_path = rtrim($dir_path, '/');
        $updir = substr($dir_path, 0, strripos($dir_path, "/"));
        $dir_depth = count(explode('/', $dir_path));
        if ($dir_depth < 3) {
            return true;
        }

        if (is_dir($dir_path)) {
            if ($this->_dir_is_empty($dir_path)) {
                rmdir($dir_path);
            } else {
                return false;
            }
        } else {
            return false;
        }

        $this->_delete_dir($updir);
    } 
    // }}}
    // {{{ private function _dir_is_empty()

    /**
     * 判断一个目录是否为空 
     * 
     * @param string $dirname 目录路劲
     * @access private
     * @return boolean 成功返回true，失败返回false
     */
    private function _dir_is_empty($dirname)
    {
        $is_exists = file_exists($dirname);
        if (!$is_exists) {
            return true;
        }
        $handle = opendir($dirname);
        if (!$handle) {
            return true;
        }

        while ($item = readdir($handle)) {
            if ($item != "." && $item != ".."){ 
                return false;
            } else {
                return true;
            }
        }
    }

    // }}}
    // {{{ private function _count_exists()

    /**
     *
     *判断引用计数文件是否存在  
     * 
     * @param string  $filesrc 文件名
     * @access private
     * @return boolean 如果计数文件存在返回true,否则返回false
     */
    private function _count_exists($filesrc)
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
