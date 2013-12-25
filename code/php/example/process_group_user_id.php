<?php
$pgid = posix_getpgrp();
$egid = posix_getegid();
$gid = posix_getgid();

$uid = posix_getuid();
$pid = posix_getpid();
$ppid = posix_getppid();

echo "<pre>";
var_dump('the current process group: ' . $pgid);
var_dump('the effective group ID: ' . $egid);
var_dump('the real group ID: ' . $gid);
echo "\n";

var_dump("uid: {$uid}");
var_dump("pid: {$pid}");
var_dump("ppid: {$ppid}");
echo "\n";

print_r(posix_getpwuid($uid));
print_r(posix_getgrgid($gid));
echo "</pre>";

