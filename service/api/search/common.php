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