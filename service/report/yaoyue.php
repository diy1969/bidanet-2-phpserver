<?php
/** @var $companyId string */
/** @var $db DB */
include_once __DIR__ . '/../common/header.php';

date_default_timezone_set('PRC');

$timeStart = isset($_GET['start-time']) ? $_GET['start-time'] : time() * 1000;
$timeEnd = isset($_GET['end-time']) ? $_GET['end-time'] : time() * 1000;
$timeStart = getDay0Time($timeStart);
$timeEnd = getDay24Time($timeEnd);
$formatTimeStart = date('Y年m月d日', $timeStart / 1000);
$formatTimeEnd = date('Y年m月d日', $timeEnd / 1000);

// 获取所有组
function getWorkGroups($db, $companyId)
{
    return $db->query("SELECT a.`name` as zu, a.uuid as zu_uuid, b.`name` as rm, b.uuid as rm_uuid from sys_work_center a INNER JOIN sys_user b ON a.uuid = b.workcenter_id LEFT JOIN sys_department c ON a.department_id = c.uuid WHERE c.group_flag=2 AND a.company_id = :id ORDER BY a.create_time ASC",
        array('id' => $companyId)
    );
}

// 未报备客资数
function getWbbkzs($db, $userId, $timeStart, $timeEnd)
{
    return $db->single("SELECT COUNT(a.id) as wbbkzs FROM bus_customer a WHERE a.customer_user_status = 2 AND a.invite_id = :id AND a.yycreate_time BETWEEN :timeStart AND :timeEnd",
        array('id' => $userId, 'timeStart' => $timeStart, 'timeEnd' => $timeEnd)
    );
}

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

$groups = getWorkGroups($db, $companyId);
?>
    <form action="" method="get" class="row">
        <div class="form-group col-md-6">
            <label>起止日期</label>
            <div class="input-daterange input-group" id="datepicker">
                <input type="text" class="picker form-control" data-target="start-time"
                       value="<?= $formatTimeStart ?>">
                <span class="input-group-addon">to</span>
                <input type="text" class="picker form-control" data-target="end-time"
                       value="<?= $formatTimeEnd ?>">
            </div>
            <input type="hidden" name="start-time" value="<?= $timeStart ?>">
            <input type="hidden" name="end-time" value="<?= $timeEnd ?>">
        </div>
        <div class="form-group col-md-12">
            <input type="submit" class="btn btn-primary" value="查询">
            <button class="export-excel btn btn-success" data-table="#yy-report"
                    data-file-name="邀约报表-<?=$formatTimeStart.'-'.$formatTimeEnd?>">导出</button>
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
            $('input[name=' + $(this).data('target') + ']').val(e.date.getTime());
        });
    </script>

    <table id="yy-report" class="table table-bordered table-responsive">
        <thead>
        <tr class="bg-primary">
            <th>组</th>
            <th>人名</th>
            <th>未报备客资数</th>
            <th>有效客资数</th>
            <th>邀约到店数</th>
            <th>成交数</th>
            <th>到店率</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $wbbkzsSum = 0;
        $yxkzsSum = 0;
        $yyddsSum = 0;
        $cjsSum = 0;
        ?>
        <?php foreach ($groups as $key => $group): ?>
            <tr>
                <td class="rowspan v-center h-center" data-name="<?= $group['zu'] ?>"><?= $group['zu'] ?></td>
                <td><?= $group['rm'] ?></td>
                <td><?= $wbbkzs = getWbbkzs($db, $group['rm_uuid'], $timeStart, $timeEnd) ?></td>
                <td><?= $yxkzs = getYxkzs($db, $group['rm_uuid'], $timeStart, $timeEnd) ?></td>
                <td><?= $yydds = getYydds($db, $group['rm_uuid'], $timeStart, $timeEnd) ?></td>
                <td><?= $cjs = getCjs($db, $group['rm_uuid'], $timeStart, $timeEnd) ?></td>
                <td><?= calculatePercent($yydds, $yxkzs) ?></td>
            </tr>
            <?php
            $wbbkzsSum += $wbbkzs;
            $yxkzsSum += $yxkzs;
            $yyddsSum += $yydds;
            $cjsSum += $cjs;
            ?>
        <?php endforeach; ?>
        <tr>
            <td colspan="2" class="v-center h-center">合计</td>
            <td><?= $wbbkzsSum ?></td>
            <td><?= $yxkzsSum ?></td>
            <td><?= $yyddsSum ?></td>
            <td><?= $cjsSum ?></td>
            <td><?= calculatePercent($yyddsSum, $yxkzsSum) ?></td>
        </tr>
        </tbody>
    </table>
<?php
include_once __DIR__ . '/../common/footer.php';
