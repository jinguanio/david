<?php
/**
 * 用于8.2.0中语言eyou_mail和app_mailadmin配置的描述的同步
 *
 * 添加语言只需要在eyou_mail中添加，8.2.0以前的版本中新添加的配置如果需要同步到8.2.0需要利用语言系统的版本合并
 * 先将eyou_mail域下的语言合并到8.2.0上
 *
 * 对于检测没有翻译的配置项是以eyou_mail域为准
 */

define('EYOUM_EXEC_ROOT', true);
require_once 'conf_global.php';
require_once PATH_EYOUM_LIB . 'config/em_config_dynamic.class.php';
require_once PATH_EYOUM_LIB . 'em_newt.class.php';
require_once PATH_EYOUM_LIB . 'em_etc.class.php';
class tran 
{
	// {{{ members

	/**
	 * __type 
	 * 
	 * @var string
	 * @access protected
	 */
	protected $__type = 'member';

	/**
	 * 新语言库信息 
	 * 
	 * @var array
	 * @access protected
	 */
	protected $__new_info = array (
		'project_name'  => 'eyou_mail5',
		'domain_name'   => 'app_mailadmin',
		'version_name'  => 'dev_8.2.0',
		'category_name' => 'SYS MEM DESC',
	);

	/**
	 * 旧的语言库信息 
	 * 
	 * @var array
	 * @access protected
	 */
	protected $__old_info = array (
		'project_name'  => 'eyou_mail5',
		'domain_name'   => 'eyou_mail',
		'version_name'  => 'dev_8.2.0',
	);

    /**
     * 翻译系统地址 
     * 
     * @var string
     * @access protected
     */
    protected $__host = 'http://172.16.100.110:8080';

	/**
	 * 精确匹配的个数 
	 * 
	 * @var float
	 * @access protected
	 */
	protected $__count_lang = 0;

	protected $__ignore_array = array(
		'captcha_display#user_login' => true,
		'captcha_display#admin_login' => true,
		'captcha_display#reset_password' => true,
		'captcha_display#user_register' => true,
		'captcha_display#user_authreg' => true,
		'lang_list#en_US' => true,
		'lang_list#en_US#default' => true,
		'lang_list#en_US#list' => true,
		'lang_list#zh_CN' => true,
		'lang_list#zh_CN#default' => true,
		'lang_list#zh_CN#list' => true,
		'innerapi_user#eyou' => true,
		'innerapi_user#eyou#key' => true,
	);

	// }}}
	// {{{ functions
	// {{{ public function run()

	/**
	 * run 
	 * 
	 * @access public
	 * @return void
	 */
	public function run()
	{
		$tran_arr = $this->_find_msgid();
		$new_domain_id = $this->_get_lang_domain_id($this->__new_info);
		$old_domain_id = $this->_get_lang_domain_id($this->__old_info);
		$old_tran_item = array();
		$new_tran_item = array();
		$no_tran_item = array();
		$real_count = count($tran_arr);
		if (!empty($tran_arr)) {
			foreach ($tran_arr as $msg_id) {
				if ('' == trim($msg_id)) {
					continue;	
				}
				$old_arr = $this->_get_lang($msg_id, $old_domain_id);
				$new_arr = $this->_get_lang($msg_id, $new_domain_id);
				
				if (!empty($new_arr)) {
					$new_tran_item[] = $msg_id;
					continue;	
				}

				if (!empty($old_arr)) {
					$msg_key = $this->_add_lang_msg_key($msg_id);	
					foreach ($old_arr as $lid => $msgstr) {
						$this->_add_lang_msg_item($msg_key, $msgstr, $lid);
					}
					$old_tran_item[] = $msg_id;
				}

				if (empty($old_arr)) {
					$no_tran_item[] = $msg_id;
				}
			}	
		}
		
		if (0 !== count($no_tran_item)) {
			$this->_send_mail($no_tran_item);
		}
	}

	// }}}
    //{{{ public function __construct()
    /*
     * 构造方法
     *
     * @param $args 
     * @return void
     */
    public function __construct($args)
    {
        if (isset($args['t'])) {
            $this->__type = $args['t'];
        }
    }

    //}}}
	// {{{ protected function _find_msgid()

	/**
	 * 查找需要翻译的msgid 
	 * 
	 * @param string $filename 
	 * @access protected
	 * @return void
	 */
	protected function _find_msgid()
	{
		$tran_arr = array_merge($this->_find_member_config(), $this->_find_system_config());	
		return $tran_arr;
	}

	// }}}
	// {{{ protected function _find_member_config()

	/**
	 * 查找成员配置的描述翻译 
	 * 
	 * @access protected
	 * @return array
	 */
	protected function _find_member_config()
	{
		$tran_arr = array();
		$mconfig = em_newt::factory('setting:mconfig');
		$config = $mconfig->config_key();
		foreach ($config['fields'] as $key => $value) {
			foreach ($value as $configs) {
				foreach ($configs as $vkey => $val) {
					if ($key == 'apuser') {
						$key = 'user';
					}
					$tran_arr[] = '_:CONFIG DESC MEM ' . strtoupper($key) . "\n" . $vkey;	
					$default = json_decode($val['default'], true);
					$tmp_arr = $default;
					if (!is_array($tmp_arr) || empty($tmp_arr)) {
						continue;	
					}
					$default_key = array_keys($default);
					foreach ($default_key as $one_key) {
						if (is_string($default[$one_key])) {
							$tran_arr[] = '_:CONFIG DESC MEM ' . strtoupper($key) . "\n" . $vkey . '#' . $one_key;	
						}

						if (is_array($default[$one_key])) {
							foreach ($default[$one_key] as $th_key => $th_val) {
								if (is_string($th_key)) {
									$tran_arr[] = '_:CONFIG DESC MEM ' . strtoupper($key) . "\n" . $vkey . '#' . $one_key . '#' . $th_key;	
								}
							}	
						}
					} 
				}
			}
		}
		$this->__count_lang = count($tran_arr);
		return $tran_arr;
	}

	// }}}
	// {{{ protected function _find_system_config()

	/**
	 * 查找系统配置的描述翻译 
	 * 
	 * @access protected
	 * @return array
	 */
	protected function _find_system_config()
	{
		$config_dynamic = new em_config_dynamic;
		$res_all = $config_dynamic->get_all_config();
		$tran_arr = array();
		foreach ($res_all as $key => $value) {
			$tran_arr[] = '_:CONFIG DESC SYS' . "\n" . $key;	
			if (is_array($value)) {
				foreach ($value as $vkey => $val) {
					if (is_string($vkey) && !isset($this->__ignore_array[$key . '#' . $vkey])) {
						$tran_arr[] = '_:CONFIG DESC SYS' . "\n" . $key . '#' . $vkey;	
					}	
					if (is_array($val)) {
						foreach ($val as $vvkey => $vval) {
							if (is_string($vvkey) && !isset($this->__ignore_array[$key . '#' . $vkey . '#' . $vvkey])) {
								$tran_arr[] = '_:CONFIG DESC SYS' . "\n" . $key . '#' . $vkey . '#' . $vvkey;	
							}	
						}
					}
				}
			}
		}
		$this->__count_lang = count($tran_arr);
		return $tran_arr;
	}

	// }}}
    // {{{ protected function _get_lang()

    /**
     * 获取语言列表
     * @return void
     */
    protected function _get_lang($msgid, $domain_id)
    {
        $url = "{$this->__host}/lang/user/?q=api&_data=msg&did=" . $domain_id . '&key=' . urlencode($msgid);  

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $str = curl_exec($ch);

        $msg_arr = json_decode($str, true);

		$arr = array();
        if (empty($msg_arr)) {
			return $arr;
        }
		
		if (isset($msg_arr['data'])) {
			foreach ($msg_arr['data'] as $value) {
				if (isset($value['msg']['msg_id']) && $msgid == $value['msg']['msg_id']) {
					foreach ($value['lang'] as $vval) {
						$arr[$vval['lang_id']] = $vval['msg_str'];		
					}	
					break;
				}
			}	
		}

		return $arr;
    }

    // }}}
    // {{{ protected function _get_lang_project_id()

    /**
     * 获取项目的ID
     * @param $project_name 
     * @return int 
     */
    protected function _get_lang_project_id($info)
    {
        $url = "{$this->__host}/lang/user/?q=api&_data=project&pname={$info['project_name']}";  

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $str = curl_exec($ch);
        $project_arr = json_decode($str, true);

        if (empty($project_arr)) {
			throw new Exception('get project id failed.');
        }

        return $project_arr[0]['project_id'];
    }

    // }}}
    // {{{ protected function _get_lang_version_id()

    /**
     * 获取语言的版本id
     * 
     * @access protected
     * @return int
     */
    protected function _get_lang_version_id($info)
    {
		$project_id = $this->_get_lang_project_id($info);
        $url = "{$this->__host}/lang/user/?q=api&_data=version&pid=" . $project_id . '&vname=' . $info['version_name'];  

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $str = curl_exec($ch);

        $vdata = json_decode($str, true);

        if (empty($vdata)) {
			throw new Exception('get version id failed.');
        }

        return $vdata[0]['version_id'];
    }

    // }}}
    // {{{ protected function _get_lang_domain_id()

    /**
     * 获取语言的域id
     * @param void
     * @return int
     */
    protected function _get_lang_domain_id($info)
    {
		$version_id = $this->_get_lang_version_id($info);
        $url = "{$this->__host}/lang/user/?q=api&_data=domain&vid=" . $version_id . '&dname=' . $info['domain_name'];  

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $str = curl_exec($ch);

        $ddata = json_decode($str, true);

        if (empty($ddata)) {
			throw new Exception('get version id failed.');
        }

        return $ddata[0]['domain_id'];
    }

    // }}}
    // {{{ protected function _get_lang_category_id()

    /**
     * 获取语言的分类ID
     * @param void
     * @return void
     */
    protected function _get_lang_category_id($info)
    {
		$domain_id = $this->_get_lang_domain_id($info);
        $url = "{$this->__host}/lang/user/?q=api&_data=category&did=" . $domain_id . '&cname=' . urlencode($info['category_name']);  

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $str = curl_exec($ch);

        $cdata = json_decode($str, true);

        //如果分类不存在，则添加一个
        if (empty($cdata)) {
            $cid = $this->_add_lang_category($this->__new_info['category_name']);
            $category_id = $cid; 
        } else {
            $category_id = $cdata[0]['category_id'];
        }
		
		return $category_id;
    }

    // }}}
    // {{{ protected function _add_lang_msg_key()

    /**
     * 获取语言的版本id
     * @param void
     * @return void
     */
    protected function _add_lang_msg_key($key)
    {
        $url = "{$this->__host}/lang/user/?q=api&type=add&_data=msg_key";
        $data = 'did=' . $this->_get_lang_domain_id($this->__new_info) . '&cid=' . $this->_get_lang_category_id($this->__new_info)  . '&mid=' . trim($key);  
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $str = curl_exec($ch);

        $mk_data = json_decode($str, true);

        if ($mk_data['res'] == 0) {
			echo $key . PHP_EOL;
	//		throw new Exception('add msg_key failed.');
        }

        return $mk_data['aid'];
    }

    // }}}
    // {{{ protected function _add_lang_msg_item()

    /**
     * 添加翻译信息 
     * 
     * @param id $msgid 
     * @param string $mstr 
     * @param string $lid 
     * @access protected
     * @return boolean
     */
    protected function _add_lang_msg_item($msgid, $mstr, $lid)
    {
        $url = "{$this->__host}/lang/user/?q=api&type=add&_data=msg_item";
        $data = "did=" . $this->_get_lang_domain_id($this->__new_info) . '&cid=' . $this->_get_lang_category_id($this->__new_info) . '&mstr=' . trim($mstr) . '&lid=' . $lid . '&mid=' . $msgid;  

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $str = curl_exec($ch);

        $mstr_data = json_decode($str, true);

        if ($mstr_data['res'] == 0) {
			echo $key . PHP_EOL;
			//throw new Exception('add msg_item failed.');
        }

        return true;
    }

    // }}}
    // {{{ protected function _add_lang_category()

    /**
     * 获取语言的版本id
     * @param void
     * @return void
     */
    protected function _add_lang_category($cname)
    {
        $url = "{$this->__host}/lang/user/?q=api&type=add&_data=category";
        $data = '&did=' . $this->_get_lang_domain_id($this->__new_info) . '&cname=' . $cname;  

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $str = curl_exec($ch);

        $cat_data = json_decode($str, true);

        if ($cat_data['res'] == 0) {
			return false;
        }

        return $cat_data['cid'];
    }

    // }}}
	// {{{ protected function _send_mail()

	/**
	 * 发信 
	 * 
	 * @param mixed $tran_arr 
	 * @access protected
	 * @return void
	 */
	protected function _send_mail($tran_arr)
	{
		$from = 'admin@test.eyou.net';
		$to = 'haozhongxiu@eyou.net';
		$subject = 'eyouTran [' . $this->__new_info['project_name'] . '/' . $this->__new_info['version_name'] . ']Need to add configuration description information translation';
		$content = '';
		foreach ($tran_arr as $value) {
			$content .= str_replace(PHP_EOL, ' ', $value) . "\n";
		}
		$cmd = '/usr/local/eyou/mail/app/bin/em_sendmail -f ' . $from . ' ' . $to . ' 2&>/dev/null';
		$letter = <<<LETTER
From: {$from}
To: {$to}
Subject: {$subject}

{$content}

LETTER;
		$pipe = popen($cmd, 'w');
		fwrite($pipe, $letter);
		$return_value = pclose($pipe);

		return !$return_value;
	}

	// }}}
	// }}}
}

// {{{ do 
$help = <<<HELP

This is a command line PHP script with one option.

Usage:
    $argv[0] -f file -d new_domain_name:old_domain_name -c new_categeroy_name [-p project_name] [-v version_name]

    -t: 类型
        如: member,system

    
Valid options:
    -h: 获取帮助信息
\n
HELP;
// }}} do

$opt_str = "t:h::";

$argments = getopt($opt_str);

if (isset($argments['h'])) {
	echo $help;
    exit(1);
}

try {
	$test = new tran($argments);
	$test->run();
} catch (Exception $e) {
	echo $e->getMessage();	
}
