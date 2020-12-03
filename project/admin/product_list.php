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

//条件の初期値
$column = isset($_GET['column']) ? $_GET['column'] : NULL;
$order = isset($_GET['order']) ? $_GET['order'] : NULL;
$sort_key = isset($_GET['sort_key']) ? $_GET['sort_key'] : '';
$name = isset($_GET['name']) ? $_GET['name'] : '';
$price1 = isset($_GET['price1']) ? $_GET['price1'] : '';
$price2 = isset($_GET['price2']) ? $_GET['price2'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$sub_category = isset($_GET['sub_category']) ? $_GET['sub_category'] : '';
$page = isset($_GET['page']) ? ($_GET['page']  - 1) * 10 : '';
//商品全件の取得(ソート含む)
if (($product_list = $product->getProductList($column, $order)) === false) {
    header('Location: error.php?message=fail&word=getProductList');
    exit;
}
$count = $product->getCountProducts();
$sort_word = '';

//カテゴリー取得
if (($categories = $product->getCategoryList()) === false) {
    header('Location: error.php?message=fail&word=getPayment');
    exit;
}

//商品検索

if (($product_list = $product->searchProduct($name, $price1, $price2, $category, $sub_category, $sort_key, $page)) === false) {
    header('Location: error.php?message=fail&word=searchProduct');
    exit;
}

//ページネーション
$count = $product->getCountProducts($name, $price1, $price2, $category);
$hit = 10;
$max_page = ceil($count['count'] / 10);
if (!isset($_GET['page'])) { // $_GET['page_id'] はURLに渡された現在のページ数
    $now_page = 1; // 設定されてない場合は1ページ目にする
} else {
    $now_page = $_GET['page'];
}
$start_no = ($now_page - 1) * $hit;
$prev_num = $now_page - 1;
$next_num = $now_page + 1;
$sort_word =
    '&name=' . $name
    . '&price=' . $price1
    . '&price2=' . $price2
    . '&category=' . $category
    . '&sub_category=' . $sub_category
    . '&sort_key=' . $sort_key
;

$first = $count['count'] == 0 ? 0: ($now_page * 10) - 9;
$end = $count['count'] > $first + 9 ? $first + 9 : $count['count'];

?>

<!-- ヘッダー -->
<?php require_once('./template/header.php') ?>
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
                            <td class="name"><input type="text" name="name" value="<?= isset($_GET['name']) ? h($_GET['name']) : '' ?>"></td>
                        </tr>
                        <tr>
                            <th>価格</th>
                            <td class="price">
                                <input type="text" name="price" value="<?= isset($_GET['price']) ? h($_GET['price']) : '' ?>">円 ~
                                <input type="text" name="price2" value="<?= isset($_GET['price2']) ? h($_GET['price2']) : '' ?>">円
                            </td>
                        </tr>
                        <tr>
                            <th>カテゴリー</th>
                            <td>
                                <select name="category">
                                    <option value="" ?>未選択
                                        <?php foreach ($categories as $val) : ?>
                                    <option value="<?= $val['id'] ?>" <?= isset($_GET['category']) && $_GET['category']  == $val['id'] ? ' selected' : '' ?>><?= $val['name'] ?>
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
        <form action="" method="get" id="form_selected_sortkey_admin">
            <span><?=$first . '~' . $end?>件を表示しています。(<?= $count['count'] ?>件)</span>
            <input type="hidden" name="name" value="<?= isset($name) ? $name : ''; ?>">
            <input type="hidden" name="price1" value="<?= isset($price1) ? $price1 : ''; ?>">
            <input type="hidden" name="price2" value="<?= isset($price2) ? $price2 : ''; ?>">
            <input type="hidden" name="category" value="<?= isset($category) ? $category : ''; ?>">
            <input type="hidden" name="sub_category" value="<?= isset($sub_category) ? $sub_category : ''; ?>">
            <select name="sort_key" id="selected_sortkey_admin">
                <option value="id_DESC" <?= isset($_GET['sort_key']) && $_GET['sort_key'] == 'id_DESC' ? ' selected' : '' ?>>IDの新しい順</option>
                <option value="id_ASC" <?= isset($_GET['sort_key']) && $_GET['sort_key'] == 'id_ASC' ? ' selected' : '' ?>>IDの古い順</option>
                <option value="price_DESC" <?= isset($_GET['sort_key']) && $_GET['sort_key'] == 'price_DESC' ? ' selected' : '' ?>>値段が高い順</option>
                <option value="price_ASC" <?= isset($_GET['sort_key']) && $_GET['sort_key'] == 'price_ASC' ? ' selected' : '' ?>>値段が安い順</option>
            </select>
        </form>
        <section class="product-table">
            <table>
                <tr>
                    <td>ID</td>
                    <td>商品名</td>
                    <td>価格</td>
                    <td>画像</td>
                    <td>登録日時</td>
                    <td>更新日時</td>
                    <td><a href="product_edit?type=create"><button type="button">新規登録</button></a></td>
                </tr>
                <?php if (empty($product_list)) : ?>
                    <tr>
                        <td colspan="7">データが見つかりませんでした</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($product_list as $item) : ?>
                        <tr>
                            <td class="nowrap"><?= h($item['id']) ?></td>
                            <td class="nowrap"><?= h($item['name']) ?></td>
                            <td class="nowrap"><?= h(number_format($item['price'])) ?></td>
                            <td><?= $item['img1'] ? '<img src="' . ADMIN_PRODUCT_IMAGE_PATH . $item['img1'] . '" width="150">' : ''; ?></td>
                            <td><?= h($item['created_at']) ?></td>
                            <td><?= isset($item['updated_at']) ? h($item['updated_at']) : '' ?></td>
                            <td>
                                <a href="product_edit?type=update&id=<?= h($item['id']) ?>"><button type="button" id="btn">編集</button></a>
                                <form action="" method="post">
                                    <button type="submit" name="delete" value="<?= $item['id'] ?>" onclick="return deleteProductCart('<?= h($item['id']) ?>', '<?= h($item['name']) ?>')">削除</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </table>
            <?php if ($now_page == 1) : ?>
                前のページへ |
            <?php else : ?>
                <a href="?<?= $sort_word ?>&page=<?= $prev_num ?>">前のページへ</a> |
            <?php endif; ?>

            <?php for ($i = 1; $i <= $max_page; $i++) : ?>
                <?php if ($now_page == $i) : ?>
                    <?= $i ?> |
                <?php else : ?>
                    <a href="?<?= $sort_word ?>&page=<?= $i ?>"><?= $i ?></a> |
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($now_page == $max_page || $count['count'] == 0) : ?>
                次のページへ
            <?php else : ?>
                <a href="?<?= $sort_word ?>&page=<?= $next_num ?>">次のページへ</a>
            <?php endif; ?>
            </p>
        </section>
    </div>
</main>
<script>
    'use strict'

    function deleteProductCart(id, name) {
        let result = window.confirm('ID:' + id + '\n商品名:' + name + ' \n上記の商品を削除してもよろしいですか?')
        if (result) {
            return true
        } else {
            return false
        }
    }


    document.getElementById('selected_sortkey_admin').addEventListener("change", function(e) {
        document.forms.form_selected_sortkey_admin.submit();
    });
</script>
<!-- フッター -->
<?php require_once('./template/footer.php') ?>