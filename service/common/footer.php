<script>
    $(function () {
        // 列合并
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
    });
</script>
</div>
</body>
</html>