<?php
/** @var $config array */

include '../includes/common.php';

use EasyWeChat\Foundation\Application;

$app = new Application($config);
$notice = $app->notice;

//$openid = $_GET['openid'];
//$noticeType = $_GET['noticeType'];
//switch ($noticeType){
//    case 1:
//        $url = 'http://'.HOST_URL.HOST_FOLDER;
//        break;
//}

$result = $notice->send([
    'touser' => 'ogP1Fv3z-ReeD3yyiWHxpgEdwcrE',
    'template_id' => 'DmHqGtGUmiEMaLt-oFw-ayiFPH0Kv0zhiDKgtPOo-8A',
    'url' => 'http://'.HOST_URL.HOST_FOLDER,
    'topcolor' => '#f7f7f7',
    'data' => [
        'username' => '我是username'
    ],
]);
var_dump($result);