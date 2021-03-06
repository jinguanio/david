<?php
$req_url = 'https://172.16.100.110:3333/service?request_token';
$authurl = 'https://172.16.100.110:3333/service?authorize';
$acc_url = 'https://172.16.100.110:3333/service?access_token';
$api_url = 'https://172.16.100.110:3333/api.php';
$conskey = 'libo';
$conssec = 'aaaaa';

session_start();

//  当 state=1 则下次请求应该包含一个 oauth_token 。
//  如果没有则返回 0
if(!isset($_GET['oauth_token']) && $_SESSION['state']==1) $_SESSION['state'] = 0;

try {
  $oauth = new OAuth($conskey,$conssec,OAUTH_SIG_METHOD_HMACSHA1,OAUTH_AUTH_TYPE_URI);
  $oauth->enableDebug();

  if(!isset($_GET['oauth_token']) && !$_SESSION['state']) {
    $request_token_info = $oauth->getRequestToken($req_url);
    $_SESSION['secret'] = $request_token_info['oauth_token_secret'];
    $_SESSION['state'] = 1;
    header('Location: '.$authurl.'?oauth_token='.$request_token_info['oauth_token']);
    exit;
  } else if($_SESSION['state']==1) {
    $oauth->setToken($_GET['oauth_token'],$_SESSION['secret']);
    $access_token_info = $oauth->getAccessToken($acc_url);
    $_SESSION['state'] = 2;
    $_SESSION['token'] = $access_token_info['oauth_token'];
    $_SESSION['secret'] = $access_token_info['oauth_token_secret'];
  } 

  $oauth->setToken($_SESSION['token'],$_SESSION['secret']);
  $oauth->fetch("$api_url");
  $json = json_decode($oauth->getLastResponse());
  print_r($json);
} catch(OAuthException $E) {
  print_r($E);
}

