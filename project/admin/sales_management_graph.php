<?php
require_once('system/library.php');
//ログイン画面からログインされていなかった場合はログイン画面へ戻す
confirmAuthAdmin();
$sale = new Sale();

//1ヶ月間の日別売上データを取得
if (($sales_list = $sale->getSalesManagementOneMonth($_GET['year_month'] ?? '')) === false) {
    header('Location: error.php?message=fail&word=salesManagement');
    exit;
}

//取得した売上データをCSVファイル化
if (!empty($_POST['download_csv'])) {
    $error = $sale->dlSalesManagementList($sales_list, $_GET['year_month'] ?? '');
}
?>

<!-- ヘッダー -->
<?php require_once('./template/header.php') ?>
<main class="adm-sales-list">
    <div class="container">
        <div class="list-top clearfix">
            <section class="btn">
                <button type="submit" disabled><?php getPage() ?></button>
            </section>
            <p class="sales-management-list-or-graph">
                <a href="sales_management_list.php<?= !empty($_GET['year_month']) ? '?year_month=' . $_GET['year_month'] : '' ?>">リストで表示する</a>
            </p>
            <form class="selected-month" action="" method="get">
                <input type="month" name="year_month" min="2020-01" value="<?= !empty($_GET['year_month']) ? $_GET['year_month'] : date('Y-m', strtotime('today')) ?>">
                <input type="submit" value="表示">
            </form>
            <div class="chart-type">
                <p class="blue-graph">
                </p>
            </div>
        </div>
        <div class="graph-list">
            <canvas id="sales-graph"></canvas>

        </div>
    </div>
</main>
<script>
    //DBのPHP売上データをJSON形式で取得
    let list = <?= json_encode($sales_list) ?>;
    //日付を配列に置き換え
    let date_data = list.map(function(e) {
        return e.date;
    });
    //総額を配列に置き換え、nullの値は0に置換
    let sales_data = list.map(function(e) {
        return (e.total_price != null ? e.total_price : 0);
    });
    //数量を配列に置き換え、nullの値は0に置換
    let nums_data = list.map(function(e) {
        return (e.total_num != null ? e.total_num : 0);
    });
    //グラフの作成
    function showChart() {
        let ctx = document.getElementById("sales-graph");
        myLineCharts = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: date_data,
                datasets: [
                    {
                        label: '売上',
                        type: 'bar',
                        data: sales_data,
                        borderColor: 'rgba(54,164,235,0.8)',
                        backgroundColor: 'rgba(54,164,235,0.5)',
                        // spanGaps: true,
                        borderWidth: 2,
                        yAxisID: 'y-axis-1',
                    },
                    {
                        label: '個数',
                        type: 'line',
                        data: nums_data,
                        borderColor: 'rgba(254,97,132,0.8)',
                        backgroundColor: 'rgba(254,97,132,0.5)',
                        spanGaps: true,
                        yAxisID: 'y-axis-2',
                    }
                ],
            },
            options: {
                title: {
                    display: true,
                    text: '売上'
                },
                scales: {
                    responsive: false,
                    yAxes: [{
                        id: 'y-axis-1',
                        position: 'left',
                        ticks: {
                            suggestedMax: 1000000,
                            suggestedMin: 0,
                            stepSize: 0,
                            maxTicksLimit: 10,
                            callback: function(value, index, values) {
                                return value + '円'
                            }
                        }
                    }, {
                        id: 'y-axis-2',
                        position: 'right',
                        ticks: {
                            suggestedMax: 50,
                            suggestedMin: 0,
                            stepSize: 0,
                            callback: function(value, index, values) {
                                return value + '個'
                            }
                        }
                    }],
                    xAxes: [{
                        ticks: {
                            autoSkip: true,
                            maxTicksLimit: 32
                        }
                    }]
                },
            }
        });
    }
    //ウインドウを開いたときにグラフを表示
    window.onload = showChart();
</script>
<!-- フッター -->
<?php require_once('./template/footer.php') ?>