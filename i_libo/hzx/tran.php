<?php
class tran 
{
	// {{{ members

	/**
	 * __filename 
	 * 
	 * @var string
	 * @access protected
	 */
	protected $__filename = '';

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
		'category_name' => 'test',
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
		'version_name'  => 'dev_8.1.0',
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
		$tran_arr = $this->_find_msgid($this->__filename);
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

				if (empty($old_arr) && empty($new_arr)) {
					$no_tran_item[] = $msg_id;
				}
			}	
		}
		
		echo '在新的项目该域中存在的翻译：' . PHP_EOL;
		echo implode($new_tran_item, PHP_EOL) . PHP_EOL;
		echo '在旧的项目该域中存在的翻译：' . PHP_EOL;
		echo implode($old_tran_item, PHP_EOL) . PHP_EOL;
		echo '不存在的翻译：' . PHP_EOL;
		echo implode($no_tran_item, PHP_EOL) . PHP_EOL;
		echo '实际翻译个数：' . $real_count . PHP_EOL;
		echo '参考翻译个数：' . $this->__count_lang . PHP_EOL;
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
        $this->__filename = $args['f'];

        if (isset($args['p'])) {
            $this->__new_info['project_name'] = $args['p'];
            $this->__old_info['project_name'] = $args['p'];
        }

        if (isset($args['v']) && (false !== strpos($args['v'], ':'))) {
			list($this->__new_info['version_name'], $this->__old_info['version_name']) = explode(':', $args['v']);
        }

        if (isset($args['d']) && strpos($args['d'], ':')) {
			list($this->__new_info['domain_name'], $this->__old_info['domain_name']) = explode(':', $args['d']);
        }

        if (isset($args['c'])) {
            $this->__new_info['category_name'] = $args['c'];
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
	protected function _find_msgid($filename)
	{
		$des_string = $this->_replace_gettext($filename);
		$tran_arr = array();
		$file_type = $this->_get_file_type($filename);
		switch ($file_type) {
			case 'php':
				$pattern = "/lang\((.*)\)/isU";
				if (!preg_match_all($pattern, $des_string, $return_arr)) {
					return false;
				}
				foreach ($return_arr[1] as $value) {
					$tran_arr[] = trim($value, '\'"');	
				}

				$pattern_lang = "/lang\(/isU";
				if (!preg_match_all($pattern_lang, $des_string, $return_count)) {
					return false;
				}
				$this->__count_lang = count($return_count[0]);
				break;		
			case 'html':
				$pattern = '/{{[\'\"]{1}(.*)[\'\"]{1}\|lang}}/isU';
				if (!preg_match_all($pattern, $des_string, $return_arr)) {
					return false;
				}
				$tran_arr = $return_arr[1];

				$pattern_lang = "/\|lang/isU";
				if (!preg_match_all($pattern_lang, $des_string, $return_count)) {
					return false;
				}
				$this->__count_lang = count($return_count[0]);
				break;		
			case 'js':
				foreach (array("\"", "'") as $value) {
					$pattern = '/L\(' . $value . '(.*)(?<!\\\\)' . $value .'\)/sU';
					if (!preg_match_all($pattern, $des_string, $return_arr)) {
						continue;
					}
					$tran_arr = array_merge($tran_arr, $return_arr[1]);
				}

				$pattern_lang = "/L\(/isU";
				if (!preg_match_all($pattern_lang, $des_string, $return_count)) {
					return false;
				}
				$this->__count_lang = count($return_count[0]);
				break;		
		}
		return $tran_arr;
	}

	// }}}
	// {{{ protected function _replace_gettext()

	/**
	 * 将getext()替换成lang() 
	 * 
	 * @param string $filename 
	 * @access protected
	 * @return void
	 */
	protected function _replace_gettext($filename)
	{
		$str_src = file_get_contents($filename);
			
		$file_type = $this->_get_file_type($filename);
		switch ($file_type) {
			case 'php':
				$rev_str = str_replace('gettext(', 'lang(', $str_src);
				break;		
			case 'html':
				$pattern = '/({{[\'\"]{1}.*)(?=\|gettext)(.*}})/isUe';
				$rev_str = preg_replace($pattern, "str_replace(array('gettext', '\\\\\\'), array('lang', ''), '\\0')",  $str_src);
				break;		
			case 'js':
				$rev_str = $str_src;
				break;
		}

		rename($filename, $filename . '.bak');
		file_put_contents($filename, $rev_str);

		return $rev_str;	
	}

	// }}}	
	// {{{ protected function _get_file_type()

	/**
	 * 获取给定文件类型 
	 * 
	 * @param string $filename 
	 * @access protected
	 * @return string
	 */
	protected function _get_file_type($filename)
	{
		if (!file_exists($filename)) {
			throw new Exception('filename new exists');
		}

		$path_parts = pathinfo($filename);
		return $path_parts['extension'];
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
			echo $msgid . PHP_EOL;
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
	// }}}
}

// {{{ do 
$help = <<<HELP

This is a command line PHP script with one option.

Usage:
    $argv[0] -f file -d new_domain_name:old_domain_name -c new_categeroy_name [-p project_name] [-v version_name]

    -f: 文件
        如: compose.html,

    -p: 项目名
        如:eyou_mail5 
        默认为:eyou_mail5

    
    -v: 版本名
        如:dev_8.2.0 
        默认为: dev_8.2.0

    -d: 需发布的域名
        如:app_mailadmin:eyou_mail, app_mailadmin_js:em_admin_js

    -c: 分类名
        如:domain等

    
Valid options:
    -h: 获取帮助信息
\n
HELP;
// }}} do

$opt_str = "f:v::p::d:c:h::";

$argments = getopt($opt_str);

if (empty($argments) 
	|| isset($argments['h'])
	|| !isset($argments['f'])) {
	echo $help;
    exit(1);
}

try {
	$test = new tran($argments);
	$test->run();
} catch (Exception $e) {
	echo $e->getMessage();	
}
