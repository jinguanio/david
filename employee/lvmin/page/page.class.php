<?php

/**
 * 分页类的实现  
 * page.class.php
 * 单例模式
 */

require('./css_factory.class.php');

require('./css/page_css1.class.php');
require('./css/page_css2.class.php');
require('./css/page_css3.class.php');



class operation
{	
	/**
	 * 单例对象 $instance
	 */
	private static $instance = null;

	/**
	 * getInstance()方法 获得单例对象
	 */
	public static function getInstance()
	{
		if(!(self::$instance instanceof self))
		{
			self::$instance =  new self;
		}
		return self::$instance;
	}

	/**
	 * 分页样式的实例对象 $obj 
	 */
	private static $obj;

	/**
	 * operation_page() 选择样式 操作分页类
	 * @param type int 选择的第几个的样式
	 * return css 实例对象
	 */
	public static function operation_page($type){
		try{
			$error = "Please input the number between 1 and 3 carefully";
			switch($type){
				case '1':
					self::$obj = new page_css1();
					break;
				case '2':
					self::$obj = new page_css2();
					break;
				case '3':
					self::$obj = new page_css3();
					break;
				default:
					throw new Exception($error);
			}
			return self::$obj;
		}catch (Exception $e) {  
            echo 'Caught exception: ',  $e->getMessage(), "\n";  
            return;
        }  
	}

}

?>
<style type="text/css">
	a{
		text-decoration: none;
	}
</style>

<script type="text/javascript">
/**
 *  用来在post提交的时候 生成隐藏表单 提交post数据
 */
function generateFormOnclick(http_method,page){
    
    var form = document.createElement("form");

    //表示该有的字段名 循环该对象 若id和这里面的一样 是该有的字段名和字段值，并将其赋值给input
 	var ids = {'name':1}
 	
 	for(var i in ids){
 		if(document.getElementById(i)){
 			var input = document.createElement("input");
			input.type = "hidden";
			input.name = i
			input.value = document.getElementById(i).value
 		}
 	}
	

    form.appendChild(input);

    form.method = http_method;

    var url= window.location.href;
    
	var querySymbolIndex = url.lastIndexOf('?');

	if(querySymbolIndex == -1){
		form.action = url + '?page=' + page;
	}
	else{
		var pageIndex = url.indexOf('page',querySymbolIndex);
		
		if(pageIndex == -1){
			form.action = url + '&page=' + page;
		}else{
			form.action = url.replace(/page=[1-9][0-9]*/,'page='+page);
		}
	}
    
    document.getElementsByTagName("body")[0].appendChild(form);
    form.submit(); 
}

</script>


<script type="text/javascript">
	
	/**
	 * 样式三中的跳转函数  输入数字jumpPageNum 点击确定 即可跳转
	 * jump() 跳转函数
	 */
	function jump(){
		var jumpPageNum = parseInt(document.getElementsByName('jump')[0].value);
		var maxPage = parseInt(document.getElementsByName('jump')[0].getAttribute('max_page'));

		/*如果输入的不是数字，默认是1*/
		if(isNaN(jumpPageNum)){
			jumpPageNum = 1;
		}

		/*若输入的数字大于最大页码，默认跳转到最大页码*/
		if(jumpPageNum >= maxPage){
			jumpPageNum = maxPage;
		}
		
		generateFormOnclick('POST',jumpPageNum);
		
	}
</script>

<script>
/**
 * 样式二中的跳转函数
 * 在select选择框中选择页码 jumpNum 点击跳转
 *
 */
function selectJump(jumpNum){
	
	generateFormOnclick('POST',jumpNum);
	
}
</script>
