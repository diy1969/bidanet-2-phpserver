<?php
session_start();

//id = 45a5c25e-4ed4-4b28-845b-bb11628d6e3f
if (!isset($_GET['id']) && !isset($_SESSION['php-company-id'])) {
    exit('不存在公司id');
}

if (isset($_GET['id'])) {
    $companyId = $_GET['id'];
    $_SESSION['php-company-id'] = $companyId;
} else {
    $companyId = $_SESSION['php-company-id'];
}

require_once __DIR__ . '/../mysql/Db.class.php';

$db = new DB();

/**
 * 计算百分率
 * @param $a int 分子
 * @param $b int 分母
 * @return string
 */
function calculatePercent($a, $b)
{
    return (!$b ? 0 : round($a / $b, 4)) * 100 . '%';
}

/**
 * 计算单价
 * @param $a int 数量
 * @param $b int 总价
 * @return float
 */
function calculateSingleCB($a, $b)
{
    return (!$a ? $b : round($b / $a, 4));
}

/**
 * 获取一天的开始时间
 * @param $timestamp
 * @param bool $isMsec 是否是毫秒级别
 * @return int
 */
function getDay0Time($timestamp, $isMsec = true){
    if($isMsec){
        $timestamp = $timestamp/1000;
    }
    $timestamp = strtotime(date('Y-m-d 00:00:00', $timestamp));
    if($isMsec){
        return $timestamp * 1000;
    }
    return $timestamp;
}

/**
 * 获取一天的最后时间
 * @param $timestamp
 * @param bool $isMsec 是否是毫秒级别
 * @return int
 */
function getDay24Time($timestamp, $isMsec = true){
    if($isMsec){
        $timestamp = $timestamp/1000;
    }
    $timestamp = strtotime(date('Y-m-d 23:59:59', $timestamp));
    if($isMsec){
        return $timestamp * 1000;
    }
    return $timestamp;
}
?>
<html>
<head>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-datepicker.min.css">
    <script src="../js/jquery-1.11.1.js"></script>
    <script src="../js/bootstrap-datepicker.min.js"></script>
    <script src="../js/bootstrap-datepicker.zh-CN.min.js"></script>
    <style>
        .v-center {
            vertical-align: middle !important;
        }
        .h-center {
            text-align: center !important;
        }
    </style>
</head>
<body>
<div class="container-fluid">
