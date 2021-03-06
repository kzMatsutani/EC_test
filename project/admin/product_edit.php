<?php
require_once('system/library.php');
//ログイン画面からログインされていなかった場合はログイン画面へ戻す
confirmAuthAdmin();
$product = new Product();
//GETパラメータが不適切な値の場合はエラー画面へ
$product->checkProductEditType();
//トークン生成
$_SESSION['productToken'] = getToken();

//画像アップロードする場合
if (!empty($_POST['uploadImg'])) {
    $error = $product->uploadProductImage($_FILES['img'], $_POST['id']);
}

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

//画像削除ボタンが押されたとき
if (!empty($_POST['delete_image'])) {
    $product->deleteProductImage($_GET['id'], $_POST['delete_image']);
}


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
<?php require_once('./template/header.php')?>
<main class="adm-edit-item">
    <div class="container">
        <section class="item-edit">
            <section class="btn">
                <button type="submit" disabled><?php getPage() ?></button>
            </section>
            <form action="product_conf.php?type=<?=$_GET['type']?><?=!empty($_GET['id']) ? '&id=' . $_GET['id'] : ''?>" method="post">
                <!-- トークン埋め込み -->
                <input type="hidden" name="productToken" value="<?=$_SESSION['productToken']?>">
                <table>
                    <tr>
                        <th>商品名</th>
                        <td><input type="text" name="name" value="<?=isset($item['name']) ? h($item['name']) : ''?>"></td>
                    </tr>
                    <tr>
                        <th>価格</th>
                        <td><input type="text" name="price" value="<?=isset($item['price']) ? h($item['price']) : ''?>"></td>
                    </tr>
                    <tr>
                        <th>在庫数</th>
                        <td><input type="text" name="qty" value="<?=isset($item['qty']) ? h($item['qty']) : ''?>"></td>
                    </tr>
                    <tr>
                        <th>タイトル</th>
                        <td><input type="text" name="title" value="<?=isset($item['title']) ? h($item['title']) : ''?>"></td>
                    </tr>
                    <tr>
                        <th>ポイント</th>
                        <td><input type="number" name="point" value="<?=isset($item['point']) ? h($item['point']) : ''?>"></td>
                    </tr>
                    <tr>
                        <th>出荷目安</th>
                        <td><input type="number" name="shipping" value="<?=isset($item['shipping']) ? h($item['shipping']) : ''?>"></td>
                    </tr>
                    <tr>
                        <th>掲載状況</th>
                        <td>
                            <label>
                                <input type="radio" name="public_status" value="0" checked>
                                非掲載
                            </label>
                            <label>
                                <input type="radio" name="public_status" value="1"<?=isset($item['public_status']) && $item['public_status'] == 1 ? ' checked' : ''?>>
                                掲載
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th>販売状況</th>
                        <td>
                            <?php foreach ($sales_status as $value) : ?>
                                <label>
                                    <input type="radio" name="sales_status" value="<?=$value['id']?>"
                                        <?=(isset($item['sales_status']) && $item['sales_status'] == $value['id']) || $value['id'] == 0 ? ' checked' : ''?>>
                                    <?=$value['name']?>
                                </label>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>カテゴリー</th>
                        <td>
                        <select name="category">
                            <option value="0" ?>未選択
                            <?php foreach ($categories as $val) : ?>
                                <option value="<?=$val['id']?>"<?=isset($item['category']) && $item['category'] == $val['id'] ? ' selected': ''?>><?=$val['name']?>
                            <?php endforeach; ?>
                        </select>
                        </td>
                    </tr>
                    <tr>
                        <th>説明文</th>
                        <td><textarea name="body"><?=isset($item['body']) ? h($item['body']) : ''?></textarea></td>
                    </tr>
                </table>
                <p><input type="submit" name="edit" value="確認画面へ"></p>
            </form>
        </section>
        <?php if (($_GET['type'] == 'update')) :?>
            <section class="img-edit">
                <table>
                    <tr>
                        <th colspan="5">商品画像</th>
                    </tr>
                    <tr>
                        <td>画像1</td>
                        <td>画像2</td>
                        <td>画像3</td>
                        <td>画像4</td>
                        <td>画像5</td>
                    </tr>
                    <tr>
                        <td><?= $item['img1'] ? '<img src="' . ADMIN_PRODUCT_IMAGE_PATH . $item['img1'] . '" width="100">' : ''; ?></td>
                        <td><?= $item['img2'] ? '<img src="' . ADMIN_PRODUCT_IMAGE_PATH . $item['img2'] . '" width="100">' : ''; ?></td>
                        <td><?= $item['img3'] ? '<img src="' . ADMIN_PRODUCT_IMAGE_PATH . $item['img3'] . '" width="100">' : ''; ?></td>
                        <td><?= $item['img4'] ? '<img src="' . ADMIN_PRODUCT_IMAGE_PATH . $item['img4'] . '" width="100">' : ''; ?></td>
                        <td><?= $item['img5'] ? '<img src="' . ADMIN_PRODUCT_IMAGE_PATH . $item['img5'] . '" width="100">' : ''; ?></td>
                    </tr>
                    <tr>
                        <td>
                            <form action="" method="post">
                                <button type="submit" name="delete_image" value="img1" onclick="return deleteProductImage('1')" <?= isset($item['img1']) ? '' : 'disabled';?>>削除</button>
                            </form>
                        </td>
                        <td>
                            <form action="" method="post">
                                <button type="submit" name="delete_image" value="img2" onclick="return deleteProductImage('2')" <?= isset($item['img2']) ? '' : 'disabled';?>>削除</button>
                            </form>
                        </td>
                        <td>
                            <form action="" method="post">
                                <button type="submit" name="delete_image" value="img3" onclick="return deleteProductImage('3')" <?= isset($item['img3']) ? '' : 'disabled';?>>削除</button>
                            </form>
                        </td>
                        <td>
                            <form action="" method="post">
                                <button type="submit" name="delete_image" value="img4" onclick="return deleteProductImage('4')" <?= isset($item['img4']) ? '' : 'disabled';?>>削除</button>
                            </form>
                        </td>
                        <td>
                            <form action="" method="post">
                                <button type="submit" name="delete_image" value="img5" onclick="return deleteProductImage('5')" <?= isset($item['img5']) ? '' : 'disabled';?>>削除</button>
                            </form>
                        </td>
                    </tr>

                </table>
            </section>
        <?php endif; ?>
    </div>
</main>
<script>
    'use strict'
    function deleteProductImage(number) {
        let result = window.confirm('画像' + number + 'を削除してもよろしいですか?')
        if (result) {
            return true
        } else {
            return false
        }
    }
</script>
<!-- フッター -->
<?php require_once('./template/footer.php')?>
