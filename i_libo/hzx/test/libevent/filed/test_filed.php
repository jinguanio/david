<?php

$fp = stream_socket_client("127.0.0.1:10001", $errno, $errstr,  STREAM_CLIENT_ASYNC_CONNECT |STREAM_CLIENT_CONNECT);

//$index_data = str_repeat('a', 1024);
//$content_data = str_repeat('a', 50 * 1024);
//
//$header = array(
//	'operation' => 1,
//	'index_length' => strlen($index_data),
//	'content_length' => strlen($content_data),
//);
//
//$header = json_encode($header);
//$header .= "\r\n\r\n";
//stream_socket_sendto($fp, $header);
//
//get_response_info($fp);
//stream_socket_sendto($fp, $index_data);
//stream_socket_sendto($fp, $content_data);
//var_dump(get_response_info($fp));

$file_id = "201304/1/3/517a3544195131224900_00_00-201304/c/c/517a3544195131224900_00_00";
$header = array(
	'operation' => 5,
	'file_name' => $file_id,
);

$header = json_encode($header);
$header .= "\r\n\r\n";
stream_socket_sendto($fp, $header);

var_dump(get_response_info($fp));


function get_response_info($fp)
{
	$data  = '';
	while(!feof($fp)) {
		$line = fgets($fp, 4096);
		// 超时用event的读写超时
		if ("\r\n" == $line) {
			break;	
		}
		$data .= $line;
	}

	return json_decode(trim($data), true);
}
