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
	$str .= xdr_string($typename);
	$str .= xdr_string($name);
	$str .= xdr_string($value);
	$str .= xdr_string($units);
	$str .= xdr_uint32($slopenum);
	$str .= xdr_uint32($tmax);
	$str .= xdr_uint32($dmax);
	return $str;
}

function gmetric_send($gm, $name, $value, $typename, $units, $slope, $tmax, $dmax)
{
	$msg  = makexdr($name, $value, $typename, $units, $slope, $tmax, $dmax);
	stream_socket_client("udp://")
}

function gmetric_close($gm)
{
	if ($gm['protocol'] == 'udp') {
		return fclose($gm['socket']);
	} else if ($gm['protocol'] == 'multicast') {
		return socket_close($gm);
	}
}

$gm = gmetric_open('localhost', 8651, 'udp');
gmetric_send($gm, 'foo', 'bar', 'string', '', 'both', 60, 0);
gmetric_close($gm);
