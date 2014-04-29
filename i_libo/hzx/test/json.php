<?php
for ($i = 1; $i < 1000; $i++) {
	$arr[] = 'tgroup_' . mt_rand(1, 6000);	
}

$arr = array_unique($arr);

echo $str = str_replace('"', '`', json_encode(array_values($arr)));
/*

$str = "{`domain_name`:`domain1.com`,`password`:`aaaaa123`,`expiration_time`:0,`quota`:100,`attach_size`:10,`rcpt_num`:10,`rcpt_size`:10,`upload_size`:1024,`lock_status`:3,`has_alias`:1,`has_pop`:1,`has_smtp`:1,`has_imap`:1,`has_remote`:1,`has_mailrecall`:1,`has_mailrecall_group`:1,`has_mailstatus_group`:1,`has_mobile`:1,`has_pushmail`:1,`has_epush`:1, `bookmark_status`: 1, `notebook_status`: 1, `storage_status`: 1, `storage_quota`: 100, `storage_file_size`: 20}";

$arr = json_decode(str_replace('`', '"', $str), true);

print_r($arr);
*/
