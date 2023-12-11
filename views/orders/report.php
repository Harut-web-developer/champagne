<?php $this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/colresizable/1.6.0/colResizable-1.6.min.js', ['depends' => 'yii\web\JqueryAsset', 'position' => \yii\web\View::POS_END]);
$this->params['sub_page'] = $sub_page;
?>
<!Doctype html><html>
<head>
</head>
<body>
<?php // $fileName = 'update'; ?>
<?php //echo $this->render($fileName, []); ?>


<?= $this->render('table', [
    'model' => $model,
    'users' => $users,
    'clients' => $clients,
    'nomenclatures' => $nomenclatures,
    'order_items' => $order_items,
    'total' => $total,
    'is_export' => true,
]) ?>
<div id="events" >
    <button onclick="print_()" style=" margin-left: 3%;">print</button>
    <button onclick="tableToExcel('table', '<?php echo ''// $request->shippingtype->name . '-' . $request->shippingtype->id; ?>')">excel</button>
</div>
<script>
    function print_() {
        document.getElementById('events').style.display = "none";
        window.print();
        document.getElementById('events').style.display = "block";
    }
    function tableToExcel (table, name) {    // $('.need-remove').hide();
        $('.other_tr').css('display', '');
        $('#totals_table').css('display', 'none');
        var uri = 'data:application/vnd.ms-excel;base64,',
            template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">' +
                '<meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">' +                            '<head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>' +
                '<body>' +                            '<table>{table}</table>' +
                '</body></html>',                    base64 = function (s) {
        return window.btoa(unescape(encodeURIComponent(s)))                    },
        format = function (s, c) {
        return s.replace(/{(\w+)}/g, function (m, p) {
                return c[p];                        })
        }
    }
    if (!table.nodeType) table = document.getElementById(table)
        var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML
    }
    var a = document.createElement('a');
    a.href = uri + base64(format(template, ctx))
    a.download = name+'.xls';
    a.click();            }
    window.addEventListener("load", function() {                $('table').colResizable();
    });                $('.other_tr').css('display', 'none');
    $('#totals_table').css('display', '');
</script>
</body>
</html>