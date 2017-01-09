<?php
/** @var $companyId string */
/** @var $db DB */
include_once __DIR__ . '/../common/header.php';

date_default_timezone_set('PRC');

$channelId = (isset($_GET['channel']) && $_GET['channel'] != -1) ? $_GET['channel'] : null;
$timeStart = isset($_GET['start-time']) ? $_GET['start-time'] : strtotime('-1 day') * 1000;
$timeEnd = isset($_GET['end-time']) ? $_GET['end-time'] : time() * 1000;
$timeStart = getDay0Time($timeStart);
$timeEnd = getDay24Time($timeEnd);

// 获取所有渠道，用于select
function getChannelOptions($db, $companyId)
{
    $data = $db->query("SELECT a.`name` as qd, a.uuid as qd_uuid from bus_channel a WHERE a.company_id = :id",
        array('id' => $companyId)
    );
    $result = [];
    foreach ($data as $d) {
        $result[$d['qd_uuid']] = $d['qd'];
    }
    return $result;
}

// 获取所有渠道方式
function getChannelWays($db, $companyId, $channelId = null)
{
    if (!$channelId) {
        return $db->query("SELECT a.`name` as qd, a.uuid as qd_uuid, b.`name` as fs, b.uuid as fs_uuid from bus_channel a INNER JOIN bus_channel_way b ON a.uuid = b.channel_id WHERE a.company_id = :id  ORDER BY a.create_time ASC",
            array('id' => $companyId)
        );
    } else {
        return $db->query("SELECT a.`name` as qd, a.uuid as qd_uuid, b.`name` as fs, b.uuid as fs_uuid from bus_channel a INNER JOIN bus_channel_way b ON a.uuid = b.channel_id WHERE a.company_id = :id AND a.uuid = :channelId ORDER BY a.create_time ASC",
            array('id' => $companyId, 'channelId' => $channelId)
        );
    }
}

// 投入
function getTr($db, $channelId, $timeStart, $timeEnd)
{
    $tr = $db->single("SELECT SUM(a.cost) as tr from channel_push a WHERE a.channel_id = :id AND a.create_time BETWEEN :timeStart AND :timeEnd",
        array('id' => $channelId, 'timeStart' => $timeStart, 'timeEnd' => $timeEnd)
    );
    return $tr ? $tr : 0;
}

// 客资数
function getKzs($db, $channelWayId, $timeStart, $timeEnd)
{
    return $db->single("SELECT COUNT(a.id) as kzs FROM bus_customer a WHERE a.channel_way_id = :id AND a.create_time BETWEEN :timeStart AND :timeEnd",
        array('id' => $channelWayId, 'timeStart' => $timeStart, 'timeEnd' => $timeEnd)
    );
}

// 有效客资数
function getYxkzs($db, $channelWayId, $timeStart, $timeEnd)
{
    return $db->single("SELECT COUNT(a.id) as yxkzs FROM bus_customer a WHERE a.valid_flag=1 AND a.channel_way_id = :id AND a.create_time BETWEEN :timeStart AND :timeEnd",
        array('id' => $channelWayId, 'timeStart' => $timeStart, 'timeEnd' => $timeEnd)
    );
}

// 到店数
function getDds($db, $channelWayId, $timeStart, $timeEnd)
{
    return $db->single("SELECT COUNT(a.id) as dds FROM bus_customer a WHERE a.customer_user_status IN(6,7,9,10) AND a.channel_way_id = :id AND a.create_time BETWEEN :timeStart AND :timeEnd",
        array('id' => $channelWayId, 'timeStart' => $timeStart, 'timeEnd' => $timeEnd)
    );
}

// 成交数
function getCjs($db, $channelWayId, $timeStart, $timeEnd)
{
    return $db->single("SELECT COUNT(a.id) as cjs FROM bus_customer a WHERE a.customer_user_status = 9 AND a.channel_way_id = :id AND a.create_time BETWEEN :timeStart AND :timeEnd",
        array('id' => $channelWayId, 'timeStart' => $timeStart, 'timeEnd' => $timeEnd)
    );
}

// 成交金额
function getCjje($db, $channelWayId, $timeStart, $timeEnd)
{
    $cjje = $db->single("SELECT SUM(a.paid) as cjje FROM bus_customer a WHERE a.customer_user_status = 9 AND a.channel_way_id = :id AND a.create_time BETWEEN :timeStart AND :timeEnd",
        array('id' => $channelWayId, 'timeStart' => $timeStart, 'timeEnd' => $timeEnd)
    );
    return $cjje ? $cjje : 0;
}

$channelWays = getChannelWays($db, $companyId, $channelId);
?>
    <form action="" method="get" class="row">
        <div class="form-group col-md-6">
            <label>起止日期</label>
            <div class="input-daterange input-group" id="datepicker">
                <input type="text" class="picker form-control" data-target="start-time"
                       value="<?= date('Y年m月d日', $timeStart / 1000) ?>">
                <span class="input-group-addon">to</span>
                <input type="text" class="picker form-control" data-target="end-time"
                       value="<?= date('Y年m月d日', $timeEnd / 1000) ?>">
            </div>
            <input type="hidden" name="start-time" value="<?= $timeStart ?>">
            <input type="hidden" name="end-time" value="<?= $timeEnd ?>">
        </div>
        <div class="form-group col-md-4">
            <label>渠道</label>
            <select name="channel" class="form-control">
                <option value="-1">全部</option>
                <?php $channelOptions = getChannelOptions($db, $companyId);
                foreach ($channelOptions as $value => $label): ?>
                    <option value="<?= $value ?>"><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group col-md-12">
            <input type="submit" class="btn btn-success" value="查询">
        </div>
    </form>
    <script>
        $('.input-daterange').datepicker({
            autoclose: true,
            language: 'zh-CN',
            minViewMode: 'days',
            format: 'yyyy年mm月dd日'
        });
        $('.input-daterange .picker').on('changeDate', function (e) {
            //console.log($(this).data('target'));
            //console.log(e.date.getTime());
            $('input[name=' + $(this).data('target') + ']').val(e.date.getTime());
        });
    </script>

    <table class="table table-bordered table-responsive">
        <tr class="bg-primary">
            <th>渠道</th>
            <th>方式</th>
            <th>投入</th>
            <th>客资数</th>
            <th>有效客资数</th>
            <th>到店数</th>
            <th>成交数</th>
            <th>成交金额</th>
            <th>有效客资成本</th>
            <th>到店成本</th>
            <th>成交成本</th>
            <th>有效客资率</th>
            <th>到店率</th>
            <th>到店成交率</th>
            <th>有效客资成交率</th>
        </tr>
        <?php
        $trSum = 0;
        $kzsSum = 0;
        $yxkzsSum = 0;
        $ddsSum = 0;
        $cjsSum = 0;
        $cjjeSum = 0;
        $currentQdUuid = null;
        ?>
        <?php foreach ($channelWays as $key => $channelWay): ?>
            <tr>
                <td class="rowspan v-center h-center" data-name="<?= $channelWay['qd'] ?>"><?= $channelWay['qd'] ?></td>
                <td><?= $channelWay['fs'] ?></td>
                <td><?= $tr = getTr($db, $channelWay['qd_uuid'], $timeStart, $timeEnd) ?></td>
                <td><?= $kzs = getKzs($db, $channelWay['fs_uuid'], $timeStart, $timeEnd) ?></td>
                <td><?= $yxkzs = getYxkzs($db, $channelWay['fs_uuid'], $timeStart, $timeEnd) ?></td>
                <td><?= $dds = getDds($db, $channelWay['fs_uuid'], $timeStart, $timeEnd) ?></td>
                <td><?= $cjs = getCjs($db, $channelWay['fs_uuid'], $timeStart, $timeEnd) ?></td>
                <td><?= $cjje = getCjje($db, $channelWay['fs_uuid'], $timeStart, $timeEnd) ?></td>
                <td><?= calculateSingleCB($yxkzs, $tr) ?></td>
                <td><?= calculateSingleCB($dds, $tr) ?></td>
                <td><?= calculateSingleCB($cjs, $tr) ?></td>
                <td><?= calculatePercent($yxkzs, $kzs) ?></td>
                <td><?= calculatePercent($dds, $yxkzs) ?></td>
                <td><?= calculatePercent($cjs, $dds) ?></td>
                <td><?= calculatePercent($cjs, $yxkzs) ?></td>
            </tr>
            <?php
            // 成本总计渠道投入的
            if($currentQdUuid != $channelWay['qd_uuid']){
                $currentQdUuid = $channelWay['qd_uuid'];
                $trSum += $tr;
            }
            $kzsSum += $kzs;
            $yxkzsSum += $yxkzs;
            $ddsSum += $dds;
            $cjsSum += $cjs;
            $cjjeSum += $cjje;
            ?>
        <?php endforeach; ?>
        <tr>
            <td colspan="2" class="v-center h-center">合计</td>
            <td><?= $trSum ?></td>
            <td><?= $kzsSum ?></td>
            <td><?= $yxkzsSum ?></td>
            <td><?= $ddsSum ?></td>
            <td><?= $cjsSum ?></td>
            <td><?= $cjjeSum ?></td>
            <td><?= calculateSingleCB($yxkzsSum, $trSum) ?></td>
            <td><?= calculateSingleCB($ddsSum, $trSum) ?></td>
            <td><?= calculateSingleCB($cjsSum, $trSum) ?></td>
            <td><?= calculatePercent($yxkzsSum, $kzsSum) ?></td>
            <td><?= calculatePercent($ddsSum, $yxkzsSum) ?></td>
            <td><?= calculatePercent($cjsSum, $ddsSum) ?></td>
            <td><?= calculatePercent($cjsSum, $yxkzsSum) ?></td>
        </tr>
    </table>
<?php
include_once __DIR__ . '/../common/footer.php';
