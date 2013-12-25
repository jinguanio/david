<?php

$str = <<<EOD

<div class="yyhf" quote="yes" contenteditable="false"><div style="display:none" id="40256181_破包包"></div><dl><dt class="fl">引用：<a class="blue" href="http://space.soufun.com/40256181/index/" target="_blank">破包包</a>在2013-11-22 15:55:08写道：</dt><dd class="fr"><a href="http://hanlinguojicheng.soufun.com/bbs/2510742333~-1/182866558_182867036.htm#182867036" class="blue">61楼</a></dd><><p>土豪妈的就是土豪</p></div><p>&nbsp;2222</p>

EOD;


$parrent = '/<div class=\"yyhf\" quote=\"yes\" contenteditable=\"false\">(.*)<\/div>/im';
$str = preg_match_all($parrent, $str, $match);
var_dump($str);
var_dump($match);
