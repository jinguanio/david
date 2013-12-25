<?php

/**
 * 文件存储类 
 * 
 * @package 
 * @version $_EYOUMBR_VERSION_$
 * @copyright $_EYOUMBR_COPYRIGHT_$
 * @author haozhongxiu <haozhongxiu@eyou.net> 
 */
abstract class em_fs_adapter_abstract
{

    // {{{ members 

    /**
     * __filename 生成 时间戳.随机数形式的文件名（不带扩展名的）
     *
     * @var string 
     * @access protected
     */
    protected $__filename;

    /**
     * 设置删除模式，默认是不删除文件目录的，设置为1则文件目录全部删除 
     * 
     * @var int
     * @access protected
     */
    protected  $__delete_mode;

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
            $fp = fopen($this->file_path($filesrc), 'r');
            if (!$fp) {
                return false;
            }

            //如果第二个参数是1时则反回文件资源
            if ($mode) {
                return $fp;
            } else {
                $contents = "";
                while (!feof($fp)) {
                    $contents .= fread($fp, 104);
                }
                fclose($fp);
                return $contents;
            }
            return false;
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
            $filepath = './uploads/' . date("Ym/d/H/i/s",$matches[1]) . '/' . $filesrc;
        } else {
            $filepath = './uploads/' . date("Ym/d/H/i/s", $matches[1]) . '/';
                
        }
        return $filepath;
    }

    // }}} 
   // {{{ protected function _create_dir()

    /**
     * 创建目录 
     * 
     * @access protected
     * @return boolean 创建成功返回true，失败返回false
     */
    protected function _create_dir()
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
    // {{{ protected function _delete_dir()
    
    /**
     * 删除目录函数
     * 
     * @param string  $dir_path 目录路劲
     * @access protected 
     * @return boolean 删除目录成功返回true，失败返回false
     */
    protected function _delete_dir($dir_path)
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
    // {{{ protected function _dir_is_empty()

    /**
     * 判断一个目录是否为空 
     * 
     * @param string $dirname 目录路劲
     * @access protected
     * @return boolean 成功返回true，失败返回false
     */
    protected function _dir_is_empty($dirname)
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
    // {{{ protected function _copy_file()

    /**
     * 拷贝文件 
     * 
     * @param string $filesrc 
     * @access protected
     * @return void
     */
    protected function _copy_file($filesrc)
    {
        
        $dsc_file = $this->file_path($this->__filename);
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
        return true;
    }
    // }}}
    // {{{ abstract public function save_file() 

    /**
     * 保存文件 
     * 
     * @param string $filesrc 将要保存文件的文件名，格式为：（时间戳.随机数） 
     * @param string $count   文件引用次数（可选）默认0 
     * @access public
     * @return boolean|string 成功返回文件名，失败返回false
     */
    abstract public function save_file($filesrc, $count = "0");
    
    // }}}
    // {{{ abstract public function add_count() 

    /**
     * 增加引用计数
     * 
     * @param string  $filesrc为格式  时间戳.随机数 
     * @access public
     * @return integer|boolean  成功返回引用个数，失败返回false
     */
    abstract public function add_count($filesrc);

    // }}}
    // {{{ abstract public function del_count() 

    /**
     * 减少一个引用计数 
     * 
     * @param string $filesrc 文件名
     * @access public
     * @return integer|boolean 成功返回引用个数，失败返回false
     */
    abstract public function del_count($filesrc);

    // }}}
    // {{{ abstract public function get_count()

    /**
     * 获取一个文件的引用次数
     * 
     * @param string $filesrc 文件名 
     * @access public 
     * @return boolean|integer 成功返回引用个数，失败返回false
     */
    abstract public function get_count($filesrc);

    // }}} 
    // {{{ abstract protected function _count_exists()

    /**
     * 判断文件是否存在
     * 
     * @param string $filesrc 文件名 
     * @access protected 
     * @return boolean 存在返回true，不存在返回false
     */
    abstract protected function _count_exists($filesrc);

    // }}} 
    // }}} end functions
}
