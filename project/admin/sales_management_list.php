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
<?php require_once('./template/header.php')?>
<main class="adm-sales-list">
    <div class="container">
        <div class="list-top clearfix">
            <section class="btn">
                <button type="submit" disabled><?php getPage() ?></button>
            </section>
            <p class="sales-management-list-or-graph">
                <a href="sales_management_graph.php<?=!empty($_GET['year_month']) ? '?year_month=' . $_GET['year_month'] : ''?>">グラフで表示する</a>
            </p>
            <form class="selected-month" action="" method="get">
                <input type="month" name="year_month" min="2020-01" value="<?=!empty($_GET['year_month']) ? $_GET['year_month'] : date('Y-m', strtotime('today'))?>">
                <input type="submit" value="表示">
            </form>
            <form class="csv-download" action="" method="post">
                <input name="download_csv" type="submit" value="CSVダウンロード"><span class="error"><?=$error ?? ''?></span>
            </form>
        </div>
        <section class="sales-list-table">
            <table>
                <tr>
                    <td>日付</td>
                    <td>商品名</td>
                    <td>個数</td>
                    <td>送料</td>
                    <td>総額</td>
                </tr>
                <?php if (empty($sales_list)) : ?>
                    <tr>
                        <td colspan="5">データが見つかりませんでした</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($sales_list as $day) : ?>
                        <tr>
                            <td class="nowrap"><?=h($day['date'])?></td>
                            <td class="nowrap"><?=h($day['product_name'])?></td>
                            <td class="nowrap"><?=h(number_format($day['total_num']))?></td>
                            <td class="nowrap"><?=h(number_format($day['total_shipping_price']))?></td>
                            <td class="nowrap"><?=h(number_format($day['total_price']))?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </table>
        </section>
    </div>
</main>
<!-- フッター -->
<?php require_once('./template/footer.php')?>