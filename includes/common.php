<?php
// Autoload 自动载入
require '../vendor/autoload.php';
// 开启session
session_start();
// 获取配置文件
include 'config.php';

date_default_timezone_set('PRC'); // 切换到中国的时间

/**
 * 所有的路劲调用此方法来解决域名后面的文件夹的问题
 * @param $path
 * @return string
 */
function toFullPath($path){
    return HOST_FOLDER.$path;
}

$config = [
    'debug'  => DEBUG,
    'apiUrl' => API_BASE_URL,
    'serviceInterval' => SERVICE_INTERVAL,
    'openidSessionKey' => OPENID_SESSION_KEY,
    'app_id'  => WX_APP_ID,
    'secret'  => WX_SECRET,
    'token'   => WX_TOKEN,
    'aes_key' => WX_AES_KEY,
];