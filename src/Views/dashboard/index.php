<?php include_once __DIR__ . '/../htmlInit.php'; ?>
<?php require_once __DIR__ . '/../htmlHead.php'; ?>
<?php require_once __DIR__ . '/../bodyContentInit.php'; ?>

<?php

use App\Controllers\FunctionController;
$functionController = new FunctionController();

?>

    <?php

    $statusTotals = [];
    $financeTotals = [];
    $financeTotalsPrices = [];
    $totalRows = $orderStatus->count();

    foreach ($orderStatus as $order) {
        // Agrupar por status
        if (!isset($statusTotals[$order->type_status_name])) {
            $statusTotals[$order->type_status_name] = 0;
        }
        $statusTotals[$order->type_status_name] += 1;

        // Agrupar por finance color
        if (!isset($financeTotals[$order->type_status_finance_name])) {
            $financeTotals[$order->type_status_finance_name] = 0;
            $financeTotalsPrices[$order->type_status_finance_name] = [
                "total_price" => 0, // Inicializa com zero
                "color" => $order->type_finance_color,
                "name" => $order->type_status_finance_name,
            ];
        }
        $financeTotals[$order->type_status_finance_name] += 1;
        $financeTotalsPrices[$order->type_status_finance_name]['total_price'] += $order->total_price;
    }

    $statusResult = [];
    foreach ($statusTotals as $name => $value) {
        $statusResult[] = ['value' => $value, 'name' => "$name - " . round(($value / $totalRows) * 100, 2) . "%"];
    }

    $financeResult = [];
    foreach ($financeTotals as $name => $value) {
        $financeResult[] = ['value' => $value, 'name' => "$name - " . round(($value / $totalRows) * 100, 2) . "%"];
    }

    ?>

    <div class="section inset mt-2 mb-2">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-md-12">
                <div class=" mt-2 mb-2">
                    <div class="card">

                        <div class="table-responsive">
                            <div class="text-center fw-bold border-bottom py-2">
                                <?=$functionController->locale('menu_item_orders')?>s
                            </div>
                            <?php if($orderStatus->count()) : ?>
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="text-center" scope="col">ID</th>
                                    <th class="text-center" scope="col"><?=$functionController->locale('menu_item_client')?></th>
                                    <th class="text-center" scope="col">Data</th>
                                    <th class="text-center" scope="col"><?=$functionController->locale('menu_item_status')?></th>
                                    <th class="text-center" scope="col"><?=$functionController->locale('menu_item_finance')?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($orderStatus as $item): ?>
                                <tr>
                                    <th class="text-center" scope="row">
                                        <a href="/order/<?=$item->id?>" target="_blank" class="d-flex align-items-center justify-content-center">
                                            <?=$item->id?>
                                            <ion-icon name="search-outline" class="ms-1"></ion-icon>
                                        </a>
                                    </th>
                                    <td class="text-center"><?=$item->client_name?></td>
                                    <td class="text-center"><?=date('d/m/Y', strtotime($item->created_at))?></td>
                                    <td class="text-center">
                                        <span class="badge badge-<?=$item->type_status_color?>">
                                            <?=$item->type_status_name?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-<?=$item->type_finance_color?>">
                                            <?=$item->type_status_finance_name?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>

                            </table>

                            <div class="text-center fw-bold border-bottom py-2 w-100">
                                <a href="/orders/" class="d-flex align-items-center justify-content-center">
                                    <?=$functionController->locale('label_show_all_orders')?>
                                    <ion-icon name="arrow-forward-circle-outline" class="ms-1"></ion-icon>
                                </a>
                            </div>
                            <?php else: ?>
                                <div class="text-center fw-bold border-bottom py-2 w-100">
                                    <a href="javascript:void(0)" class="d-flex text-warning align-items-center justify-content-center">
                                        <?=$functionController->locale('not_found_results')?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="section inset">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-md-12">
                <div class="text-center rounded fw-bold bg-white border-bottom py-2">
                    <?=$functionController->locale('label_financial_movements')?>
                </div>
                <?php if($financeTotalsPrices): ?>
                <div class="row justify-content-center">
                    <?php foreach ($financeTotalsPrices as $name => $value): ?>
                        <div class="section mt-2 mb-2 col-lg-6 col-md-12">
                            <div class="stat-box">
                                <div class="title"><?=$value['name']?></div>
                                <div class="value text-<?=$value['color']?>"><?=$functionController->formatCurrencyBR($value['total_price'])?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center mb-2 rounded fw-bold bg-white border-bottom py-2">
                    <?=$functionController->locale('not_found_results')?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- * Stats -->

    <!-- Stats -->
    <div class="section inset">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-md-12">
                <div class="row justify-content-center">
                    <div class="section mt-2 mb-2 col-lg-6 col-md-12">
                        <div class="stat-box justify-content-center align-items-center w-100" id="chart-status" style="height: 400px">

                        </div>
                    </div>
                    <div class="section mt-2 mb-2 col-lg-6 col-md-12">
                        <div class="stat-box justify-content-center align-items-center w-100" id="chart-finance" style="height: 400px">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- * Stats -->


<?php require_once __DIR__ . '/../bodyContentEnd.php'; ?>

<script src="/assets/js/plugins/apexcharts/apexcharts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.1/dist/echarts.min.js"></script>

<script>
    $(document).ready(() => {

        function echart(data){

            let chartDom = document.getElementById(data.div);
            let myChart = echarts.init(chartDom);
            let option = {
                title: {
                    text: data.title,
                    left: 'center'
                },
                tooltip: {
                    trigger: 'item'
                },
                legend: {
                    orient: 'vertical',
                    bottom: 'bottom'
                },
                series: [
                    {
                        name: data.title,
                        type: 'pie',
                        radius: '50%',
                        top: 25,
                        data: data.data,
                        emphasis: {
                            itemStyle: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        }
                    }
                ]
            };
            option && myChart.setOption(option);
        }


        let data = {};

        data.div = "chart-status";
        data.title = "<?=$functionController->locale('label_chart_production')?>";
        data.data = <?=$functionController->parseObjectToJson($statusResult)?>;
        echart(data)

        data.div = "chart-finance";
        data.title = "<?=$functionController->locale('label_chart_finance')?>";
        data.data = <?=$functionController->parseObjectToJson($financeResult)?>;
        echart(data)

    })
</script>

<?php require_once __DIR__ . '/../htmlEnd.php';?>
