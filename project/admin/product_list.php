<?php
require_once('system/library.php');
//ログイン画面からログインされていなかった場合はログイン画面へ戻す
confirmAuthAdmin();
$product = new Product();

//商品削除ボタンが押された場合
if (isset($_POST['delete'])) {
    if ($product->deleteProduct($_POST['delete']) == false) {
        header('Location: error.php?message=fail&word=deleteProduct');
        exit;
    }
}

//ソート条件の設定
$column = isset($_GET['column']) ? $_GET['column'] : NULL;
$order = isset($_GET['order']) ? $_GET['order'] : NULL;
//商品全件の取得(ソート含む)
if (($product_list = $product->getProductList($column, $order)) === false) {
    header('Location: error.php?message=fail&word=getProductList');
    exit;
}

//カテゴリー取得
if (($categories = $product->getCategoryList()) === false) {
    header('Location: error.php?message=fail&word=getPayment');
    exit;
}

//サブカテゴリ取得

//商品検索
if (isset($_GET['name'])) {
    $selected_payment = isset($_GET['payment']) ? $_GET['payment'] : [0 => 0];
    if (($product_list = $product->searchProduct($_GET['name'], $_GET['price'], $_GET['price2'], $selected_payment)) === false) {
        header('Location: error.php?message=fail&word=searchProduct');
        exit;
    }
}

?>

<!-- ヘッダー -->
<?php require_once('./template/header.php')?>
<main class="adm-product-list">
    <div class="container">
        <div class="list-top">
            <section class="btn">
                <button type="submit" disabled><?php getPage() ?></button>
            </section>
            <section class="product-search">
                <form action="" method="get">
                    <table>
                        <tr>
                            <th>商品名</th>
                            <td class="name"><input type="text" name="name" value="<?=isset($_GET['name']) ? h($_GET['name']) : ''?>"></td>
                        </tr>
                        <tr>
                            <th>価格</th>
                            <td class="price">
                                <input type="text" name="price" value="<?=isset($_GET['price']) ? h($_GET['price']) : ''?>">円 ~
                                <input type="text" name="price2" value="<?=isset($_GET['price2']) ? h($_GET['price2']) : ''?>">円
                            </td>
                        </tr>
                        <tr>
                            <th>カテゴリー</th>
                            <td>
                                <select name="category">
                                    <option value="" ?>未選択
                                    <?php foreach ($categories as $val) : ?>
                                        <option value="<?=$val['id']?>"<?=isset($_GET['payment'][$val['id']]) ? ' selected': ''?>><?=$val['name']?>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>サブカテゴリ</th>
                            <td>
                                <select name="category">
                                    <option value="" ?>未選択
                                    <?php foreach ($categories as $val) : ?>
                                        <option value="<?=$val['id']?>"<?=isset($_GET['payment'][$val['id']]) ? ' selected': ''?>><?=$val['name']?>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="submit" value="検索"></td>
                        </tr>
                    </table>
                </form>
            </section>
        </div>
        <section class="product-table">
            <table>
                <tr>
                    <td><a href="product_list.php?column=id&order=ASC">▲</a><br>ID<br><a href="product_list.php?column=id&order=DESC">▼</a></td>
                    <td><a href="product_list.php?column=name&order=ASC">▲</a><br>商品名<br><a href="product_list.php?column=name&order=DESC">▼</a></td>
                    <td><a href="product_list.php?column=price&order=ASC">▲</a><br>価格<br><a href="product_list.php?column=price&order=DESC">▼</a></td>
                    <td>画像</td>
                    <td><a href="product_list.php?column=created_at&order=ASC">▲</a><br>登録日時<br><a href="product_list.php?column=created_at&order=DESC">▼</a></td>
                    <td><a href="product_list.php?column=updated_at&order=ASC">▲</a><br>更新日時<br><a href="product_list.php?column=updated_at&order=DESC">▼</a></td>
                    <td><a href="product_edit.php?type=create"><button type="button">新規登録</button></a></td>
                </tr>
                <?php if (empty($product_list)) : ?>
                    <tr>
                        <td colspan="7">データが見つかりませんでした</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($product_list as $item) : ?>
                        <tr>
                            <td class="nowrap"><?=h($item['id'])?></td>
                            <td class="nowrap"><?=h($item['name'])?></td>
                            <td class="nowrap"><?=h(number_format($item['price']))?></td>
                            <td><?=$item['img1'] ? '<img src="' . ADMIN_PRODUCT_IMAGE_PATH . $item['img1'] . '" width="150">' : ''; ?></td>
                            <td><?=h($item['created_at'])?></td>
                            <td><?=isset($item['updated_at']) ? h($item['updated_at']) : '' ?></td>
                            <td>
                                <a href="product_edit.php?type=update&id=<?=h($item['id'])?>"><button type="button" id="btn">編集</button></a>
                                <form action="" method="post">
                                    <button type="submit" name="delete" value="<?=$item['id']?>" onclick="return deleteProductCart('<?=h($item['id'])?>', '<?=h($item['name'])?>')">削除</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </table>
        </section>
    </div>
</main>
<script>
    'use strict'
    function deleteProductCart(id, name)
    {
        let result = window.confirm('ID:' + id + '\n商品名:' + name + ' \n上記の商品を削除してもよろしいですか?')
        if (result) {
            return true
        } else {
            return false
        }
    }
</script>
<!-- フッター -->
<?php require_once('./template/footer.php')?>
