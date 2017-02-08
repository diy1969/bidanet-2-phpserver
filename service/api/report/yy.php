<?php
/** @var $db */
/** @var $uuid */
/** @var $timeFlag */
/** @var $timeStart */
/** @var $timeEnd */
include_once __DIR__ . '/common.php';

// 有效客资数
function getYxkzs($db, $userId, $timeStart, $timeEnd)
{
    return $db->single("SELECT COUNT(a.id) as yxkzs FROM bus_customer a WHERE a.customer_user_status NOT IN(2, 4) AND a.valid_flag != 2 AND a.invite_id = :id AND a.yycreate_time BETWEEN :timeStart AND :timeEnd",
        array('id' => $userId, 'timeStart' => $timeStart, 'timeEnd' => $timeEnd)
    );
}

// 邀约到店数
function getYydds($db, $userId, $timeStart, $timeEnd)
{
    return $db->single("SELECT COUNT(a.id) as yydds FROM bus_customer a WHERE a.customer_user_status IN(6,7,9,10) AND a.invite_id = :id AND a.yycreate_time BETWEEN :timeStart AND :timeEnd",
        array('id' => $userId, 'timeStart' => $timeStart, 'timeEnd' => $timeEnd)
    );
}

// 成交数
function getCjs($db, $userId, $timeStart, $timeEnd)
{
    return $db->single("SELECT COUNT(a.id) as cjs FROM bus_customer a WHERE a.customer_user_status = 9 AND a.invite_id = :id AND a.yycreate_time BETWEEN :timeStart AND :timeEnd",
        array('id' => $userId, 'timeStart' => $timeStart, 'timeEnd' => $timeEnd)
    );
}

// 目标
function getTarget($db, $userId, $timeFlag){
    $result = $db->row("SELECT a.record_valid_customer as yxkzs, a.invite_shop_customer as yydds, a.network_deal_customer as yyddl, a.deal_order_total as cjs FROM invite_report a WHERE a.creator_id = :id AND a.time_flag = :timeFlag ORDER BY a.create_time DESC limit 1",
        array('id' => $userId, 'timeFlag' => $timeFlag)
    );
    return $result ? $result : [
        'yxkzs' => 0,
        'yydds' => 0,
        'yyddl' => 0,
        'cjs' => 0,
    ];
}

$yxkzs = getYxkzs($db, $uuid, $timeStart, $timeEnd);
$yydds = getYydds($db, $uuid, $timeStart, $timeEnd);
$cjs = getCjs($db, $uuid, $timeStart, $timeEnd);

$target = getTarget($db, $uuid, $timeFlag);
echo json_encode([
    'actualResult' => [
        'yxkzs' => $yxkzs,
        'yydds' => $yydds,
        'yyddl' => $yxkzs ? round($yydds/$yxkzs, 4) * 100 : 0,
        'cjs' => $cjs,
    ],
    'targetResult' => [
        'yxkzs' => $target['yxkzs'],
        'yydds' => $target['yydds'],
        'yyddl' => $target['yyddl'],
        'cjs' => $target['cjs'],
    ]
]);
exit;