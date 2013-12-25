<?php
$context = new ZMQContext(1);

echo "connect to hello world server .... \n";

$requester = new ZMQSocket($context, ZMQ::SOCKET_REP);

$requester->connect("tcp://localhost:5560");
$who = ' server:' . time() . PHP_EOL;

while(true) {
	try {
	$reply = $requester->recv();
	echo $reply, PHP_EOL;
	sleep(3);
	$requester->send("response...." . $who);
	} catch (ZMQSocketException $e) {
	}
}

// 分发层
// 协议层
// 数据处理层
// 传输层

swan://monitor::member/service/base/get_key?host_id=23&sssss
//分页
