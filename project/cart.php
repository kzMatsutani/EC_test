<?php
require_once('./admin/system/library.php');
confirmAuthUser();
$order = new Order();

//カートから商品数の変更
if (!empty($_POST['change_num_product_in_cart'])) {
    if (!preg_match('/^\A[0-9]+$/u', $_POST['num'])) {
        $error = '※不適切な値が入力されました。 有効な値を入力してください。';
    } elseif ($_POST['num'] > 100) {
        $error = '※一つの商品の個数を101以上にすることができません。';
    } else {
        if (!$order->changeNumProductInCart($_POST['num'], $_POST['cart_id'])) {
            header('Location: error.php?message=system');
            exit;
        }
    }
}

//カートを空にするボタンが押されたとき、すべての商品をカートから削除
if (!empty($_POST['deleteAllProudct'])) {
    if (!$order->deleteAllProductInCart($_SESSION['user_id'])) {
        header('Location: error.php?message=system');
        exit;
    }
}

//商品個別の削除ボタンが押されたとき
if (!empty($_POST['deleteProduct'])) {
    if (!$order->deleteProductInCart($_POST['deleteProduct'])) {
        header('Location: error.php?message=system');
        exit;
    }
}

//ログインユーザのカート内商品の取得
if (($cart_in_product = $order->getProductInCart($_SESSION['user_id'])) === false) {
    header('Location: error.php?message=system');
    exit;
}

//カート内商品の支払い方法を絞り込みして取得
if (!empty($cart_in_product) && ($payment = $order->getPaymentInfo($cart_in_product)) === false) {
    header('Location: error.php?message=system');
    exit;
}

//小計と合計数の初期値
$num_total = 0;
$sub_total = 0;



?>

<!-- ヘッダー -->
<?php require_once('./template/header.php'); ?>
<div class="cart-container clearfix">
    <main class="cart-main">
        <h2>カート <span class="error"><?=!empty($error) ? $error : ''?></span></h2>
        <div class="cart-item">
            <?php if (empty($cart_in_product)) : ?>
                <!-- カートに商品が入っていない場合 -->
                <p class="not-product-cart">カートに商品が入っておりません</p>
            <?php else : ?>
                <!-- カートに商品が入っていた場合 -->
                <table>
                    <tr>
                        <th>削除</th>
                        <th>商品画像</th>
                        <th>商品名</th>
                        <th>個数</th>
                        <th>単価</th>
                        <th>販売価格</th>
                    </tr>
                    <?php foreach ($cart_in_product as $product) : ?>
                        <tr>
                            <td>
                                <form class="cart-delete" action="" method="post">
                                    <button type="submit" name="deleteProduct" value="<?=$product['cart_id']?>" onclick="return deleteProductInCart('<?=h($product['name'])?>')">削除</button>
                                </form>
                            </td>
                            <td>
                                <img src="<?=MAIN_PRODUCT_IMAGE_PATH . (isset($product['img']) ? h($product['img']) : NO_IMAGE_FILE)?>">
                            </td>
                            <td><?=h($product['name'])?></td>
                            <td>
                                <form class="item-num" action="" method="post">
                                    <input id="item_num<?=h($product['id'])?>" onchange="changePrice()" type="number" name="num" value="<?=h($product['num'])?>">
                                    <input type="hidden" name="cart_id" value="<?=h($product['cart_id'])?>" readoly>
                                    <input type="submit" name="change_num_product_in_cart" value="変更">
                                </form>
                            </td>
                            <?php $price = $product['price'] * $product['num']; ?>
                            <?php $num_total += $product['num']; ?>
                            <?php $sub_total += $price; ?>
                            <td><?=number_format($product['price'])?>円</td>
                            <td id="item_sub_price<?=h($product['id'])?>"><?=number_format($price)?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
        <div class="cart-option clearfix">
            <?php if (!empty($cart_in_product)) : ?>
                <form action="" method="post">
                    <button class="option-delete-all" type="submit" name="deleteAllProudct" value="1" onclick="return deleteAllProductInCart()">カートを空にする</button>
                </form>
            <?php endif; ?>
            <button class="option-more-select" onClick="location.href='index.php#order-container'">買い物を続ける</button>
        </div>
    </main>
    <aside class="cart-accounting clearfix">
        <div class="cart-register">
            <?php if ($num_total == 0) : ?>
                <li class="cart-empty-product">商品が入っておりません</li>
            <?php elseif (count($payment) == 0) : ?>
                <div class="cart-empty-payment-message">
                    <span class="empty-payment"><span class="new-line">※カート内の商品に</span>共通の支払い方法がございません</span>
                </div>
                <li class="cart-empty-payment">レジに進むことができません</li>
            <?php else : ?>
                <li><button type="submit" onClick="location.href='cart_edit.php'">レジに進む</button></li>
            <?php endif; ?>
        </div>
        <div class="cart-price">
            <h2>合計金額</h2>
            <ul class="cleafix">
                <li><span class="left">小計</span><span class="right" id="cart_sub_total"><?=number_format($sub_total)?>円</span></li>
                <li><span class="left">商品点数</span><span class="right" id="cart_num_total"><?=number_format($num_total)?>個</span></li>
                <?php $shipping_price = $sub_total < 10000 && $sub_total > 0 ? 1000 : 0; ?>
                <li><span class="left">送料</span><span class="right" id="shipping_price"><?=number_format($shipping_price)?>円</span></li>
                <li class="strong"><span class="left">総額</span><span class="right" id="cart_total_price"><?=number_format($sub_total + $shipping_price)?>円</span></li>
            </ul>
        </div>
    </aside>
</div>
<script>
    'use strict'
    //カート内商品の個別削除の確認
    function deleteProductInCart(name) {
        let result = window.confirm('商品名:' + name + ' \n\n上記の商品をカートから削除してもよろしいですか?');
        if (result) {
            return true;
        }
        return false;
    }
    //カート内商品の全削除の確認
    function deleteAllProductInCart() {
        let result = window.confirm('カートを空にしてもよろしいですか？')
        if (result) {
            return true;
        }
        return false;
    }
    //テーブルデータをJSONで取得
    let products = <?=json_encode($cart_in_product)?>;

    //個数が変更された場合、商品とカートの価格を変更
    function changePrice()
    {
        let sub_total_price = 0;
        let total_num = 0;
        let shipping_price = 0;
        //カート内商品の値の取得、小計の上書き
        for (let i = 0; i < products.length; i++) {
            let id = products[i].id;
            let num = document.getElementById('item_num' + id).value;
            let price = products[i].price;
            let item_sub_price = document.getElementById('item_sub_price' + id);
            total_num += Number(num);
            sub_total_price += Number(price * num);
            item_sub_price.textContent = Number(price * num).toLocaleString();
        }
        //小計が10000を下回る場合は1000
        if (sub_total_price < 10000) {
            shipping_price = 1000;
        }
        //カート小計の上書き
        document.getElementById('cart_sub_total').textContent = Number(sub_total_price).toLocaleString() + '円';
        document.getElementById('shipping_price').textContent = shipping_price + '円';
        document.getElementById('cart_num_total').textContent = Number(total_num).toLocaleString() + '個';
        document.getElementById('cart_total_price').textContent = Number(sub_total_price + shipping_price).toLocaleString() + '円';
    }
</script>
<!-- フッター -->
<?php require_once('./template/footer.php'); ?>
