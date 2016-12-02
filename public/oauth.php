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

// 未登录
if (empty($_SESSION[$config['openidSessionKey']])) {
    $_SESSION['target_url'] = toFullPath('/public/oauth.php');
    $oauth->redirect()->send();
    exit;
}
$openid = $_SESSION[$config['openidSessionKey']];
$webIndexUrl = toFullPath('/web/index.html');
$js = <<<JS
    window.localStorage.setItem('openid', '$openid');
    window.location.href = $webIndexUrl;
JS;

?>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, maximum-scale=1.0, minimum-scale=1.0">
        <title>网销大师</title>
    </head>
    <body>
        <script>
            window.localStorage.setItem('openid', '<?=$openid?>');
            window.location.href = '<?=$webIndexUrl?>';
        </script>
        <h1>页面正在跳转...</h1>
        <h1 style="text-align: center;"><a href="<?=$webIndexUrl?>">如果没有跳转，请点此跳转</a></h1>
    </body>
</html>
