<?php
$this->params['sub_page'] = $sub_page;
$this->params['date_tab'] = $date_tab;
$session = Yii::$app->session;
?>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between pb-0">
                    <div class="card-title mb-0">
                        <h5 class="m-0 me-2">Պատվերի վիճակագրություն</h5>
<!--                        <small class="text-muted">42.82k Ընդհանուր վաճառք</small>-->
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3" style="position: relative;">
                        <div id="chart1"></div>


                        <div class="resize-triggers"><div class="expand-trigger"><div style="width: 398px; height: 139px;"></div></div><div class="contract-trigger"></div></div></div>
                    <ul class="p-0 m-0" style="max-height: 160px;overflow-y: scroll;">
                        <?php
                        if (!empty($chart_round_products)){
                            foreach ($chart_round_products as $item){
                                ?>
                                <li class="d-flex mb-4 pb-1">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <!--                                <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-mobile-alt"></i></span>-->
                                    </div>
                                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                        <div class="me-2">
                                            <span class="mb-0 chartRoundName"><?=$item['name']?></span>
                                            <!--                                    <small class="text-muted">կիսաչոր</small>-->
                                        </div>
                                        <div class="user-progress">
                                            <small class="fw-medium"><?=number_format($item['price']) . ' դր.'?></small>
                                        </div>
                                    </div>
                                </li>
                                <?php
                            }
                        }
                        ?>

                    </ul>
                </div>
            </div>
        </div>
        <!-- Transactions -->
            <div class="col-md-6 col-lg-4 order-1 mb-4">
                <div class="card h-100" style="max-height: 610px;">
                    <div class="card-header">
                        <h5 class="card-title m-0 me-2">Վճարումներ</h5>
                        <?php if ($session['role_id'] == 1){ ?>

                            <select id="singleClients" class="js-example-basic-single form-control mt-2 filterClientsChart" name="client_id">
                                <option value="null">Ընտրել հաճախորդին</option>
                                <?php
                                foreach ($get_clients as $client){
                                    ?>
                                    <option value="<?=$client['id']?>"><?=$client['name']?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        <?php } ?>

                    </div>
                    <div class="card-body  paymentsPart" style="overflow-y: scroll;">
                        <ul class="p-0 m-0">
                            <?php
                            if ($session['role_id'] == 1){
                            if (!empty($clients_payment)){
                                foreach ($clients_payment as $item){
                                    ?>
                                    <li class="d-flex mb-4 pb-1">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <img src="/img/icons/unicons/chart.png" alt="User" class="rounded">
                                        </div>
                                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0"><?=$item['name']?></h6>
                                                <small class="text-muted d-block mb-1">Վճարված</small>
                                            </div>
                                            <div class="user-progress d-flex align-items-center gap-1">
                                                <h6 class="mb-0"><?=number_format($item['payment_sum']) . ' դր.'?></h6>
                                                <!--                                            <span class="text-muted">դր.</span>-->
                                            </div>
                                        </div>
                                    </li>
                                    <?php
                                }
                            }else{
                                ?>
                                <li class="d-flex mb-4 pb-1">Վճարված ապրանք չկա</li>
                                <?php
                            }
                            }else{?>
                                <li class="d-flex mb-4 pb-1">Սահմանափակված տվյալներ</li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-lg-4 order-2">
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-6 mb-4">
                        <div class="card">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="/img/icons/unicons/chart-success.png" alt="chart success" class="rounded">
                                </div>
                            </div>
                            <span class="fw-medium d-block mb-1">Շահույթ</span>
                            <h4 style="font-size: 15px" class="card-title mb-2 orders_profit"><?=number_format($cost) . ' դր.'?></h4>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-6 mb-4">
                        <div class="card">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="/img/icons/unicons/wallet-info.png" alt="Credit Card" class="rounded">
                                </div>
                            </div>
                            <span class="fw-medium d-block mb-1">Վաճառք</span>
                            <h4 style="font-size: 15px" class="card-title text-nowrap mb-2 orders_sale"><?=number_format($sale) . ' դր.'?></h4>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-6 mb-4">
                        <div class="card">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="/img/icons/unicons/paypal.png" alt="Credit Card" class="rounded">
                                </div>
                            </div>
                            <span class="fw-medium d-block mb-1">Վճարումներ</span>
                            <?php if ($session['role_id'] == 1){ ?>
                                <h4 style="font-size: 15px" class="card-title text-nowrap mb-2 orders_pay"><?=number_format($payment) . ' դր.'?></h4>
                            <?php }else{?>
                                <h4 style="font-size: 15px" class="card-title text-nowrap mb-2 orders_pay">Սահմանափակված</h4>
                            <?php } ?>

                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-6 mb-4">
                        <div class="card">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="/img/icons/unicons/cc-primary.png" alt="Credit Card" class="rounded">
                                </div>
                            </div>
                            <span class="fw-medium d-block mb-1">Գործարքներ</span>
                            <h4 style="font-size: 15px" class="card-title mb-2 orders_deal"><?=number_format($deal) . ' դր.'?></h4>
                        </div>
                    </div>
                </div>
            </div>

    </div>
        <div class="row">
            <div id="chart"></div>
        </div>
        <div class="row">
            <div id="chart2"></div>
        </div>

</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.42.0/apexcharts.min.js" ></script>

<script>

    var options = {
        series: [{
            name: 'Նախորդ տարի',
            data: <?php echo json_encode($line_chart_orders_last_year); ?>,
        }, {
            name: 'Այս տարի',
            data: <?php echo json_encode($line_chart_orders_this_year); ?>,
        }],
        chart: {
            height: 350,
            type: 'area'
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth'
        },
        xaxis: {
            type: 'date',
            categories: <?php echo json_encode($line_chart_orders_label); ?>,
        },
        tooltip: {
            x: {
                format: 'dd/MM/yy HH:mm'
            },
        },
    };
        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();


    var options1 = {
        series: <?php echo json_encode($round_chart_percent); ?>,
        chart: {
            height: 350,
            type: 'radialBar',
        },
        plotOptions: {
            radialBar: {
                dataLabels: {
                    name: {
                        fontSize: '22px',
                    },
                    value: {
                        fontSize: '16px',
                    },
                    total: {
                        show: true,
                        label: 'Ընդհանուր',
                        formatter: function (w) {
                            // By default this function returns the average of all series. The below is just an example to show the use of custom formatter function
                            return '<?php echo number_format($chart_round_total,'0',',',',') ?>'
                        }
                    }
                }
            }
        },
        labels: <?php echo json_encode($round_chart_label); ?>
    };

    var chart1 = new ApexCharts(document.querySelector("#chart1"), options1);
    chart1.render();

    var options2 = {
        series: [{
            name: "Պարտք",
            data: <?php echo json_encode($line_chart_number); ?>
        }],
        chart: {
            height: 350,
            type: 'line',
            zoom: {
                enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'straight'
        },
        title: {
            text: 'Պարտքերի գրաֆիկ',
            align: 'left'
        },
        grid: {
            row: {
                colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                opacity: 0.5
            },
        },
        xaxis: {
            categories: <?php echo json_encode($line_chart_label); ?>
        }
    };


        var chart2 = new ApexCharts(document.querySelector("#chart2"), options2);
        chart2.render();

</script>

<?php
//$this->registerJsFile(
//    '@web/js/charts.js',
//    ['depends' => [\yii\web\JqueryAsset::class]]
//);
//?>
