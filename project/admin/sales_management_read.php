<?php
require_once('system/library.php');
//ログイン画面からログインされていなかった場合はログイン画面へ戻す
confirmAuthAdmin();
$sale = new Sale();

if (!empty($_FILES['csv'])) {
    $list = $sale->readSalesManagementList($_FILES['csv']);
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
            <form action="" method="post" enctype="multipart/form-data">
                <input type="file" name="csv">
                <input type="submit" value="読み込む">
            </form>
        </div>
        <section class="sales-list-table">
            <table>
                <?php if (empty($list)) : ?>
                    <tr>
                        <td>日付</td>
                        <td>商品名</td>
                        <td>個数</td>
                        <td>送料</td>
                        <td>販売金額</td>
                    </tr>
                    <tr>
                        <td colspan="5">データが見つかりませんでした</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($list as $day) : ?>
                        <tr>
                            <td class="nowrap"><?=isset($day[0]) ? h($day[0]) : ''?></td>
                            <td class="nowrap"><?=isset($day[1]) ? h($day[1]) : ''?></td>
                            <td class="nowrap"><?=isset($day[2]) ? h($day[2]) : ''?></td>
                            <td class="nowrap"><?=isset($day[3]) ? h($day[3]) : ''?></td>
                            <td class="nowrap"><?=isset($day[4]) ? h($day[4]) : ''?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </table>
        </section>
    </div>
</main>
<!-- フッター -->
<?php require_once('./template/footer.php')?>