<?php
/** @var $config array */

include '../includes/common.php';

use EasyWeChat\Foundation\Application;

$config = array_merge($config,[
    'oauth' => [
        'scopes'   => ['snsapi_base'],
        'callback' => toFullPath('/public/oauth_callback.php'),
    ],
]);
$app = new Application($config);
$oauth = $app->oauth;

$user = $oauth->user();
$_SESSION[$config['openidSessionKey']] = $user->getId();
$targetUrl = empty($_SESSION['target_url']) ? toFullPath('/') : $_SESSION['target_url'];
header('location:'. $targetUrl);