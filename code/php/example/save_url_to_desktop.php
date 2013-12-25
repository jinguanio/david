<?php
$Shortcut = "[InternetShortcut]
URL=http://mail.eyou.net/
IDList=[{000214A0-0000-0000-C000-000000000046}]
Prop3=19,2";
Header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=".iconv('utf-8', 'gb2312','亿邮').".url;");
echo $Shortcut;

