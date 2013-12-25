<?php

/**
 * class page_css3
 *
 * 显示第三种样式
 */

class page_css3 extends css_factory
{

	/**
	 * display() 展示分页功能
	 * return String 返回展示语句
	 */
	public function display(){

		/*
		 * $nav array 页码导航
		 */
		$nav = array();

		/**
     	 *显示分页信息 放入$nav数组中
		 */
		$info = <<<PAGE
				总计<span id="total">$this->total</span>记录,
				分为<span id="">$this->total_page</span>页,
				当前第<span id="">$this->page</span>页,
				每页<span id="">$this->per_page_num</span>条记录 
PAGE;
		
		$nav[] = "<span style='color:#f00;'><strong>" . $this->page . "&nbsp;". '</strong></span>';
		
		for($left = $this->page-1,$right = $this->page+1;($left>=1||$right<=$this->total_page) && count($nav)<$this->display_page_num;){
		
			if($left>=1){
				array_unshift($nav, $this->render_url($left,$left) . "&nbsp;");
				$left-=1;
			}
			if($right<=$this->total_page){
				array_push($nav, $this->render_url($right,$right) . "&nbsp;");
				$right+=1;
			}
		}
		/**
		 * 将首页 末页 上下页 的链接页码放入$nav数组中
		 */
		array_unshift($nav, $this->first_page() . "&nbsp;" .  $this->previous_page());
        array_push($nav, $this->next_page(). "&nbsp;" . $this->last_page());
        
		array_unshift($nav, "$info");
		
		$jump = " 跳到<input type='text' name='jump' max_page=" .$this->total_page . " style='width:30px;height:20px;'>页 
		<input type='button' value='确定' id='jump_sure' style='width:40px;height:23px;' onclick='jump();'>";	

		echo implode('', $nav) . $jump;
	}
}
