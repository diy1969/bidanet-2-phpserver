<?php
/** @var $config array */

include '../includes/common.php';

use EasyWeChat\Foundation\Application;

$app = new Application($config);
// 从项目实例中得到服务端应用实例。
$server = $app->server;
$server->setMessageHandler(function ($message) {
    // $message->FromUserName // 用户的 openid
    // $message->MsgType // 消息类型：event, text....
    return 'http://'.HOST_URL.HOST_FOLDER;
});
$response = $server->serve();
$response->send();