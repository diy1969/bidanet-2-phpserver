<?php
/** @var $config array */

include '../includes/common.php';

use EasyWeChat\Foundation\Application;

/**
 * 记录日志
 * @param $fileName
 * @param $msgArr
 */
function logger($fileName, $msgArr){
    $fp = fopen("../tmp/".$fileName. date('YmdH') .".log", "a");
    fwrite($fp, date('i:s') . "\n");
    foreach($msgArr as $key => $msg){
        fwrite($fp, $key . ':' . $msg . "\n");
    }
    fwrite($fp, "\n");
    fclose($fp);
}

function getGroupFlagName($groupFlag){
    $groupFlag = intval($groupFlag);
    switch($groupFlag){
        case 1:
            $msg = '抓客';
            break;
        case 2:
            $msg = '邀约';
            break;
        case 3:
            $msg = '门市';
            break;
        default:
            $msg = '未知';
            break;
    }
    return $msg;
}

/**
 * 获取远程消息
 * @param $config
 */
function getAndSendData($config){
    $ch = curl_init();
    //设置选项，包括URL
    curl_setopt($ch, CURLOPT_URL, $config['apiUrl'].'getTemplateData?seq=0');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    // 设置超时
    $requestTimeOut = ($config['serviceInterval']-1)<1 ? 1 : ($config['serviceInterval']-1);
    curl_setopt($ch, CURLOPT_TIMEOUT, $requestTimeOut);
    //执行并获取HTML文档内容
    $output = curl_exec($ch);
    // 得到错误消息代码
    $errno = curl_errno( $ch );
    if($errno){
        $info  = curl_getinfo( $ch );
        // 记录当前操作日志
        logger('curl', [
            '错误号' => $errno,
            '错误信息' => json_encode($info)
        ]);
        //释放curl句柄
        curl_close($ch);
        exit;
    }

    //释放curl句柄
    curl_close($ch);

    // 转字符串为json对象
    $jsonOutput = json_decode($output);

    // 记录当前操作日志
    logger('msg', [
        '消息' => $output
    ]);

    // 发送消息
    if(isset($jsonOutput->msgs) && count($jsonOutput->msgs) > 0){
        $msgs = $jsonOutput->msgs;
        // 获取发送模板消息的对象
        $app = new Application($config);
        $notice = $app->notice;
        // 循环发送模板消息
        foreach($msgs as $item) {
            $msgTypeCode = $item->msgTypeCode; // 后期可能需要根据msgTypeCode来定义消息显示的内容
            $isOperation = $item->isOperation; // 是否是可以操作的消息，是为字符串"TRUE"，不是为字符串"FALSE"
            $data = [
                'touser' => $item->openid,
                'template_id' => 'Q0B-p7iA1dO_IwQvGQMAbhYR_LO6MAMAWsworAUuTO0',
                'topcolor' => '#f7f7f7',
                'data' => [
                    'first' => $item->messageType,
                    'keyword1' => $item->messageType,
                    'keyword2' => '客资姓名：' . $item->name1,
                    'keyword3' => date('Y-m-d H:i:s', time()),
                    'remark' => '操作人：' . getGroupFlagName($item->modify_group_flag) . '-' . $item->modifyUserName
                ],
            ];
            if($isOperation == 'TRUE'){
                $data['url'] = 'http://' . HOST_URL . HOST_FOLDER;
            }
            $result = $notice->send($data);
            // 记录返回结果
            logger('template', [
                '简化消息' => json_encode([
                    'name1' => $item->name1,
                    'msgTypeCode' => $item->msgTypeCode,
                    'messageType' => $item->messageType,
                    'customer_user_status' => $item->customer_user_status,
                    'yyworkCenterName' => $item->yyworkCenterName,
                    'uykName' => $item->uykName,
                    'msworkCenterUuid' => $item->msworkCenterName,
                    'umkName' => $item->umkName,
                ], JSON_UNESCAPED_UNICODE),
                '发送数据' => json_encode($data, JSON_UNESCAPED_UNICODE),
                '发送结果' => json_encode($result, JSON_UNESCAPED_UNICODE),
            ]);
        }
    }
}