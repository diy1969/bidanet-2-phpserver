<?php
/** @var $db */
/** @var $uuid */
/** @var $timeFlag */
/** @var $timeStart */
/** @var $timeEnd */
include_once __DIR__ . '/common.php';

// 我的客资数
function getKzs($db, $userId, $timeStart, $timeEnd)
{
    $result = $db->single("SELECT COUNT(a.id) as kzs FROM bus_customer a WHERE a.customer_service_id = :id AND a.zkcreate_time BETWEEN :timeStart AND :timeEnd",
        array('id' => $userId, 'timeStart' => $timeStart, 'timeEnd' => $timeEnd)
    );
    return $result ? $result : 0;
}

// 有效客资数
function getYxkzs($db, $userId, $timeStart, $timeEnd)
{
    $result = $db->single("SELECT COUNT(a.id) as yxkzs FROM bus_customer a WHERE a.valid_flag=1 AND a.customer_service_id = :id AND a.zkcreate_time BETWEEN :timeStart AND :timeEnd",
        array('id' => $userId, 'timeStart' => $timeStart, 'timeEnd' => $timeEnd)
    );
    return $result ? $result : 0;
}

// 成交数
function getCjs($db, $userId, $timeStart, $timeEnd)
{
    $result = $db->single("SELECT COUNT(a.id) as cjs FROM bus_customer a WHERE a.customer_user_status = 9 AND a.customer_service_id = :id AND a.zkcreate_time BETWEEN :timeStart AND :timeEnd",
        array('id' => $userId, 'timeStart' => $timeStart, 'timeEnd' => $timeEnd)
    );
    return $result ? $result : 0;
}

// 目标
function getTarget($db, $userId, $timeFlag, $timeStart, $timeEnd){
    $result = $db->row("SELECT a.my_customer as kzs, a.valid_customer as yxkzs, a.customer_valid_rate as yxl, a.deal_customer as cjl FROM zkreport a WHERE a.creator_id = :id AND a.time_flag = :timeFlag AND a.create_time BETWEEN :timeStart AND :timeEnd ORDER BY a.create_time DESC limit 1",
        array('id' => $userId, 'timeFlag' => $timeFlag, 'timeStart' => $timeStart, 'timeEnd' => $timeEnd)
    );
    return $result ? $result : [
        'kzs' => 0,
        'yxkzs' => 0,
        'yxl' => 0,
        'cjl' => 0,
    ];
}

$kzs = getKzs($db, $uuid, $timeStart, $timeEnd);
$yxkzs = getYxkzs($db, $uuid, $timeStart, $timeEnd);
$cjs = getCjs($db, $uuid, $timeStart, $timeEnd);

$target = getTarget($db, $uuid, $timeFlag, $timeStart, $timeEnd);
echo json_encode([
    'actualResult' => [
        'kzs' => $kzs,
        'yxkzs' => $yxkzs,
        'yxl' => $kzs ? round($yxkzs/$kzs, 4) * 100 : 0,
        'cjl' => $kzs ? round($cjs/$yxkzs, 4) * 100 : 0,
    ],
    'targetResult' => [
        'kzs' => $target['kzs'],
        'yxkzs' => $target['yxkzs'],
        'yxl' => $target['yxl'],
        'cjl' => $target['cjl'],
    ]
]);
exit;