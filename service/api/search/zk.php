<?php
/** @var $db DB */
include_once __DIR__ . '/common.php';

$uuid = isset($_GET['uuid']) ? $_GET['uuid'] : null;
$name = isset($_GET['name']) ? $_GET['name'] : null;
$cellphone = isset($_GET['cellphone']) ? $_GET['cellphone'] : null;
$create_time_1 = isset($_GET['create_time_1']) ? $_GET['create_time_1'] : null;
$create_time_2 = isset($_GET['create_time_2']) ? $_GET['create_time_2'] : null;
$status = isset($_GET['status']) ? $_GET['status'] : null;

$query = "SELECT
 a.uuid as uuid,
 a.name1 as name,
 a.zkcreate_time as createTime,
 a.customer_flag as customerFlag,
 a.customer_user_status as status,
 a.valid_flag as validFlag
 FROM bus_customer a WHERE a.customer_service_id = :id";
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
if ($create_time_1 && $create_time_2) {
    $query .= " AND a.zkcreate_time BETWEEN :create_time_1 AND :create_time_2";
    $queryParam += [
        'create_time_1' => getDay0Time($create_time_1),
        'create_time_2' => getDay24Time($create_time_2)
    ];
}
if ($status) {
    $query .= " AND a.customer_user_status in (:status)";
    $queryParam += [
        'status' => $status
    ];
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