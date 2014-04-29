<?php

/**
 * fs adapter db 处理类 
 * 
 * @uses em
 * @uses _fs_adapter_abstract
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
require_once 'em_fs_adapter_abstract.class.php';

class em_fs_adapter_db extends em_fs_adapter_abstract
{

    // {{{ members


    /**
     * 定义PDO连接的dsn 
     * 
     * @var string
     * @access protected
     */
    protected $__dsn = 'mysql:unix_socket=/usr/local/eyou/mail/opt/logs/mysql.sock;dbname=haozhongxiu';

    /**
     * 定义mysql数据库的用户名 
     * 
     * @var string
     * @access protected
     */
    protected $__user = 'root';

    /**
     *  
     * 定义存储mysql连接资源  
     * 
     * @var object 
     * @access protected 
     */
    protected static $__dbh;

    // }}}    
    // {{{ functions
    // {{{ public function  __construct()

    /**
     * 构造函数，初始化生成一个文件名 
     * 
     * @param int $deletemode 选择删除文件模式，当为1时连带目录删除
     * @access public
     * @return  string 文件名
     */
    public function __construct($deletemode = 0)
    {
        parent::__construct($deletemode);
    }
    // }}}
    // {{{ public function save_file()

    /**
     * 保存文件 
     * 
     * @param string $filesrc 将要保存的源文件的路劲
     * @param string $count   文件引用次数默认0
     * @access public
     * @return boolean|string 成功返回文件名，失败返回false
     */
    public function save_file($filesrc, $count = '0')
    {
        //创建一个目录
        $is_create_dir = $this->_create_dir();
        if (!$is_create_dir) {
            return false;
        }

        //获取文件内容或拷贝文件

        $is_copy = $this->_copy_file($filesrc);
        if (!$is_copy) {
            return false;
        }
        
        $is_add = $this->_mysql_add($filesrc);       

        if (!$is_add) {
            return false;
        }
        return $this->__filename; 
    }
    // }}}
    // {{{ public function add_count()

    /**
     * 增加引用计数 
     * 
     * @param string $filesrc 文件名
     * @access public
     * @return integer|boolean 成功返回引用个数，失败返回false
     */
    public function add_count($filesrc)
    {
        $file_num = $this->get_count($filesrc);

        if (false === $file_num) {
            return false;
        } else {
            $file_num++;
            $is_update = $this->_mysql_update($filesrc, $file_num);
            if (!$is_update) {
                return false;
            } else {
                return $file_num;    
            }
        }
     
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
        $file_num = $this->get_count($filesrc);

        if (false === $file_num) {
            return false;
        } else {
            $file_num--;
            if (0 >= $file_num) {
                $this->_delete_file($filesrc);
                return $file_num;
            }
            $is_update = $this->_mysql_update($filesrc, $file_num);
            if (!$is_update) {
                return false;
            } else {
                return $file_num;    
            }
        }
    }
    // }}}
    // {{{ public function get_count()

    /**
     * 获取一个文件的引用次数 
     * 
     * @param string  $filesrc 文件名
     * @access public
     * @return boolean|integer 成功返回引用个数，失败返回false
     */
    public function get_count($filesrc)
    {
        $dbh = $this->_mysql_conn();

        $query = "select filecount from fs_data where filename = '$filesrc' ;";
        try {
            $stmt = $dbh->query($query);
            $file_detail = $stmt->fetch(PDO::FETCH_ASSOC);
            return $file_detail['filecount'];
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }
    // }}}
    // {{{ protected function _count_exists()

    protected function _count_exists($filesrc)
    {
        $dnh = $this->_mysql_conn();
        
        $query = "select * from fs_data where filename = '$filesrc';";
        $stmt = $dnh->query($query);
        if ($stmt->rowCount()) {
            //debug
            return true;
        }
        return false;
    }
    // }}}
    // {{{ protected function  _get_filestat()

    /**
     * 获取文件状态 
     * 
     * @param string $filesrc 文件名
     * @access protected
     * @return array 返回文件状态的数组，[fileatime]=>访问时间,[filectime]=>创建时间,[filemtime]=>修改时间
     */
    protected function _get_filestat($filesrc)
    {
        $file_path = $this->file_path($this->__filename);
        clearstatcache();

        $file_stat = stat($file_path);
        return $file_stat;
    }
    // }}}
    // {{{ protected function _mysql_conn()

    /**
     * 连接数据库的单件模式 
     * 
     * @access protected
     * @return object 返回一个PDO对象
     */
    protected function _mysql_conn()
    {
        if (isset(self::$__dbh) && self::$__dbh instanceof PDO) {
            return self::$__dbh;
        } else {
            try{
                self::$__dbh = new PDO($this->__dsn, $this->__user);
            } catch (PDOException $e){
                echo '数据库链接失败：'.$e->getMessage();
            }
            return self::$__dbh;
        }     
    }
    // }}} 
    // {{{  protected function _mysql_add()

    /**
     * 添加文件信息到mysql数据库
     * 
     * @param string  $filesrc 
     * @access protected
     * @return boolean|integer 成功返回添加行数，失败返回false
     */
    protected function _mysql_add($filesrc)
    {
        //创建数据库对象
        $dbh = $this->_mysql_conn();

        //获取文件的状态
        $filestat = $this->_get_filestat($filesrc);
        
        $query = "insert into fs_data(filename, filectime, fileatime, filemtime, filecount) values(?,?,?,?,?);";
        $stmt  = $dbh->prepare($query);

        $filename  = $this->__filename;
        $filectime = $filestat['ctime'];
        $fileatime = $filestat['atime'];
        $filemtime = $filestat['mtime'];
        $filecount = 0;
  
        $stmt->bindparam(1,$filename);
        $stmt->bindparam(2,$filectime);
        $stmt->bindparam(3,$fileatime);
        $stmt->bindparam(4,$filemtime);
        $stmt->bindparam(5,$filecount);
        $is_insert = $stmt->execute();

        if (!$is_insert) {
            return false;
        }

        return $stmt->rowCount();
    }
    // }}} 
    // {{{ protected function _mysql_update()

    /**
     * 更新数据库 
     * 
     * @param string  $filesrc 文件名
     * @param string $filecount 文件引用个数
     * @access protected
     * @return boolean|integer 成功返回影响行数，失败返回false
     */
    protected function _mysql_update($filesrc, $filecount)
    {
        $dbh = $this->_mysql_conn();
        
        $filestat = $this->_get_filestat($filesrc);

        $query = "update fs_data set filecount = $filecount , filectime =". $filestat['ctime'] .", fileatime =".  $filestat['atime'] .", filemtime =". $filestat['mtime']." where filename ='$filesrc'";
        
        $affected = $dbh->exec($query);
        if (!$affected) {
            $error = $dbh->errorInfo();
            echo $error[2]; //输出错误信息
            return false;
        }
        
        return $affected;
    }
    // }}} 
    // {{{  protected function _mysql_del()

    /**
     * 从数据库中将文件信息删除 
     * 
     * @param string  $filesrc  文件名
     * @access protected
     * @return boolean 成功返回true 失败返回false
     */
    protected function _mysql_del($filesrc)
    {
        $dbh = $this->_mysql_conn();

        $query = "delete from fs_data where filename  = '$filesrc';";
        $affected = $dbh->exec($query);
        if (!$affected) {
            return false;
        }
        return true;
    }
    // }}}
    // {{{protected function _delete_file()
    
    /**
     * 删除一个文件 
     * 
     * @param string  $filesrc 文件名
     * @access protected
     * @return boolean 成功返回true，失败返回false
     */
    protected function _delete_file($filesrc)
    {
        $filename = $this->file_path($filesrc);
        $dir_path = substr($filename, 0, strripos($filename, "/"));
        if (!is_file($filename)) {
            return false;
        }
        $is_unlink = unlink($filename);
        $is_del_mysql = $this->_mysql_del($filesrc);
        if ($is_unlink && $is_del_mysql) {
            if ($this->__delete_mode) {
                $this->_delete_dir($dir_path);
            }
            return true;
        }
        return false;

    }
    // }}}
    // }}}
}
