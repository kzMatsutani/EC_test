<?php
require_once('system/library.php');
//ログイン画面からログインされていなかった場合はログイン画面へ戻す
confirmAuthAdmin();
$product = new Product();
//GETパラメータが不適切な値の場合はエラー画面へ
$product->checkProductEditType();

//支払情報の取得
$id = isset($_GET['id']) ? $_GET['id'] : '';
if (!($payment = $product->getPaymentList($id))) {
    header('Location: error.php?message=fail&word=getPayment');
    exit;
}
//DBから取得した支払い方法の選択を置き換え、POSTが存在すればPOSTを代入。

//$itemに空配列を初期値として設定
$item = [];
//商品編集の場合は$itemに商品データを追加
if (!empty($_GET['id'])) {
    if (!($item = $product->getProduct($_GET['id']))) {
        header('Location: error.php?message=fail&word=getProduct');
        exit;
    }
}
//再編集で戻ってきた場合はPOSTを優先して代入
$item = $_POST + $item;
?>

<!-- ヘッダー -->
<?php require_once('./template/header.php') ?>
<main class="adm-edit-item">
    <div class="container">
        <section class="item-edit">
            <section class="btn">
                <button type="submit" disabled><?php getPage() ?></button>
            </section>
            <form action="product_conf.php?type=<?= $_GET['type'] ?><?= !empty($_GET['id']) ? '&id=' . $_GET['id'] : '' ?>" method="post">
                <table>
                    <tr>
                        <th>カテゴリー名</th>
                        <td><input type="text" name="name" value="<?= isset($item['name']) ? h($item['name']) : '' ?>"></td>
                    </tr>
                </table>
                <p><input type="submit" name="edit" value="確認画面へ"></p>
            </form>
        </section>
        <section class="img-edit">
            <p class="error"><span><?= !empty($error) ? $error : '' ?></span></p>
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                <table>
                    <tr>
                        <td>カテゴリー画像</td>
                    </tr>
                    <tr>
                        <td class="img">
                            <?= !empty($item['img']) ? '<img src=' . ADMIN_PRODUCT_IMAGE_PATH . $item['img'] . '>' : '' ?>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="file" name="img"></td>
                    </tr>
                </table>
                <p><input type="submit" class="img-submit" name="uploadImg" value="仮画像アップロード" onclick="return editClick()"></p>
            </form>
        </section>
    </div>
</main>
<script>
    'use strict'

    function editClick() {
        let result = window.confirm('画像をアップロードしてもよろしいですか？')
        if (result) {
            return true
        } else {
            return false
        }
    }
</script>
<!-- フッター -->
<?php require_once('./template/footer.php') ?>