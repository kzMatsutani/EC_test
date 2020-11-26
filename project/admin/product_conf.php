<?php
require_once('system/library.php');
//ログイン画面からログインされていなかった場合はログイン画面へ戻す
confirmAuthAdmin();
$product = new Product();
//GETパラメータが不適切な値の場合はエラー画面へ
$product->checkProductEditType();

//支払情報の取得
if (!($payment = $product->getPaymentList())) {
    header('Location: error.php?message=fail&word=getPayment');
    exit;
}
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
                <input type="hidden" name="name" value="<?=h($_POST['name'])?>">
                <input type="hidden" name="sub_name" value="<?=h($_POST['sub_name'])?>">
                <input type="hidden" name="day" value="<?=h($_POST['day'])?>">
                <input type="hidden" name="price" value="<?=h($_POST['price'])?>">
                <input type="hidden" name="description" value="<?=h($_POST['description'])?>">
                <?php if (!empty($_POST['selected_payment'])) : ?>
                    <?php foreach ($_POST['selected_payment'] as $key => $value) : ?>
                        <input type="hidden" name="selected_payment[<?=$key?>]" value="1">
                    <?php endforeach; ?>
                <?php endif; ?>
                <table>
                    <tr>
                        <th>商品名</th>
                        <td><?=h($_POST['name'])?></td>
                    </tr>
                    <tr>
                        <th>商品名(サブ)</th>
                        <td><?=h($_POST['sub_name'])?></td>
                    </tr>
                    <tr>
                        <th>日数</th>
                        <td><?=h($_POST['day'])?></td>
                    </tr>
                    <tr>
                        <th>価格</th>
                        <td><?=h($_POST['price'])?></td>
                    </tr>
                    <tr>
                        <th>説明文</th>
                        <td><?=nl2br(h($_POST['description']))?></td>
                    </tr>
                    <tr>
                        <th>支払い方法</th>
                        <td>
                            <?php if (!empty($_POST['selected_payment'])) : ?>
                                <?php foreach ($_POST['selected_payment'] as $key => $value) : ?>
                                    <?=$payment[$key]['name']?>
                                <?php endforeach; ?>
                            <?php endif;?>
                        </td>
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
