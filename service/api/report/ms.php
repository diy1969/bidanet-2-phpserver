<?php
/** @var $db */
/** @var $uuid */
/** @var $timeFlag */
/** @var $timeStart */
/** @var $timeEnd */
include_once __DIR__ . '/common.php';

// 接收客资数
function getJskzs($db, $userId, $timeStart, $timeEnd)
{
    return $db->single("SELECT COUNT(a.id) as jskzs FROM bus_customer a WHERE a.customer_user_status IN(6,7,9,10) AND a.sales_id = :id AND a.mscreate_time BETWEEN :timeStart AND :timeEnd",
        array('id' => $userId, 'timeStart' => $timeStart, 'timeEnd' => $timeEnd)
    );
}

// 实际到店数
function getSjdds($db, $userId, $timeStart, $timeEnd)
{
    return $db->single("SELECT COUNT(a.id) as sjdds FROM bus_customer a WHERE a.customer_user_status IN(9,10) AND a.sales_id = :id AND a.mscreate_time BETWEEN :timeStart AND :timeEnd",
        array('id' => $userId, 'timeStart' => $timeStart, 'timeEnd' => $timeEnd)
    );
}

// 成交数
function getCjs($db, $userId, $timeStart, $timeEnd)
{
    return $db->single("SELECT COUNT(a.id) as cjs FROM bus_customer a WHERE a.customer_user_status = 9 AND a.sales_id = :id AND a.mscreate_time BETWEEN :timeStart AND :timeEnd",
        array('id' => $userId, 'timeStart' => $timeStart, 'timeEnd' => $timeEnd)
    );
}

// 目标
function getTarget($db, $userId, $timeFlag, $timeStart, $timeEnd){
    $result = $db->row("SELECT a.send_valid_number as jskzs, a.invite_shop_number as sjdds, a.deal_order_total as cjs, a.order_price as cjl FROM salesroom_report a WHERE a.creator_id = :id AND a.time_flag = :timeFlag AND a.create_time BETWEEN :timeStart AND :timeEnd ORDER BY a.create_time DESC limit 1",
        array('id' => $userId, 'timeFlag' => $timeFlag, 'timeStart' => $timeStart, 'timeEnd' => $timeEnd)
    );
    return $result ? $result : [
        'jskzs' => 0,
        'sjdds' => 0,
        'cjs' => 0,
        'cjl' => 0,
    ];
}

$jskzs = getJskzs($db, $uuid, $timeStart, $timeEnd);
$sjdds = getSjdds($db, $uuid, $timeStart, $timeEnd);
$cjs = getCjs($db, $uuid, $timeStart, $timeEnd);

$target = getTarget($db, $uuid, $timeFlag, $timeStart, $timeEnd);
echo json_encode([
    'actualResult' => [
        'jskzs' => $jskzs,
        'sjdds' => $sjdds,
        'cjs' => $cjs,
        'cjl' => $sjdds ? round($cjs/$sjdds, 4) * 100 : 0,
    ],
    'targetResult' => [
        'jskzs' => $target['jskzs'],
        'sjdds' => $target['sjdds'],
        'cjs' => $target['cjs'],
        'cjl' => $target['cjl'],
    ]
]);
exit;