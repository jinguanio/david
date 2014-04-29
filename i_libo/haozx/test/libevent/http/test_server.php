<?php
require_once EMBASE_PATH_EYOU_TOOLMAIL_CONF . 'conf_global.php';
require_once PATH_EYOUM_LIB . 'httpapi/phphttp/em_httpapi_phphttp_http.class.php';
require_once PATH_EYOUM_LIB . 'em_httpapi.class.php';
require_once PATH_EYOUM_LIB . 'api/router/em_httpapi_router_route.class.php';
require_once PATH_EYOUM_LIB . 'httpapi/router/em_httpapi_router.class.php';
$router = new em_httpapi_router();
$route  = new em_httpapi_router_route();
$router->add_route('api', $route);
$httpapi = em_httpapi::get_instance();
//$httpapi->set_base_url('/api');
$httpapi->set_router($router);
$httpapi->add_controller_directory(PATH_EYOUM_LIB . 'api/action/', 'api');

$params = array(
	'server_host' => '0.0.0.0:8011',
);
$http = new em_httpapi_phphttp_http($params);
$http->set_default_callback(array($httpapi, 'dispatch'));
$http->set_max_body_size(1024);
$http->add_alias('test.toolmail');
$http->remove_alias('test.toolmail');
$http->bind();
$http->run();
