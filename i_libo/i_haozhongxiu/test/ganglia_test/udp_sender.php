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


	$str = xdr_uint32(58);
	$str .= xdr_string('lan-100.120:lan-100.120');
	$str .= xdr_string($name);
	$str .= xdr_uint32(1);
	$str .= xdr_string($typename);
	$str .= xdr_string($name);
	$str .= xdr_string($units);
	$str .= xdr_uint32($slopenum);
	$str .= xdr_uint32($tmax);
	$str .= xdr_uint32($dmax);
	$str .= chr(0);
	$str .= chr(0);
	$str .= chr(0);
	$str .= chr(1);

	$str .= xdr_string('SPOOF_HOST');
	$str .= xdr_string('lan-100.120:lan-100.120');
	return $str;
}

echo hex2bin('00000085000000176c616e2d3130302e3132303a6c616e2d3130302e3132300000000003666f6f000000000100000002257300000000000362617200');

echo "\n";
echo hex2bin('00000080000000176c616e2d3130302e3132303a6c616e2d3130302e3132300000000003666f6f000000000100000006737472696e67000000000003666f6f000000000473656373000000030000003c0000001e000000010000000a53504f4f465f484f53540000000000176c616e2d3130302e3132303a6c616e2d3130302e31323000');
echo "\n";
$msg =  makexdr("foo", 'bar', 'string', 'secs', 'both', 60, 30);
echo $msg . "\n";
echo bin2hex($msg);
?>
