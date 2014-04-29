<?php

function xdr_uint32($val)
{
	return pack("N", intval($val));
}

function xdr_string($str)
{
	$len = strlen(strval($str));
	$pad = (4 - $len % 4) % 4;
	return xdr_uint32($len) . $str . pack("a$pad", "");
}

function makexdr($name, $value, $typename, $units, $slope, $tmax, $dmax)
{

	if ($slope == "zero") {
		$slopenum = 0;
	} else if ($slope == "positive") {
		$slopenum = 1;
	} else if ($slope == "negative") {
		$slopenum = 2;
	} else if ($slope == "both") {
		$slopenum = 3;
	} else {
		$slopenum = 4;
	}

	$str  = xdr_uint32(0);
	$str .= xdr_string('lan-100.120:lan-100.120');
	$str .= xdr_string($name);
	$str .= xdr_uint32($slopenum);
	$str .= xdr_string($typename);
	$str .= xdr_string($name);
	$str .= xdr_string($value);
	$str .= xdr_string($units);
	$str .= xdr_uint32($tmax);
	$str .= xdr_uint32($dmax);
	$str .= xdr_string("\nSPOOF_HOSTlan-100.120:lan-100.120");
	return $str;
}

$msg = makexdr("foo", "23", "uint32", "sec", "both", 60, 0);
echo $msg . "\n";
echo bin2hex($msg);
echo "\n----------------\n";
$bin = '00000080000000176c616e2d3130302e3132303a6c616e2d3130302e3132300000000003666f6f00000000010000000675696e743332000000000003666f6f000000000373656300000000030000003c00000000000000010000000a53504f4f465f484f53540000000000176c616e2d3130302e3132303a6c616e2d3130302e31323000';
echo pack('H*', $bin) . "\n";
echo $bin;

//$fp =  stream_socket_client("udp://127.0.0.1:8649", $errno, $errstr, 3);
//$rev = pack('H*', '00000080000000176c616e2d3130302e3131343a6c616e2d3130302e3131340000000003666f6f000000000100000006737472696e67000000000003666f6f0000000000000000030000003c00000000000000010000000a53504f4f465f484f53540000000000176c616e2d3130302e3131343a6c616e2d3130302e31313400');
//echo $rev;
//
//$rev1 = stream_socket_sendto($fp, $rev); 
//var_dump($rev1);
//$arr = stream_socket_recvfrom($fp, 1024);
//var_dump($arr);
