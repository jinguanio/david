<?php

function merge_array_recursive($array1, $array2)
{
	foreach ($array2 as $key1 => $value) {
		if (is_array($value)) {
			if (isset($array1[$key1])) {
				$array1[$key1] = merge_array_recursive($array1[$key1], $value);
			} else {
				var_dump($key1);
				$array1[$key1] = $value;	
			}
		} else {
			$key_1 = array_search($value, $array1);
			if (is_numeric($key1) && (false === $key_1)) {
				$array1[] = $value;	
			} else {
				$array1[$key_1] = $value;	
			}
		}
	}

	return $array1;
}

$array1 = array(
2,3,4, 'ss' => array(2, 3,4,),
);

$array2 = array(
1,2,3, 'ss' => array(2, 1,), 'www' => array(11),
);
var_dump(merge_array_recursive($array1, $array2));
var_dump(array_merge($array1, $array2));
