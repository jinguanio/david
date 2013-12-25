<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * eYou Mail Daemon
 *
 * @category    eYou_Mail
 * @package     Em_Daemon
 * @copyright   $_EYOUMBR_COPYRIGHT_$
 * @version     $_EYOUMBR_VERSION_$
 */
require_once EMBASE_PATH_EYOU_TOOLMAIL_CONF . 'conf_global.php';
require_once PATH_EYOUM_IMPLEMENTS . 'process/emimp_process_abstract.class.php';

/**
 * gmond_metric 子进程
 *
 * @category    eYou_Mail
 * @package     Em_Daemon
 * @subpackage  Process
 */
class emimp_process_gmond_metric extends emimp_process_abstract
{
    // {{{ consts

    /**
     * 换行符 
     */
    const CRLF = "\r\n";

    /**
     * 超时返回结果 秒
     */
    const RE_TIMEOUT = 40;

    /**
     * 认证失败返回 秒
     */
    const RE_AUTH = 50;

    /**
     * 其他失败返回 (包括：socket 连接失败、fwrite/fread失败) 秒 
     */
    const RE_OTHER = 60;

    /**
     * 连接超时时间 3s 
     */
    const CONNECT_TIMEOUT = 1;

    /**
     * 读写超时时间 30s 
     */
    const RW_TIMEOUT = 3;

    /**
     * multi 分隔符 
     */
    const SEPARATE_MULTI = '__';

    // 获取项目, 请求时间
    const POP3D_AUTH = 'mailservice_pop3d_req_time_auth';
    const SMTPD_AUTH = 'mailservice_smtpd_req_time_auth';
    const IMAPD_AUTH = 'mailservice_imapd_req_time_auth';
    const FILED_ADD  = 'mailservice_filed_req_time_add_50k';
    const FILED_DEL  = 'mailservice_filed_req_time_del_50k';

    const STATUS_START    = 0;
    // pop
    const POP_STATUS_USER     = 1;
    const POP_STATUS_USER_RE  = 2;
    const POP_STATUS_PASS     = 3;
    const POP_STATUS_PASS_RE  = 4;
    const POP_STATUS_QUIT     = 5;
    const POP_STATUS_QUIT_RE  = 6;
    // smtp
    const SMTP_STATUS_EHLO    = 1;
    const SMTP_STATUS_EHLO_RE = 2;
    const SMTP_STATUS_AUTH    = 3;
    const SMTP_STATUS_AUTH_RE = 4;
    const SMTP_STATUS_USER    = 5;
    const SMTP_STATUS_USER_RE = 6;
    const SMTP_STATUS_PASS    = 7;
    const SMTP_STATUS_PASS_RE = 8;
    const SMTP_STATUS_QUIT    = 9;
    const SMTP_STATUS_QUIT_RE = 10;
    // imap
    const IMAP_PREFIX = 'gmond ';
    const IMAP_STATUS_LOGIN    = 1;
    const IMAP_STATUS_LOGIN_RE = 2;
    const IMAP_STATUS_LIST     = 3;
    const IMAP_STATUS_LIST_RE  = 4;
    const IMAP_STATUS_QUIT     = 5;
    const IMAP_STATUS_QUIT_RE  = 6;

    // }}}
    // {{{ members

    /**
     * 默认睡眠时间 5 min
     */
    protected $__sleep_time = 300;

    /**
     * 开始执行时间
     */
    protected $__start_time = 0;

    /**
     * 登录 MTA 的用户名 
     */
    protected $__mta_user;

    /**
     * 登录 MTA 的密码 
     */
    protected $__mta_pass;

    /**
     * event base 
     * 
     * @var EventBase
     * @access protected
     */
    protected $__event_base = null;

    /**
     * events 
     * 
     * @var EventBufferEvent
     * @access protected
     */
    protected $__events = array();

    /**
     * 记录连接状态 
     * 
     * @var array
     * @access protected
     */
    protected $__status = array();

    /**
     * 连接地址 
     * 
     * @var array
     * @access protected
     */
    protected $__host = array();

    /**
     * 允许发送的数据
     * 
     * @var array
     * @access protected
     */
    protected $__allow_send = array(
        self::POP3D_AUTH => true,
        self::IMAPD_AUTH => true,
        self::SMTPD_AUTH => true,
        self::FILED_ADD  => true,
        self::FILED_DEL  => true,
    );

    // }}} end members
    // {{{ functions
    // {{{ protected function _init()

    /**
     * 初始化
     *
     * @return void
     */
    protected function _init()
    {
        $this->log('Start gmond metric, param:' . var_export($this->__proc_config, true), __FILE__, __LINE__, LOG_DEBUG);

        if (!empty($this->__proc_config['sleep_time'])) {
            $this->__sleep_time = $this->__proc_config['sleep_time'];
        }

        if (!empty($this->__proc_config['mta_user'])) {
            $this->__mta_user = $this->__proc_config['mta_user'];
        }

        if (!empty($this->__proc_config['mta_pass'])) {
            $this->__mta_pass = $this->__proc_config['mta_pass'];
        }

        // pop 的相关配置
        $host = $this->_parse_host('pop');
        if ($host) {
            foreach ($host as $key => $value) {
                $this->__host[self::POP3D_AUTH . self::SEPARATE_MULTI . $key] = $value;    
            }
        }

        // smtp 的相关配置
        $host = $this->_parse_host('smtpd');
        if ($host) {
            foreach ($host as $key => $value) {
                $this->__host[self::SMTPD_AUTH . self::SEPARATE_MULTI . $key] = $value;    
            }
        }

        // imap 的相关配置
        $host = $this->_parse_host('imapd');
        if ($host) {
            foreach ($host as $key => $value) {
                $this->__host[self::IMAPD_AUTH . self::SEPARATE_MULTI . $key] = $value;    
            }
        }

        $this->__event_base = new EventBase();

        if (!$this->__event_base) {
            $log = 'can not create event base';
            $this->log($log, __FILE__, __LINE__, LOG_WARNING);
            require_once PATH_EYOUM_IMPLEMENTS . 'process/emimp_process_exception.class.php';
            throw new emimp_process_exception($log);
        }
    }

    // }}}
    // {{{ protected function _run()

    /**
     * 单次执行
     *
     * @return void
     */
    protected function _run()
    {
        // 未到采集时间
        $run_time = microtime(true) - $this->__start_time;
        if ($this->__sleep_time >= $run_time) {
            $sleep_time = $this->__sleep_time - $run_time;
            $this->log("gmond metric sleep " . $this->__sleep_time, __FILE__, __LINE__, LOG_DEBUG);
            usleep(intval($sleep_time * 1000000));
            return;
        }

        $this->__start_time = microtime(true);
        foreach ($this->__host as $key => $host) {
            // 初始化状态
            $this->__status[$key] = self::STATUS_START;
            // 非阻塞方式创建 socket
            $client_src = stream_socket_client($host, $errno, $errstr, self::CONNECT_TIMEOUT, STREAM_CLIENT_ASYNC_CONNECT | STREAM_CLIENT_CONNECT);
            stream_set_blocking($client_src, 0);
            if (!$client_src) {
                // 创建 socket client 失败
                $this->log("connect socket faild, process: $key host: $host", __FILE__, __LINE__, LOG_DEBUG);
                $this->_send_gmond($key, self::RE_OTHER);
                continue; 
            }

            $this->__events[$key] = new Event($this->__event_base, $client_src, Event::READ, array($this, 'callbak_event'), $key);
            $this->__events[$key]->add();
        }

        $this->__event_base->loop();
        $this->log('gmond get data run time:' . (microtime(true) - $this->__start_time) * 1000, __FILE__, __LINE__, LOG_DEBUG);
    }

    // }}}
    // {{{ protected function _parse_host()

    /**
     * 解析服务的主机地址信息
     * 
     * @param string $type 
     * @access protected
     * @return void
     */
    protected function _parse_host($type)
    {
        $host_key  = $type . '.host';
        $multi_key = $type . '.multi';
        if (isset($this->__proc_config[$host_key]) && isset($this->__proc_config[$multi_key])) {
            $host  = trim_array(explode(',', $this->__proc_config[$host_key]));
            $multi = trim_array(explode(',', $this->__proc_config[$multi_key]));
            if (count($host) !== count(array_unique($multi))) {
                $log = "$type.host and $type.multi config error.";
                $this->log($log, __FILE__, __LINE__, LOG_WARNING);
                require_once PATH_EYOUM_IMPLEMENTS . 'process/emimp_process_exception.class.php';
                throw new emimp_process_exception($log);
            }

            $host = array_combine($multi, $host);
            return $host;
        }

        return false;
    }

    // }}}
    // {{{ public function callbak_event()

    /**
     * callbak_event 
     * 
     * @access public
     * @return void
     */
    public function callbak_event($client_src, $what, $key)
    {
        if (false === strpos($key, self::SEPARATE_MULTI)) {
            //发送数据，错误
            $this->log('invalid param, callback:' . $key, __FILE__, __LINE__, LOG_DEBUG);
            $this->_send_gmond($key, self::RE_OTHER);
            $this->_del_event($key);
            return false;    
        }

        list($data_name, $multi_name) = explode(self::SEPARATE_MULTI, $key, 2);
        $event_buffer = null;
        $opt = EventBufferEvent::OPT_DEFER_CALLBACKS | EventBufferEvent::OPT_CLOSE_ON_FREE;
        switch ($data_name) {
            case self::POP3D_AUTH:
                $event_buffer = new EventBufferEvent($this->__event_base, $client_src, $opt,
                        array($this, 'callback_read_pop'), array($this, 'callback_write_pop'), array($this, 'callback_error'), $key);
                break;
            case self::SMTPD_AUTH:
                $event_buffer = new EventBufferEvent($this->__event_base, $client_src, $opt,
                        array($this, 'callback_read_smtp'), array($this, 'callback_write_smtp'), array($this, 'callback_error'), $key);
                break;
            case self::IMAPD_AUTH:
                $event_buffer = new EventBufferEvent($this->__event_base, $client_src, $opt,
                        array($this, 'callback_read_imap'), array($this, 'callback_write_imap'), array($this, 'callback_error'), $key);
                break;
            case self::FILED_ADD:
                $event_buffer = new EventBufferEvent($this->__event_base, $client_src, $opt,
                        array($this, 'callback_read_pop'), array($this, 'callback_write_pop'), array($this, 'callback_error'), $key);
                break;
            case self::FILED_DEL:
                $event_buffer = new EventBufferEvent($this->__event_base, $client_src, $opt,
                        array($this, 'callback_read_pop'), array($this, 'callback_write_pop'), array($this, 'callback_error'), $key);
                break;
        }

        if ($event_buffer instanceof EventBufferEvent) {
            $event_buffer->setTimeouts(self::RW_TIMEOUT, self::RW_TIMEOUT);
            $event_buffer->enable(Event::READ | Event::TIMEOUT);
        }
    }

    // }}}
    // {{{ public function callback_error()

    /**
     * 错误处理回调 
     * 
     * @param EventBufferEvent $buffer 
     * @access public
     * @return void
     */
    public function callback_error($buffer, $event, $key)
    {
        if ($event == (EventBufferEvent::TIMEOUT | EventBufferEvent::READING)
            || $event == (EventBufferEvent::TIMEOUT | EventBufferEvent::WRITING)) {
            $this->_send_gmond($key, self::RE_TIMEOUT);
            $this->_del_event($key, $buffer);
            $this->log("read or write timeout, key: $key", __FILE__, __LINE__, LOG_DEBUG);
            return false;
        }

        return true;
    }

    // }}}
    // {{{ public function callback_read_pop()

    /**
     * POP 处理读回调 
     * 
     * @param EventBufferEvent $buffer 
     * @access public
     * @return void
     */
    public function callback_read_pop($buffer, $key)
    {
        if (!isset($this->__status[$key])) {
            //发送数据，错误
            $this->log('invalid param, pop3d_auth:' . $host, __FILE__, __LINE__, LOG_DEBUG);
            $this->_send_gmond($key, self::RE_OTHER);
            $this->_del_event($key, $buffer);
            return false;    
        }
        $read = $buffer->read(1024);
        if ($read && 0 === strpos(trim($read), '+OK')) {
            switch ($this->__status[$key]) {
                case self::STATUS_START:
                    $this->__status[$key] = self::POP_STATUS_USER;
                    break;
                case self::POP_STATUS_USER_RE:
                    $this->__status[$key] = self::POP_STATUS_PASS;
                    break;
                case self::POP_STATUS_PASS_RE:
                    $this->__status[$key] = self::POP_STATUS_QUIT;
                    break;
                case self::POP_STATUS_QUIT_RE:
                    // 发送数据, 成功
                    $time = microtime(true) - $this->__start_time;
                    $time = ($time <= 1) ? 1 : $time;
                    $this->_send_gmond($key, $time);
                    $this->_del_event($key, $buffer);
                    return true;
            } 
            $buffer->enable(Event::WRITE | Event::TIMEOUT);
        }

        // 认证失败处理
        if (!$read || 0 === strpos(trim($read), '-ERR')) {
            $this->_send_gmond($key, self::RE_AUTH);
            $this->_del_event($key, $buffer);
            return false;
        }
    }

    // }}}
    // {{{ public function callback_write_pop()

    /**
     * POP 处理写回调 
     * 
     * @param EventBufferEvent $buffer 
     * @access public
     * @return void
     */
    public function callback_write_pop($buffer, $key)
    {
        if (!isset($this->__status[$key])) {
            //发送数据，错误
            $this->log('invalid param, pop3d_auth:' . $host, __FILE__, __LINE__, LOG_DEBUG);
            $this->_send_gmond($key, self::RE_OTHER);
            $this->_del_event($key, $buffer);
            return false;    
        }

        switch ($this->__status[$key]) {
            case self::POP_STATUS_USER:
                $write = $buffer->write('user ' . $this->__mta_user . self::CRLF);
                if ($write) {
                    $this->__status[$key] = self::POP_STATUS_USER_RE;    
                }
                break;
            case self::POP_STATUS_PASS:
                $write = $buffer->write('pass ' . $this->__mta_pass . self::CRLF);
                if ($write) {
                    $this->__status[$key] = self::POP_STATUS_PASS_RE;    
                }
                break;
            case self::POP_STATUS_QUIT:
                $write = $buffer->write('quit' . self::CRLF);
                if ($write) {
                    $this->__status[$key] = self::POP_STATUS_QUIT_RE;    
                }
                break;
        } 

        $buffer->enable(Event::READ | Event::TIMEOUT);
    }

    // }}}
    // {{{ public function callback_read_smtp()

    /**
     * SMTP 处理读回调 
     * 
     * @param EventBufferEvent $buffer 
     * @access public
     * @return void
     */
    public function callback_read_smtp($buffer, $key)
    {
        if (!isset($this->__status[$key])) {
            //发送数据，错误
            $this->log('invalid param, smtp_auth:' . $host, __FILE__, __LINE__, LOG_DEBUG);
            $this->_send_gmond($key, self::RE_OTHER);
            $this->_del_event($key, $buffer);
            return false;    
        }
        $data = $buffer->read(1024);

        if (!$data) {
            $this->_send_gmond($key, self::RE_OTHER);
            $this->_del_event($key, $buffer);
            return false;
        } 

        // 错误处理， 如果是 4, 5的是否定应答
        $first_bit = substr(trim($data), 0, 1);
        if (in_array($first_bit, [4, 5])) {
            $this->_send_gmond($key, self::RE_AUTH);
            $del = $this->_del_event($key, $buffer);
            return false;
        }

        $return_code = substr(trim($data), 0, 3);
        switch ($this->__status[$key]) {
            case self::STATUS_START:
                if ('220' == $return_code) {
                    $this->__status[$key] = self::SMTP_STATUS_EHLO;
                }
                break;
            case self::SMTP_STATUS_EHLO_RE:
                if ('250' == $return_code) {
                    $this->__status[$key] = self::SMTP_STATUS_AUTH;
                }
                break;
            case self::SMTP_STATUS_AUTH_RE:
                if ('334' == $return_code) {
                    $this->__status[$key] = self::SMTP_STATUS_USER;
                }
                break;
            case self::SMTP_STATUS_USER_RE:
                if ('334' == $return_code) {
                    $this->__status[$key] = self::SMTP_STATUS_PASS;
                }
                break;
            case self::SMTP_STATUS_PASS_RE:
                if ('235' == $return_code) {
                    $this->__status[$key] = self::SMTP_STATUS_QUIT;
                }
                break;
            case self::SMTP_STATUS_QUIT_RE:
                // 发送数据, 成功
                if ('221' == $return_code) {
                    $time = microtime(true) - $this->__start_time;
                    $time = ($time <= 1) ? 1 : $time;
                    $this->_send_gmond($key, $time);
                    $this->_del_event($key, $buffer);
                    return true;
                }
                break;
        } 
        $buffer->enable(Event::WRITE | Event::TIMEOUT);
    }

    // }}}
    // {{{ public function callback_write_smtp()

    /**
     * SMTP 处理写回调 
     * 
     * @param EventBufferEvent $buffer 
     * @access public
     * @return void
     */
    public function callback_write_smtp($buffer, $key)
    {
        if (!isset($this->__status[$key])) {
            //发送数据，错误
            $this->log('invalid param, smtp_auth:' . $host, __FILE__, __LINE__, LOG_DEBUG);
            $this->_send_gmond($key, self::RE_OTHER);
            $this->_del_event($key, $buffer);
            return false;    
        }

        if (false === strpos($this->__mta_user, '@')) {
            //发送数据，错误
            $this->log('invalid mta user name, smtp_auth:' . $host, __FILE__, __LINE__, LOG_DEBUG);
            $this->_send_gmond($key, self::RE_OTHER);
            $this->_del_event($key, $buffer);
            return false;    
        }

        list($user_name, $domain_name) = explode('@', $this->__mta_user);
        switch ($this->__status[$key]) {
            case self::SMTP_STATUS_EHLO:
                $write = $buffer->write('ehlo ' . $domain_name . self::CRLF);
                if ($write) {
                    $this->__status[$key] = self::SMTP_STATUS_EHLO_RE;    
                }
                break;
            case self::SMTP_STATUS_AUTH:
                $write = $buffer->write('auth login' . self::CRLF);
                if ($write) {
                    $this->__status[$key] = self::SMTP_STATUS_AUTH_RE;    
                }
                break;
            case self::SMTP_STATUS_USER:
                $write = $buffer->write(base64_encode($user_name) . self::CRLF);
                if ($write) {
                    $this->__status[$key] = self::SMTP_STATUS_USER_RE;    
                }
                break;
            case self::SMTP_STATUS_PASS:
                $write = $buffer->write(base64_encode($this->__mta_pass) . self::CRLF);
                if ($write) {
                    $this->__status[$key] = self::SMTP_STATUS_PASS_RE;    
                }
                break;
            case self::SMTP_STATUS_QUIT:
                $write = $buffer->write('quit' . self::CRLF);
                if ($write) {
                    $this->__status[$key] = self::SMTP_STATUS_QUIT_RE;    
                }
                break;
        } 

        $buffer->enable(Event::READ | Event::TIMEOUT);
    }

    // }}}
    // {{{ public function callback_read_imap()

    /**
     * IMAP 处理读回调 
     * 
     * @param EventBufferEvent $buffer 
     * @access public
     * @return void
     */
    public function callback_read_imap($buffer, $key)
    {
        if (!isset($this->__status[$key])) {
            //发送数据，错误
            $this->log('invalid param, imap_auth:' . $host, __FILE__, __LINE__, LOG_DEBUG);
            $this->_send_gmond($key, self::RE_OTHER);
            $this->_del_event($key, $buffer);
            return false;    
        }
        $data = $buffer->read(1024);
        $this->log($data, __FILE__, __LINE__, LOG_DEBUG);

        if (!$data) {
            $this->_send_gmond($key, self::RE_OTHER);
            $this->_del_event($key, $buffer);
            return false;
        } 


        $status = $this->__status[$key];
        switch ($status) {
            case self::STATUS_START:
                if (0 === strpos(trim($data), '* OK')) {
                    $this->__status[$key] = self::IMAP_STATUS_LOGIN;
                }
                break;
            case self::IMAP_STATUS_LOGIN_RE:
                if (0 === strpos(trim($data), self::IMAP_PREFIX . 'OK')) {
                    $this->__status[$key] = self::IMAP_STATUS_LIST;
                }
                break;
            case self::IMAP_STATUS_LIST_RE:
                if (0 === strpos(trim($data), self::IMAP_PREFIX . 'OK')) {
                    $this->__status[$key] = self::IMAP_STATUS_QUIT;
                }
                break;
            case self::IMAP_STATUS_QUIT_RE:
                // 发送数据, 成功
                if (0 === strpos(trim($data), '* BYE')) {
                    $time = microtime(true) - $this->__start_time;
                    $time = ($time <= 1) ? 1 : $time;
                    $this->_send_gmond($key, $time);
                    $this->_del_event($key, $buffer);
                    return true;
                }
                break;
        } 

        // 错误处理
        if ($status === $this->__status[$key]) {
            $this->_send_gmond($key, self::RE_AUTH);
            $del = $this->_del_event($key, $buffer);
            return false;
        } else {
            $buffer->enable(Event::WRITE | Event::TIMEOUT);
        }
    }

    // }}}
    // {{{ public function callback_write_imap()

    /**
     * IMAP 处理写回调 
     * 
     * @param EventBufferEvent $buffer 
     * @access public
     * @return void
     */
    public function callback_write_imap($buffer, $key)
    {
        if (!isset($this->__status[$key])) {
            //发送数据，错误
            $this->log('invalid param, imap_auth:' . $host, __FILE__, __LINE__, LOG_DEBUG);
            $this->_send_gmond($key, self::RE_OTHER);
            $this->_del_event($key, $buffer);
            return false;    
        }

        switch ($this->__status[$key]) {
            case self::IMAP_STATUS_LOGIN:
                $content = self::IMAP_PREFIX . 'login ' . $this->__mta_user . ' ' . $this->__mta_pass . self::CRLF;
                $write = $buffer->write($content);
                if ($write) {
                    $this->__status[$key] = self::IMAP_STATUS_LOGIN_RE;    
                }
                break;
            case self::IMAP_STATUS_LIST:
                $write = $buffer->write(self::IMAP_PREFIX . 'list "~/Mail/" "%"' . self::CRLF);
                if ($write) {
                    $this->__status[$key] = self::IMAP_STATUS_LIST_RE;    
                }
                break;
            case self::IMAP_STATUS_QUIT:
                $write = $buffer->write(self::IMAP_PREFIX . 'logout' . self::CRLF);
                if ($write) {
                    $this->__status[$key] = self::IMAP_STATUS_QUIT_RE;    
                }
                break;
        } 

        $buffer->enable(Event::READ | Event::TIMEOUT);
    }

    // }}}
    // {{{ protected function _send_gmond()
    
    /**
     * _send_gmond 
     * 
     * @param string $key
     * @param float $value 
     * @access protected
     * @return void
     */
    protected function _send_gmond($key, $value)
    {
        if (false === strpos($key, self::SEPARATE_MULTI)) {
            //发送数据，错误
            $this->log('invalid param, key:' . $key, __FILE__, __LINE__, LOG_DEBUG);
            return;    
        }
        list($data_name, $multi_name) = explode(self::SEPARATE_MULTI, $key, 2);

        if (!array_key_exists($data_name, $this->__allow_send) || false == $this->__allow_send[$data_name]) {
            $this->log("not allow send $data_name data to gmond", __FILE__, __LINE__, LOG_DEBUG);
        }

        $param = $this->_send_param($data_name);
        $cmd = PATH_EYOUM_OPT_BIN . 'gmetric -n ' . $key . ' -v ' . $value . ' -t ' . $param['value_type']
               . ' -u ' . $param['units'] . ' -s ' . $param['slope'] . ' -x ' . $param['time_max']  . ' -g ' . $param['groups']
               . ' -D "d" ' . ' -T "t"';
        exec($cmd, $rev, $status);
        if ($status) {
            $this->log("send $key data to gmond faild", __FILE__, __LINE__, LOG_DEBUG);
            return;
        }
        $this->log("send gmond success. name: $key, value $value", __FILE__, __LINE__, LOG_DEBUG);
    }

    // }}}
    // {{{ protected function _send_param()

    /**
     * 获取发送参数 
     * 
     * @access protected
     * @return array
     */
    protected function _send_param($type)
    {
        $p_group = 'mailservice';

        $metrics[self::POP3D_AUTH] = 
        array(
            'name' => self::POP3D_AUTH,
            'time_max' => 60,
            'value_type' => 'float',
            'units' => 'sec',
            'slope' => 'both',
            'groups' => $p_group,
        );

        $metrics[self::SMTPD_AUTH] = 
        array(
            'name' => self::SMTPD_AUTH,
            'time_max' => 60,
            'value_type' => 'float',
            'units' => 'sec',
            'slope' => 'both',
            'groups' => $p_group,
        );

        $metrics[self::IMAPD_AUTH] = 
        array(
            'name' => self::IMAPD_AUTH,
            'time_max' => 60,
            'value_type' => 'float',
            'units' => 'sec',
            'slope' => 'both',
            'groups' => $p_group,
        );

        $metrics[self::FILED_ADD] = 
        array(
            'name' => self::FILED_ADD,
            'time_max' => 60,
            'value_type' => 'float',
            'units' => 'sec',
            'slope' => 'both',
            'groups' => $p_group,
        );

        $metrics[self::FILED_DEL] = 
        array(
            'name' => self::FILED_DEL,
            'time_max' => 60,
            'value_type' => 'float',
            'units' => 'sec',
            'slope' => 'both',
            'groups' => $p_group,
        );
            
        return isset($metrics[$type]) ? $metrics[$type] : false;
    }

    // }}}
    // {{{ protected function _del_event()

    /**
     * _del_event 
     * 
     * @param string $event_key 
     * @access protected
     * @return void
     */
    protected function _del_event($event_key, $buffer = null)
    {
        // 关闭 buffer 的读写
        if (isset($buffer) && ($buffer instanceof EventBufferEvent)) {
            $buffer->disable(Event::READ | Event::WRITE);
        }

        // 删除时间句柄
        if (isset($this->__events[$event_key])) {
            $del = $this->__events[$event_key]->del();
            return $del;    
        }

        return false;
    }

    // }}}
    // }}} end functions
}
