<?php
/** @var $companyId string */
/** @var $db DB */
include_once __DIR__ . '/../common/header.php';

date_default_timezone_set('PRC');

$timeStart = isset($_GET['start-time']) ? $_GET['start-time'] : time() * 1000;
$timeEnd = isset($_GET['end-time']) ? $_GET['end-time'] : time() * 1000;
$timeStart = getDay0Time($timeStart);
$timeEnd = getDay24Time($timeEnd);

// 获取所有组
function getWorkGroups($db, $companyId)
{
    return $db->query("SELECT a.`name` as zu, a.uuid as zu_uuid, b.`name` as rm, b.uuid as rm_uuid from sys_work_center a INNER JOIN sys_user b ON a.uuid = b.workcenter_id LEFT JOIN sys_department c ON a.department_id = c.uuid WHERE c.group_flag=1 AND a.company_id = :id ORDER BY a.create_time ASC",
        array('id' => $companyId)
    );
}

// 客资数
function getKzs($db, $userId, $timeStart, $timeEnd)
{
    return $db->single("SELECT COUNT(a.id) as kzs FROM bus_customer a WHERE a.customer_service_id = :id AND a.zkcreate_time BETWEEN :timeStart AND :timeEnd",
        array('id' => $userId, 'timeStart' => $timeStart, 'timeEnd' => $timeEnd)
    );
}

// 有效客资数
function getYxkzs($db, $userId, $timeStart, $timeEnd)
{
    return $db->single("SELECT COUNT(a.id) as yxkzs FROM bus_customer a WHERE a.valid_flag=1 AND a.customer_service_id = :id AND a.zkcreate_time BETWEEN :timeStart AND :timeEnd",
        array('id' => $userId, 'timeStart' => $timeStart, 'timeEnd' => $timeEnd)
    );
}

// 到店数
function getDds($db, $userId, $timeStart, $timeEnd)
{
    return $db->single("SELECT COUNT(a.id) as dds FROM bus_customer a WHERE a.customer_user_status IN(6,7,9,10) AND a.customer_service_id = :id AND a.zkcreate_time BETWEEN :timeStart AND :timeEnd",
        array('id' => $userId, 'timeStart' => $timeStart, 'timeEnd' => $timeEnd)
    );
}

// 成交数
function getCjs($db, $userId, $timeStart, $timeEnd)
{
    return $db->single("SELECT COUNT(a.id) as cjs FROM bus_customer a WHERE a.customer_user_status = 9 AND a.customer_service_id = :id AND a.zkcreate_time BETWEEN :timeStart AND :timeEnd",
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
                       value="<?= date('Y年m月d日', $timeStart / 1000) ?>">
                <span class="input-group-addon">to</span>
                <input type="text" class="picker form-control" data-target="end-time"
                       value="<?= date('Y年m月d日', $timeEnd / 1000) ?>">
            </div>
            <input type="hidden" name="start-time" value="<?= $timeStart ?>">
            <input type="hidden" name="end-time" value="<?= $timeEnd ?>">
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
        <thead>
        <tr class="bg-primary">
            <th>组</th>
            <th>人名</th>
            <th>客资数</th>
            <th>有效客资数</th>
            <th>到店数</th>
            <th>成交数</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $kzsSum = 0;
        $yxkzsSum = 0;
        $ddsSum = 0;
        $cjsSum = 0;
        ?>
        <?php foreach ($groups as $key => $group): ?>
            <tr>
                <td class="rowspan v-center h-center" data-name="<?= $group['zu'] ?>"><?= $group['zu'] ?></td>
                <td><?= $group['rm'] ?></td>
                <td><?= $kzs = getKzs($db, $group['rm_uuid'], $timeStart, $timeEnd) ?></td>
                <td><?= $yxkzs = getYxkzs($db, $group['rm_uuid'], $timeStart, $timeEnd) ?></td>
                <td><?= $dds = getDds($db, $group['rm_uuid'], $timeStart, $timeEnd) ?></td>
                <td><?= $cjs = getCjs($db, $group['rm_uuid'], $timeStart, $timeEnd) ?></td>
            </tr>
            <?php
            $kzsSum += $kzs;
            $yxkzsSum += $yxkzs;
            $ddsSum += $dds;
            $cjsSum += $cjs;
            ?>
        <?php endforeach; ?>
        <tr>
            <td colspan="2" class="v-center h-center">合计</td>
            <td><?= $kzsSum ?></td>
            <td><?= $yxkzsSum ?></td>
            <td><?= $ddsSum ?></td>
            <td><?= $cjsSum ?></td>
        </tr>
        </tbody>
    </table>
<?php
include_once __DIR__ . '/../common/footer.php';
