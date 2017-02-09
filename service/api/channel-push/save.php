<?php
/** @var $db DB */
include_once __DIR__ . '/../common.php';

$companyId = isset($_POST['companyId']) ? $_POST['companyId'] : null;
$createTime = isset($_POST['createTime']) ? $_POST['createTime'] : null;
$listData = isset($_POST['listData']) ? json_decode($_POST['listData'], true) : null;

function update($db, $data)
{
    $db->query("UPDATE channel_push SET cost=:cost WHERE uuid=:uuid", array(
        'cost' => $data['cost'],
        'uuid' => $data['channelPushUuid']
    ));
}

function insert($db, $data, $createTime)
{
    if($data['cost']){
        $db->query("INSERT INTO
 channel_push(create_time, modify_time, uuid, company_id, channel_id, channel_way_id, cost)
 VALUES(     :create_time,:modify_time,:uuid,:company_id,:channel_id,:channel_way_id,:cost)", array(
            'create_time' => $createTime,
            'modify_time' => time()*1000,
            'uuid' => uuid(),
            'company_id' => $data['companyUuid'],
            'channel_id' => $data['channelUuid'],
            'channel_way_id' => $data['channelWayUuid'],
            'cost' => $data['cost']
        ));
    }
}

foreach ($listData as $item) {
    if ($item['channelPushUuid']) {
        update($db, $item);
    } else {
        insert($db, $item, $createTime);
    }
}

echo json_encode([
    'retMsg' => 'OK',
], JSON_UNESCAPED_UNICODE);
exit;