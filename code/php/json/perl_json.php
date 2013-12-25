<?php
$str = '你好';

//exec('/tmp/jsontest/json.pl >/tmp/jsontest/2', $out, $ret);
//var_dump($out);
function utf8_unicode($name){
    $name = iconv('UTF-8', 'UCS-2', $name);
    $len  = strlen($name);
    $str  = '';
    for ($i = 0; $i < $len - 1; $i = $i + 2){
        $c  = $name[$i];
        $c2 = $name[$i + 1];
        if (ord($c) > 0){   //两个字节的文字
            $str .= '\u'.base_convert(ord($c), 10, 16).str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
            //$str .= base_convert(ord($c), 10, 16).str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
        } else {
            $str .= '\u'.str_pad(base_convert(ord($c2), 10, 16), 4, 0, STR_PAD_LEFT);
            //$str .= str_pad(base_convert(ord($c2), 10, 16), 4, 0, STR_PAD_LEFT);
        }
    }
    $str = strtoupper($str);//转换为大写
    return $str;
}
function unicode_decode($name)
{
    $name = strtolower($name);
    // 转换编码，将Unicode编码转换成可以浏览的utf-8编码
    $pattern = '/([\w]+)|(\\\u([\w]{4}))/i';
    preg_match_all($pattern, $name, $matches);
    if (!empty($matches))
    {
        $name = '';
        for ($j = 0; $j < count($matches[0]); $j++)
        {
            $str = $matches[0][$j];
            if (strpos($str, '\\u') === 0)
            {
                $code = base_convert(substr($str, 2, 2), 16, 10);
                $code2 = base_convert(substr($str, 4), 16, 10);
                $c = chr($code).chr($code2);
                $c = iconv('UCS-2', 'UTF-8', $c);
                $name .= $c;
            }
            else
            {
                $name .= $str;
            }
        }
    }
    return $name;
}

$str = trim(file_get_contents('/tmp/jsontest/1'));
var_dump(mb_detect_encoding($str));
//$str = iconv('utf-32', 'utf-8', $str);
//$ret = json_encode($str);
//var_dump($ret);

//var_dump(iconv_get_encoding());
//var_dump(json_decode("\"\u4f60\u597d\"", true));
$ret = json_decode("$str", true);
$ret = $ret['data']['title'];
$ret = iconv('ASCII', 'UTF-8'$ret);
var_dump(mb_detect_encoding($ret));
var_dump($ret);

