<?php

define('EYOUM_EXEC_ROOT', true);
require_once 'conf_global.php';
require_once PATH_EYOUM_LIB . 'em_newt.class.php';

$newt_m = em_newt::factory('setting:mconfig');
$mem_arr = $newt_m->config_key();
$spl_arr = new RecursiveArrayIterator($mem_arr['fields']);
// {{{ select
//foreach ($spl_arr as $key => $value) {
//	if ($spl_arr->hasChildren()) {
//		$reg_arr = new RegexIterator($spl_arr->getChildren(), '/^plu/');
//		$reg_arr->setFlags(RegexIterator::USE_KEY);
//		foreach ($reg_arr as $key => $value) {
//			if ($reg_arr->hasChildren()) {
//				$c_reg_arr = new RegexIterator($reg_arr->getChildren(), '/^plu/');
//				$c_reg_arr->setFlags(RegexIterator::USE_KEY);
//				foreach ($c_reg_arr as $key => $value) {
//					echo $key," -<< ";	
//					echo $reg_arr->key()," -<< ";	
//					echo $spl_arr->key() , "\n";
//				}
//			}
//		}
//	}
//}

$test = new RecursiveIteratorIterator(new RecursiveArrayIterator($mem_arr['fields']), RecursiveIteratorIterator::SELF_FIRST); 
$test->setMaxDepth(1);

foreach ($test as $key => $value) {
	if (is_array($value)) {
		var_export($value); 
		echo "\n";
	}
}
exit;




print_r(iterator_to_array($test));
foreach ($spl_arr as $key => $value) {
	if ($spl_arr->hasChildren()) {
		$reg_arr = $spl_arr->getChildren();
		foreach ($reg_arr as $key => $value) {
			if ($reg_arr->hasChildren()) {
				$c_reg_arr = new RegexIterator($reg_arr->getChildren(), '/plugin/');
				$c_reg_arr->setFlags(RegexIterator::USE_KEY);
				foreach ($c_reg_arr as $key => $value) {
					echo $spl_arr->key() , "  --> ";
					echo $reg_arr->key()," --> ";	
					echo $key,"\n";	
				}
			}
		}
	}
}
/*
*/
// }}}

// {{{ replace
$arr_iterator = new ArrayIterator(array('test1', 'test2', 'test3')); 
$reg_iterator = new RegexIterator($arr_iterator, '/^(test)(\d+)/', RegexIterator::REPLACE); 
$reg_iterator->replacement = '$2:$1'; 
       
foreach ($reg_iterator as $key => $value) {
	echo $key,'||', $value,"\n";	
}
print_r(iterator_to_array($reg_iterator)); 
// }}}
