<?php
require_once('system/library.php');
//ログイン画面からログインされていなかった場合はログイン画面へ戻す
confirmAuthAdmin();
$product = new Product();
//GETパラメータが不適切な値の場合はエラー画面へ
$product->checkProductEditType();
//トークンが生成されていなければエラー画面へ
productAuthToken($_POST['productToken']);

//カテゴリーの取得
$id = isset($_GET['id']) ? $_GET['id'] : '';
if (($categories = $product->getCategoryList()) === false) {
    header('Location: error.php?message=fail&word=getPayment');
    exit;
}

//販売状況の取得
if (($sales_status = $product->getSalesStatusList()) === false) {
    header('Location: error.php');
    exit;
}

echo '<pre>';
var_dump($_POST['category']);
echo '</pre>';


?>

<!-- ヘッダー -->
<?php require_once('./template/header.php')?>
<main class="adm-edit-item">
    <div class="container">
        <section class="item-conf">
            <section class="btn">
                <button type="submit" disabled><?php getPage() ?></button>
            </section>
            <form action="product_done.php?type=<?=$_GET['type']?><?=!empty($_GET['id']) ? '&id='. $_GET['id'] : ''?>" method="post">
                <!-- トークン埋め込み -->
                <input type="hidden" name="productToken" value="<?=$_SESSION['productToken']?>">
                <input type="hidden" name="name" value="<?=h($_POST['name'])?>">
                <input type="hidden" name="price" value="<?=h($_POST['price'])?>">
                <input type="hidden" name="point" value="<?=h($_POST['point'])?>">
                <input type="hidden" name="shipping" value="<?=h($_POST['shipping'])?>">
                <input type="hidden" name="public_status" value="<?=h($_POST['public_status'])?>">
                <input type="hidden" name="sales_status" value="<?=h($_POST['sales_status'])?>">
                <input type="hidden" name="category" value="<?=h($_POST['category'])?>">
                <input type="hidden" name="title" value="<?=h($_POST['title'])?>">
                <input type="hidden" name="body" value="<?=h($_POST['body'])?>">
                <table>
                    <tr>
                        <th>商品名</th>
                        <td><?=h($_POST['name'])?></td>
                    </tr>
                    <tr>
                        <th>価格</th>
                        <td><?=h($_POST['price'])?></td>
                    </tr>
                    <tr>
                        <th>付属ポイント</th>
                        <td><?=h($_POST['point'])?></td>
                    </tr>
                    <tr>
                        <th>発送目安日</th>
                        <td><?=h($_POST['shipping'])?>日</td>
                    </tr>
                    <tr>
                        <th>掲載状況</th>
                        <td>
                            <?php if ($_POST['public_status'] == 0) : ?>
                                非掲載
                            <?php elseif ($_POST['public_status'] == 1) : ?>
                                掲載
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>販売状況</th>
                        <td><?=h($sales_status[$_POST['sales_status']]['name'])?></td>
                    </tr>
                    <tr>
                        <th>カテゴリー</th>
                        <td><?=$_POST['category'] == 0 ? '未選択' : h($categories[$_POST['category']]['name'])?></td>
                    </tr>
                    <tr>
                        <th>商品説明</th>
                        <td><?=nl2br(h($_POST['body']))?></td>
                    </tr>
                </table>
                <p>
                    <input type="submit" name="update" value="<?=$_GET['type'] == 'update' ? '編集' : '新規登録'?>">
                    <input type="submit" name="cancel" formaction="product_edit.php?type=<?=$_GET['type']?><?=!empty($_GET['id']) ? '&id='. $_GET['id'] :''?>" value="戻る">
                </p>
            </form>
        </section>
    </div>
</main>
<!-- フッター -->
<?php require_once('./template/footer.php')?>
