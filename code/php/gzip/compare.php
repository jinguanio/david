<?php
echo 'eminfo data', PHP_EOL;
$str = '{"parter_id":"0123456789","hid":"2524662888","pname":"imap_svr","data":{"ret":"str","res":"0","detail":"[{\"color\":null,\"title\":\"\",\"val\":\"check list: 127.0.0.1:143\"},{\"color\":null,\"title\":\"\",\"val\":\"\"},{\"color\":null,\"title\":\"\",\"val\":\"connect [127.0.0.1:143] return welcome banner\"},{\"color\":null,\"title\":\"\",\"val\":\"[* OK IMAP4rev1 server starting on test.eyou.net (eYou MUA v8.1.0)] (0.00211096 seconds)\"},{\"color\":null,\"title\":\"\",\"val\":\"imap_user or imap_pass not defined, imap login test skip\"},{\"color\":null,\"title\":\"\",\"val\":\"\"},{\"color\":null,\"title\":\"\",\"val\":\"\"}]","level":"ok","summary":"1/1 imap check success ","auto":"[{\"color\":\"\",\"title\":\"\",\"val\":\"auto handle is disabled. Nothing to do\"}]","title":"IMAP SVR OK ","extra":"","snap":"","act":"{\"sms\":{\"fail\":[],\"succ\":[]},\"mail\":{\"fail\":[],\"succ\":[]}}"},"type":"post","hname":"dev__esop.eyou.net" ,"job":["qMn31934","1389166605"]}';
$l = strlen($str);
echo 'total: ', $l, ' byte', PHP_EOL;
$gzdata = gzencode($str, 6);
$l2 = strlen($gzdata);
echo 'compress: ', $l2, 'byte', PHP_EOL;
$rate = (100-round($l2/$l*100)) . '%';
echo 'rate: ', $rate, PHP_EOL;
echo 'result: ', round(50*35*$rate/100,2) . 'k', PHP_EOL, PHP_EOL;

echo 'ganglia data', PHP_EOL;
exec('nc localhost 8649 >/tmp/xml', $out, $ret);
$xml = file_get_contents('/tmp/xml');
$l = strlen($xml);
echo 'total: ', $l, ' byte', PHP_EOL;
$gzdata = gzencode($xml, 6);
$l2 = strlen($gzdata);
echo 'compress: ', $l2, 'byte', PHP_EOL;
$rate = (100-round($l2/$l*100)) . '%';
echo 'rate: ', $rate, PHP_EOL;
echo 'result: ', round($l2/1024, 2) . 'k', PHP_EOL;

