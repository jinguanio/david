<?php

$fp = new SplFileObject('delete_file');

foreach ($fp as $value) {
	if ('' === trim($value)) {
		continue;	
	}
	list($file_type, $file_name) = explode(' ', $value);
	$file_path = "/usr/local/eyou/mail82/" . trim($file_name);
	if ('d' == trim($file_type)) {
		rmdir($file_path);
		continue;	
	}

		
	if (file_exists($file_path)) {
		unlink($file_path);	
	} 
}

