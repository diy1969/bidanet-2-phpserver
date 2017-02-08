<?php
/** @var $db DB */
include_once __DIR__ . '/common.php';

$uuid = isset($_GET['uuid']) ? $_GET['uuid'] : null;
// 快速搜索参数
$quick = isset($_GET['quick']) ? $_GET['quick'] : null;
$quickType = isset($_GET['quick_type']) ? $_GET['quick_type'] : null;
// 非快速搜索参数
$name = isset($_GET['name']) ? $_GET['name'] : null;
$cellphone = isset($_GET['cellphone']) ? $_GET['cellphone'] : null;
$bb_time_1 = isset($_GET['bb_time_1']) ? $_GET['bb_time_1'] : null;
$bb_time_2 = isset($_GET['bb_time_2']) ? $_GET['bb_time_2'] : null;
$customerFlag = isset($_GET['customer_flag']) ? $_GET['customer_flag'] : null;
$status = isset($_GET['status']) ? $_GET['status'] : null;

$query = "SELECT
 a.uuid as uuid,
 a.name1 as name,
 a.yycreate_time as getTime,
 a.customer_flag as customerFlag,
 a.customer_user_status as status,
 a.valid_flag as validFlag
 FROM bus_customer a WHERE a.invite_id = :id";
$queryParam = [
    'id' => $uuid
];

if($quick && $quickType){
    switch ($quickType) {
        case 1: // 最近5日内还没有下发的客资
            $dayAgo = strtotime('-5 day') * 1000;
            $query .= " AND a.yycreate_time >= :dayAgo AND a.mscreate_time = 0";
            $queryParam += [
                'dayAgo' => $dayAgo
            ];
            break;
        case 2: // 超过5日没有下发的客资
            $dayAgo = strtotime('-5 day') * 1000;
            $query .= " AND a.yycreate_time < :dayAgo AND a.mscreate_time = 0";
            $queryParam += [
                'dayAgo' => $dayAgo
            ];
            break;
        case 3: // 超过10天没有下发的客资
            $dayAgo = strtotime('-10 day') * 1000;
            $query .= " AND a.yycreate_time < :dayAgo AND a.mscreate_time = 0";
            $queryParam += [
                'dayAgo' => $dayAgo
            ];
            break;
        case 4: // 超过30天没有下发的客资
            $dayAgo = strtotime('-30 day') * 1000;
            $query .= " AND a.yycreate_time < :dayAgo AND a.mscreate_time = 0";
            $queryParam += [
                'dayAgo' => $dayAgo
            ];
            break;
        case 5: // 最近3天到店了，但是没有成交的客资
            $dayAgo = strtotime('-3 day') * 1000;
            $query .= " AND a.mstime >= :dayAgo AND a.customer_user_status = 10";
            $queryParam += [
                'dayAgo' => $dayAgo
            ];
            break;
        case 6: // 最近3天未到店，门市改期客资
            $dayAgo = strtotime('-3 day') * 1000;
            $query .= " AND a.mstime >= :dayAgo AND a.customer_user_status = 7";
            $queryParam += [
                'dayAgo' => $dayAgo
            ];
            break;
    }
}else{
    if ($name) {
        $query .= " AND a.name1 LIKE :name";
        $queryParam += [
            'name' => "%$name%"
        ];
    }
    if ($cellphone) {
        $query .= " AND a.tel1 LIKE :cellphone";
        $queryParam += [
            'cellphone' => "%$cellphone%"
        ];
    }
    if ($bb_time_1 && $bb_time_2) {
        $query .= " AND a.yycreate_time BETWEEN :bb_time_1 AND :bb_time_2";
        $queryParam += [
            'bb_time_1' => getDay0Time($bb_time_1),
            'bb_time_2' => getDay24Time($bb_time_2)
        ];
    }
    if ($customerFlag) {
        $query .= " AND a.customer_flag = :customerFlag";
        $queryParam += [
            'customerFlag' => $customerFlag
        ];
    }
    if ($status) {
        $query .= " AND a.customer_user_status in (:status)";
        $queryParam += [
            'status' => $status
        ];
    }
}

$result = $db->query($query,$queryParam);

echo json_encode([
    'retMsg' => 'OK',
    'retVal' => [
        'list' => $result,
        'total' => count($result)
    ]
], JSON_UNESCAPED_UNICODE);
exit;