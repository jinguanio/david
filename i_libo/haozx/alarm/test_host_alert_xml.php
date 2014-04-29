<?php

// {{{ xml
$test = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<!--  vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: -->
<monalert>

<hostgroups>
    <hostgroup>
        <name>hg_eyou_wan</name>
        <display_name>eYou 外网</display_name>
        <desc>eYou 外网</desc>
        <members>
            <member>lan-100.114</member>
        </members>
    </hostgroup>
</hostgroups>
<hosts>
	HOSTS_RAND
</hosts>

</monalert>
EOF;

$host = <<<EOD
    <host>
        <name>RE_NAME</name>
        <display_name>RE_NAME-display</display_name>
        <desc>RE_NAME-desc</desc>
        <type>RE_TYPE</type>
        <parent_host>PARENT_NAME</parent_host>
    </host>
EOD;
// }}}

$hosts = '';
$prefix = 'testhost_';
for ($i = -1; $i< 10; $i++) {
	$name = $prefix . $i;
	if ($i > 6) {
		$parent_id = mt_rand(5, $i);
	}

	if (isset($parent_id)) {
		$parent_host = $prefix . $parent_id;
	} else {
		$parent_host = null;	
	}
	$types = array();
	for ($j = 0; $j < 8; $j++) {
		$types[] = mt_rand(0, 1);
	}

	if (array_sum($types) > 7) {
		$type = 'switch';
	} else {
		$type = 'server';	
	}
	
	$xml_host = str_replace(array('RE_NAME', 'RE_TYPE', 'PARENT_NAME'), array($name, $type, $parent_host), $host);
	$hosts .= $xml_host;
}

$test = str_replace('HOSTS_RAND', $hosts, $test);

file_put_contents('a.xml', $test);
