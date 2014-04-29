<?php
require EMBASE_PATH_EYOU_TOOLMAIL_CONF . 'conf_global.php';
$base = new EventBase();
$http = new EventHttp($base);
$http->setAllowedMethods(EventHttpRequest::CMD_GET | EventHttpRequest::CMD_POST);

require_once PATH_EYOUM_LIB . 'em_httpapi.class.php';

require_once PATH_EYOUM_LIB . 'api/router/em_httpapi_router_route.class.php';
require_once PATH_EYOUM_LIB . 'httpapi/router/em_httpapi_router.class.php';
$router = new em_httpapi_router();
$route  = new em_httpapi_router_route();
$router->add_route('api', $route);
$httpapi = em_httpapi::get_instance();
$httpapi->set_router($router);
$httpapi->add_controller_directory(PATH_EYOUM_LIB . 'api/action/', 'api');
$http->setDefaultCallback(array($httpapi, 'dispatch'), "custom data value");

$http->bind("0.0.0.0", 8010);
$base->loop();
?>
