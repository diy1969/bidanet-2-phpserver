<?php
/** @var $db DB */
include_once __DIR__ . '/../common.php';

$companyId = isset($_POST['companyId']) ? $_POST['companyId'] : null;
$createTime = isset($_POST['createTime']) ? $_POST['createTime'] : null;
$targetDate = isset($_POST['targetDate']) ? $_POST['targetDate'] : null;

$query = "SELECT
 :targetDate as targetDate,
 a.uuid as channelWayUuid,
 b.uuid as channelUuid,
 c.uuid as channelPushUuid,
 b.company_id as companyUuid,
 b.name as channelName,
 a.name as channelWayName,
 c.cost as cost
 FROM bus_channel_way a
 LEFT JOIN bus_channel b ON b.uuid = a.channel_id
 LEFT JOIN channel_push c ON c.channel_way_id = a.uuid AND c.create_time = :createTime AND c.channel_id = b.uuid
 WHERE a.company_id = :companyId
 ORDER BY b.create_time ASC, a.create_time ASC";
$queryParams = [
    'targetDate' => $targetDate,
    'createTime' => $createTime,
    'companyId' => $companyId
];

$result = $db->query($query,$queryParams);

echo json_encode([
    'retMsg' => 'OK',
    'listMap' => $result
], JSON_UNESCAPED_UNICODE);
exit;