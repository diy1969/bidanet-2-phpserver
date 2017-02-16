<script>
    $(function () {
        // 行合并
        var rowspanArr = {};
        $('.rowspan').each(function () {
            if (rowspanArr[$(this).data('name')]) {
                rowspanArr[$(this).data('name')]++;
            } else {
                rowspanArr[$(this).data('name')] = 1;
            }
        });
        $.each(rowspanArr, function (name, number) {
            var elem = $('[data-name=' + name + ']');
            elem.eq(0).attr('rowspan', number);
            for (var i = 1; i < number; i++) {
                elem.eq(i).remove();
            }
        });

        // 定住元素
        $(".pinned").freezeHeader();

        // 导出 excel
        $('.export-excel').click(function () {
            var table = $(this).data('table'),
                type = $(this).data('type') || 'excel',
                fileName = $(this).data('file-name') || '导出数据';
            $(table).tableExport({
                type: type,
                fileName: fileName
            });
        });

        // 时间范围选择，时间转时间戳
        $('.input-daterange').datepicker({
            autoclose: true,
            language: 'zh-CN',
            minViewMode: 'days',
            format: 'yyyy年mm月dd日'
        });
        $('.input-daterange .picker').on('changeDate', function (e) {
            $('input[name=' + $(this).data('target') + ']').val(e.date.getTime());
        });
    });
</script>
</div>
</body>
</html>