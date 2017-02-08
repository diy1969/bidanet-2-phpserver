<?php
/** @var $db DB */
include_once __DIR__ . '/common.php';

$uuid = isset($_GET['uuid']) ? $_GET['uuid'] : null;
$name = isset($_GET['name']) ? $_GET['name'] : null;
$cellphone = isset($_GET['cellphone']) ? $_GET['cellphone'] : null;
$dd_time_1 = isset($_GET['dd_time_1']) ? $_GET['dd_time_1'] : null;
$dd_time_2 = isset($_GET['dd_time_2']) ? $_GET['dd_time_2'] : null;
$customerFlag = isset($_GET['customer_flag']) ? $_GET['customer_flag'] : null;
$status = isset($_GET['status']) ? $_GET['status'] : null;
$isOrder = isset($_GET['is_order']) ? $_GET['is_order'] : null;

$query = "SELECT
 a.uuid as uuid,
 a.name1 as name,
 a.mscreate_time as xiafaTime,
 a.shop_date as shopDate,
 a.paid as paid,
 a.str_shop_date as strShopDate,
 a.customer_flag as customerFlag,
 a.customer_user_status as status,
 a.valid_flag as validFlag
 FROM bus_customer a WHERE a.sales_id = :id";
$queryParam = [
    'id' => $uuid
];

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
if ($dd_time_1 && $dd_time_2) {
    $query .= " AND a.shop_date BETWEEN :dd_time_1 AND :dd_time_2";
    $queryParam += [
        'dd_time_1' => getDay0Time($dd_time_1),
        'dd_time_2' => getDay24Time($dd_time_2)
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
if($isOrder){
    if($isOrder == 1){ // 已订单
        $query .= " AND a.paid != null";
    }elseif($isOrder == 2){ // 未订单
        $query .= " AND a.paid = null";
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