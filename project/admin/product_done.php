<?php
require_once('system/library.php');
//ログイン画面からログインされていなかった場合はログイン画面へ戻す
confirmAuthAdmin();
$product = new Product();
//GETパラメータが不適切な値の場合はエラー画面へ
$product->checkProductEditType();
$selected_payment = isset($_POST['selected_payment']) ? $_POST['selected_payment'] : '';

//商品の新規登録(GETパラメーターがcreate)
if (!empty($_POST['update']) && $_GET['type'] == 'create') {
    if (!($product->createProduct($_POST['name'], $_POST['sub_name'], $_POST['day'], $_POST['price'], $_POST['description'], $selected_payment))) {
        header('Location: error.php?message=fail&word=createProduct');
        exit;
    }
}

//商品データの更新(GETパラメーターがupdate)
if (!empty($_POST['update']) && $_GET['type'] == 'update') {
    if (!($product->updateProduct($_GET['id'], $_POST['name'], $_POST['sub_name'], $_POST['day'], $_POST['price'], $_POST['description'], $selected_payment))) {
        header('Location: error.php?message=fail&word=updateProduct');
        exit;
    }
}
?>

<!-- ヘッダー -->
<?php require_once('./template/header.php')?>
<main class="adm-product-list">
    <div class="container">
        <section class="btn">
            <button type="submit" disabled><?php getPage() ?></button>
        </section>
        <section class="done">
            <p><?=$_GET['type'] == 'update' ? '編集' : '新規登録'?>が完了しました</p>
        </section>
    </div>
</main>
<!-- フッター -->
<?php require_once('./template/footer.php')?>
