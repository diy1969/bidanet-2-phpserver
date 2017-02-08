<?php
/** @var $db DB */
include_once __DIR__ . '/../common.php';

date_default_timezone_set('PRC');

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

$uuid = isset($_GET['uuid'])?$_GET['uuid']:0;
$timeFlag = isset($_GET['timeFlag'])?$_GET['timeFlag']:1; // 1 今日，2 本周，3 本月

switch ($timeFlag){
    case 1: // 今日
        $timeStart = getDay0Time(time() * 1000);
        $timeEnd = getDay24Time(time() * 1000);
        break;
    case 2: // 本周
        $firstTime = strtotime('this week');
        $timeStart = getDay0Time($firstTime * 1000);
        $timeEnd = getDay24Time(time() * 1000);
        break;
    case 3: // 本月
        $firstTime = strtotime(date('Ym01', strtotime('this month')));
        $timeStart = getDay0Time($firstTime * 1000);
        $timeEnd = getDay24Time(time() * 1000);
        break;
    default:
        $timeStart = getDay0Time(time() * 1000);
        $timeEnd = getDay24Time(time() * 1000);
        break;
}