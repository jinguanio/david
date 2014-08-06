<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * eYou Install lib
 * 
 * @category   eYou_Mail
 * @package    Em_Install
 * @copyright  $_EYOUMBR_COPYRIGHT_$
 * @version    $_EYOUMBR_VERSION$_$
 */

/**
 * 安装包基类
 * 
 * @category   eYou_Mail
 * @package    Em_Install
 * @subpackage Em_Install
 */

error_reporting(E_ALL);
set_time_limit(0);

define('PATH_HOME', $_SERVER['HOME']);
define('PATH_BACKUP', 'embuild');
define('PATH_EYOU', '/usr/local/eyou');

define('FTP_SERVER', 'mail5pub.eyou.net');
define('FTP_USER', 'libo');
define('FTP_PASS', 'aaaaa123');

define('GIT_BASE', 'ssh://libo@code.eyou.net/gitroot');

abstract class build_base
{
    // {{{ members
    
    protected $__is_upload = false;

    // }}}
    // {{{ functions
    // {{{ protected function _create_repo()

    /**
     * 创建 git 仓库 
     * 
     * @param string $repository git 仓库名称
     * @return bool
     */
    protected function _create_repo($repo)
    {
        if (!is_dir(PATH_HOME . "/" . PATH_BACKUP) && !mkdir(PATH_HOME . "/" . PATH_BACKUP, 0755, true)) {
            $this->_log("Fail: dir `" . PATH_HOME . "/" . PATH_BACKUP . "` is not exist", __LINE__);
            return false;
        }
        chdir(PATH_HOME . "/" . PATH_BACKUP);

        if (false === $this->_cmd("rm -rf {$repo}")) {
            $this->_log("Fail: delete {$repo} repository fail", __LINE__);
            return false;
        }

        if (false === $this->_cmd("git clone " . GIT_BASE . "/{$repo}")) {
            $this->_log("Fail: clone {$repo} repository fail", __LINE__);
            return false;
        }

        return true;
    }

    // }}}
    // {{{ protected function _clear_repo()

    /**
     * 删除 git repo 
     * 
     * @param string $repository 
     * @return void
     */
    protected function _clear_repo($repository)
    {
        return $this->_cmd("rm -rf " . PATH_HOME . "/" . PATH_BACKUP . "/{$repository}");
    }

    // }}}
    // {{{ protected function _upload_ftp()

    /**
     * 上传 ftp 
     * 
     * @param source $fp file handle
     * @param string $url ftp url
     * @return array
     */
    protected function _upload_ftp($fp, $url)
    {

        $ch = curl_init();
        if (!$ch) {
            $this->_log("Fail: create curl handle fail", __LINE__);
            $this->_echo($msg, true, 'fail');
        }

        //curl_setopt($ch, CURLOPT_VERBOSE, 1); // ftp 通讯详细信息
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, FTP_USER . ":" . FTP_PASS);
        curl_setopt($ch, CURLOPT_PUT, 1);
        curl_setopt($ch, CURLOPT_INFILE, $fp);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FTP_CREATE_MISSING_DIRS, 1);
        curl_exec($ch);

        $err_no = curl_errno($ch);
        $err_msg = curl_error($ch);
        curl_close($ch);

        return array( 'err_no' => $err_no, 'err_msg' =>$err_msg );
    }

    // }}}
    // {{{ protected function _init_eyou()

    /**
     * 检查 mail 目录 
     * 
     * @return void
     */
    protected function _init_eyou()
    {
        $ret = $this->_cmd("sudo rm -rf " . PATH_EYOU . "/mail");
        $msg = 'init eyou/mail dir';

        if (false === $ret) {
            $this->_log("Fail: delete `" . PATH_EYOU . "/mail` fail", __LINE__);
            $this->_echo($msg, true, 'fail');
        }

        $ret = $this->_cmd("sudo mkdir -p " . PATH_EYOU . "/mail");
        if (false === $ret) {
            $this->_log("Fail: recreate `" . PATH_EYOU . "/mail` fail", __LINE__);
            $this->_echo($msg, true, 'fail');
        }

        $this->_log('Msg: check eyou-mail dir finish', __LINE__);
        $this->_echo($msg, false, 'succ');
    }

    // }}}
    // {{{ protected function _cmd()

    /**
     * 命令 
     * 
     * @params string $cmd
     * @return void
     */
    protected function _cmd($cmd)
    {
        //$cmd = escapeshellcmd($cmd);

        unset($out);
        exec("$cmd 2>&1", $out, $ret);
        if (0 !== $ret) {
            $this->_log("Fail: exec cmd fail, Cmd: {$cmd}, Out: " . json_encode($out), __LINE__);
            return false;
        }

        $this->_log("Msg: exec cmd succ, Cmd: `{$cmd}`, Out: " . json_encode($out), __LINE__);
        return $out;
    }

    // }}}
    // {{{ protected function _log()

    /**
     * log 日志 
     * 
     * @param string $msg 
     * @param string $file 
     * @return void
     */
    protected function _log($msg, $line, $file)
    {
        $msg = date('c') . " DEBUG line: {$line}, " . $msg . PHP_EOL;
        if (500 < strlen($msg)) {
            $msg = substr($msg, 0, 500) . ".....\n";
        }

        error_log($msg, 3, $file);
    }

    // }}}
    // {{{ protected function _echo()

    /**
     * 输出 
     * 
     * @param string $msg 
     * @param bool $is_exit 
     * @return void
     */
    protected function _echo($msg, $is_exit = false, $stat = null)
    {
        switch ($stat) {
        case 'succ':
            $msg = sprintf("%-40s", $msg);
            echo "{$msg}[\033[0;32mOK\033[0m]\n";
            break;

        case 'fail':
            $msg = sprintf("%-40s", $msg);
            echo "{$msg}[\033[0;31mFAIL\033[0m]\n";
            break;

        default:
            echo "$msg\n";
        }

        if ($is_exit) {
            exit(1);
        }
    }

    // }}}
    // }}}
}

