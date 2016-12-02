<?php
/** @var $config array */

include '../includes/common.php';

// 退出移除session
unset($_SESSION[$config['openidSessionKey']]);
?>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, maximum-scale=1.0, minimum-scale=1.0">
        <title>网销大师</title>
    </head>
    <body>
        <h1>退出成功，请重新进入</h1>
    </body>
</html>

