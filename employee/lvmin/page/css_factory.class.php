<?php

/**
 * 分页类的实现
 * css_factory 得到分页类的基本属性 和 对应的某页的链接地址
 * 
*/

class css_factory
{

	/**
	 * total 数据的总条数
	 */ 
	protected $total;   

	/**
	 * per_page_num 每页显示的数据条数
	 */
	protected $per_page_num;  

	/**
	 * page 当前页码
	 */
	protected $page;     

	/**
	 * url 当前的链接地址
	 */
	protected $url = '';  

	/**
	 * total_page 总页数
	 */
	public $total_page; 

	/**
	 * display_page_num 每页显示的页码的个数
	 */
	protected $display_page_num; 

	/**
         * @var string specifies which HTTP method to use
         * @access protected
         */
        protected $http_method  = 'GET';

        /**
	 * offset int 初始化分页类的属性数组
	 */
	public $offset ;

	/**
	 * init_page_arr Array 初始化分页类的属性数组
	 */
	public $init_page_arr = array();

	/**
	 * init_css()方法 初始化分页类的数据
	 */
	public function init_css($init_page_arr=false)
	{
		if (!is_array($init_page_arr)) {
            echo 'this init_css() function Parameter 1 expected to be Array. Incorrect value given.';
            return false;
        }
        
        $page = isset($_GET['page'])?$_GET['page']:1; //获取当前页

		$this->total = intval(isset($init_page_arr['total']) ? $init_page_arr['total'] : 0);
		$this->per_page_num = intval(isset($init_page_arr['per_page_num']) ? $init_page_arr['per_page_num'] : 5);
		$this->display_page_num = intval(isset($init_page_arr['display_page_num']) ? $init_page_arr['display_page_num'] : 5);

        $this->http_method = isset($init_page_arr['http_method']) ? $init_page_arr['http_method'] : 'GET';

		//总页数
		$this->total_page = ceil($this->total/$this->per_page_num);

		//获取当前页面 {{{ $page
		if (!empty($init_page_arr['page'])) {
			$this->page = intval($init_page_arr['page']);
		} else {
			$this->page = isset($_GET['page']) ? intval($_GET['page']):1;
		}
		$this->page = $this->page <= 0 ? 1 : $this->page;
	
		if(!empty($this->total_page) && $this->page > $this->total_page)
		{
			$this->page = $this->total_page;
		}
		// }}} 

        //地址栏的参数保存
        $uri = $_SERVER['REQUEST_URI'];
        $parse = parse_url($uri);

        $param = array();
        if (isset($parse['query'])) {
            parse_str($parse['query'],$param);
        }
        //不管有没有都unset掉$param下的page单元，因为page是要算出来的
        unset($param['page']);

        //这么是为了将param中的page去掉

        $url = $parse['path'] . '?';
        if (!empty($param)) {
            $param = http_build_query($param);//得到去掉page的参数
            $url = $url . $param . '&';//得到完整路径
        }

        $this->url = $url;

		//获取偏移量 即开始的行数
		$this->offset = $this->per_page_num * ($this->page - 1);
	
	}	

	//获取当前的链接地址
	public function get_url($page)
	{
		return $this->url . 'page=' . $page;
	}


	//获取首页
	public function first_page($text = '首页')
    {
 		return $this->render_url('1', $text);
    }
    //获取末页
	public function last_page($text = '末页')
    {
 		return $this->render_url($this->total_page, $text);
    }
    //获取上一页
	public function previous_page($text = '上一页')
    {
        $pageprev = ($this->page-1>=1)? ($this->page-1):1;
 		
    	return $this->render_url($pageprev, $text);
    	
    }
    //获取下一页
	public function next_page($text = '下一页')
    {
        $pagenext = ($this->page+1<=$this->total_page)? ($this->page+1):$this->total_page;
 		
        return $this->render_url($pagenext, $text);
    }

    /**
     * Renders a link using the appropriate method   使用适当的方法渲染链接
     * @return string The link in string form  得到正确的链接地址
     */
    public function render_url($page,$text)
    {           
        if ($this->http_method == 'GET') {
            return '<a href="' .$this->get_url($page) . '">' . $text . '</a>' ;
        } elseif ($this->http_method == 'POST') {
            if(!empty($_GET)){
                $this->url = $this->url . http_build_query($_GET);
            }
            $url = $this->url;
            
            //处理$data 要提交的数据
            $data = array();
            $data = $_POST;

            return '<a href="javascript:void(0)" onclick="generateFormOnclick(\'POST\',' .  $page .')">' . $text . '</a>';
        }
        return '';
    }

}




