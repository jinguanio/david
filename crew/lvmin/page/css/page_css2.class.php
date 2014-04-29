<?php

/**
 * class page_css2
 *
 * 显示第二种样式
 */

class page_css2 extends css_factory
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

		$nav[] = "<span style='font-weight:bold;'><strong>" . $this->page. "&nbsp;" . '</strong></span>';

		for($left = $this->page-1,$right = $this->page+1;($left>=1||$right<=$this->total_page) && count($nav)<$this->display_page_num;){
		
			if($left>=1){
				array_unshift($nav, $this->render_url($left,$left). "&nbsp;");
				$left-=1;
			}
			if($right<=$this->total_page){
				array_push($nav, $this->render_url($right,$right). "&nbsp;" );
				$right+=1;
			}
	
		}
		
		/**
		 * 将首页 末页 上下页 的链接页码放入$nav数组中
		 */
		
		array_unshift($nav, $this->first_page('<<') . "&nbsp;" .  $this->previous_page('<'));
        array_push($nav, $this->next_page('>'). "&nbsp;" . $this->last_page('>>'));
		
		//页码列表
		if($this->http_method == 'POST'){
	 	$selector=<<<PAGE
	 		<select id="pages" onchange="selectJump(this.value);">
PAGE;
		}elseif($this->http_method == 'GET'){
			$selector=<<<PAGE
	 		<select id="pages" onchange="window.location.href='$this->url'+'page='+this.value">
PAGE;
		}
		/**
		 * $selector 循环页码 显示在一个下拉菜单中
		 */
		for($p=1;$p<=$this->total_page;$p++){
			if($this->page==$p){
				$selector .= <<<PAGE
	 			<option value="$p" selected>$p</option>
PAGE;
			}else{
				$selector .=<<<PAGE
				<option value="$p">$p</option>
PAGE;
			}
		}
		
		$selector .='</select>';

		echo implode('', $nav) . $selector;
	}
}


