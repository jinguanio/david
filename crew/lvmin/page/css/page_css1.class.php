<?php
 /*
 * class page_css1
 * 显示第一种样式
 */

class page_css1 extends css_factory
{
	/**
     * 分页样式输出
     * @type $type
     * @return string
     */
    public function display()
    {
            
        //$nav array 页码导航
         
        $nav = array();
        $nav[] = "<span>" . $this->page . '</span>';
    
        for($left = $this->page-1,$right = $this->page+1;($left>=1||$right<=$this->total_page) && count($nav)<$this->display_page_num;){
        
            if($left>=1){
                array_unshift($nav, '[' . $this->render_url($left,$left) . ']');
                $left-=1;
            }
            if($right<=$this->total_page){
                array_push($nav, '[' . $this->render_url($right,$right) . ']');
                $right+=1;
            }
            
        }
        
        /**
         * 将首页 末页 上下页 的链接页码放入$nav数组中
         */
        array_unshift($nav, $this->first_page() . $this->previous_page());
        array_push($nav, $this->next_page() . $this->last_page());
        
        echo implode('', $nav);
        
    }
}
