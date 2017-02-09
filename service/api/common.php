<?php
require_once __DIR__ . '/../mysql/Db.class.php';

$db = new DB();

header('Access-Control-Allow-Origin:*');

/**
 * 生成一个uuid
 * @param string $prefix
 * @return string
 */
function uuid($prefix = '')
{
    $chars = md5(uniqid(mt_rand(), true));
    $uuid  = substr($chars,0,8) . '-';
    $uuid .= substr($chars,8,4) . '-';
    $uuid .= substr($chars,12,4) . '-';
    $uuid .= substr($chars,16,4) . '-';
    $uuid .= substr($chars,20,12);
    return $prefix . $uuid;
}