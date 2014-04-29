<?php
//$url = 'localhost:8010/?q=host.do&action=service_list';
////$http = new HttpRequest($url, HttpRequest::METH_POST);
////$http->addPostFields(array('myname', 'website', 'comment'));
////$http->addRawPostData('name=myname&website=mysite&comment=mytext');
////$response = $http->send();
////var_dump($response);
//
//$r = new HttpRequest($url, HttpRequest::METH_POST);
////$r->setOptions(array('cookies' => array('lang' => 'de')));
//$r->setPostFields(array('host_name' => 'lan-100.114', 'pass' => 's3c|r3t'));
////$r->addPostFile('image', 'profile.jpg', 'image/jpeg');
//try {
//	var_dump($r->send());
//} catch (HttpException $ex) {
//	echo $ex;
//}

//@set_include_path('/usr/local/eyou/toolmail/app/mc/inc/conf' . PATH_SEPARATOR . get_include_path());
//require_once 'conf_global.php';
////require_once '/usr/local/eyou/toolmail/app/mc/inc/conf/conf_global.php';
//require_once PATH_EYOUM_LIB . 'emdata/em_emdata_client_test.class.php';
//
//$http = new em_emdata_client_test();
//try {
//	$result = $http->set_module('monitor_cache')
//		->set_action('metric')
//		->set_params(array('host_name' => 'lan-100.114', 'metric_name' => 'net_tx_errs__sit0'))
//		->run();
//	var_dump($result);
//} catch (em_exception $e) {
//	echo $e->getMessage();	
//}

@set_include_path('/usr/local/eyou/toolmail/app/mc/inc/conf' . PATH_SEPARATOR . get_include_path());
require_once 'conf_global.php';
//require_once '/usr/local/eyou/toolmail/app/mc/inc/conf/conf_global.php';
require_once PATH_EYOUM_LIB . 'emdata/em_emdata_client.class.php';

$http = new em_emdata_client();
try {
	$result = $http->set_module('host')
		->set_action('status_count')
		->set_params(array('host_name' => 'lan-100.114', 'metric_name' => 'net_tx_errs__sit0'))
//		->set_params(array('host_name' => 'lan-100.114', 'columns' => json_encode($column)))
//		->set_params(array('cluster_name' => 'eYouMail'))
		->run();
	var_dump($result);
} catch (em_exception $e) {
	echo $e->getMessage();	
}

