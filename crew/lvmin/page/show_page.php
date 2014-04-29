<?php

require('./page.class.php');


$conn = mysql_connect('localhost','root','');

mysql_select_db('test');
mysql_query('set names utf8');

$where_str = 'where 1';

$http_method = $_SERVER['REQUEST_METHOD'];
	
switch($_SERVER['REQUEST_METHOD'])
{
	case 'GET': 
	$name = isset($_GET['name']) ? $_GET['name']:'';
		if($name == ''){
			$where_str .= '';
		}else{
			$where_str .= " and name='$name'";
		}
		break;
	case 'POST': 
	$name = isset($_POST['name']) ? $_POST['name']:'';
		if($name == ''){
			$where_str .= '';
		}else{
			$where_str .= " and name='$name'";
		}
		break;	
	default:
		$where_str .= '';
}

$sql = "SELECT count(*) from page_test $where_str";

$res = mysql_query($sql);
$row = mysql_fetch_row($res);
$total = $row[0];           //得到总个数


//测试分页类

$oper = operation::getInstance();    //获取单例对象
$obj = $oper::operation_page('1');   //得到分页的实例对象

$init_arr = array(
		'total'=>$total,       //总条数 必须
		'display_page_num'=>5, //每页展示的页码的个数 可选 
		
		'per_page_num'=>5,      // 每页显示的条数 可选
		'http_method' => $http_method   //提交方式 可选 默认是GET方式
		);//初始化分页数组

$per_page_num = $init_arr['per_page_num'];

$obj->init_css($init_arr);             //得到分页的样式 可在页面展示

$offset = $obj->offset; //获取偏移量 在sql语句中查询数据

$sql = "SELECT * from page_test $where_str limit $offset,$per_page_num" ; 

$res = mysql_query($sql);
$list = array();

while($row = mysql_fetch_assoc($res)){
	$list[] = $row;
}

include('./show.html');
?>

<html>
	<head>
		<title>展示页</title>
	</head>
	<body>
		<form action="show_page.php" method='post' name='search' id="search">
			<p>查询姓名：<input type="text" name='name' id='name' value="<?php echo $name;?>"></p>
			<p><input type="submit" value='查询'><input type="reset" value='取消'></p>
		</form>
		<table border='1' cellspacing='0' width="400">
			<tr><th>id</th><th>name</th></tr>
			<?php 
			 	foreach($list as $v){
			 ?>
			<tr><td><?php echo $v['id'];?></td><td><?php echo $v['name'];?></td></tr>
			<?php } ?>
		</table>
	</body>
</html>
<?php $obj->display(); ?>



