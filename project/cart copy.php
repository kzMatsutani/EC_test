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
if (!empty($cart_in_product)) {
    $json = json_encode($cart_in_product);
}
echo $json;
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
                                    <input id="item_num<?=h($product['id'])?>" onchange="test('<?=h($product['id'])?>')" type="text" name="num" value="<?=h($product['num'])?>">
                                    <input type="hidden" name="cart_id" value="<?=h($product['cart_id'])?>">
                                    <input type="submit" name="change_num_product_in_cart" value="変更">
                                </form>
                            </td>
                            <?php $price = $product['price'] * $product['num']; ?>
                            <?php $num_total += $product['num']; ?>
                            <?php $sub_total += $price; ?>
                            <td><?=number_format($product['price'])?>円</td>
                            <input type="hidden" id="item_price<?=h($product['id'])?>" value="<?=$product['price']?>" readonly>
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

    let num = new Array();
    let price = new Array();
    let item_sub_price = new Array();
    let product_id = new Array();
    let json = [{"id":"4","0":"4","name":"\u90f5\u4fbf\u632f\u66ff\u30aa\u30f3\u30ea\u30fc","1":"\u90f5\u4fbf\u632f\u66ff\u30aa\u30f3\u30ea\u30fc","sub_name":"DIET","2":"DIET","day":"30","3":"30","price":"30000","4":"30000","img":"20201005103854_item_1.jpg","5":"20201005103854_item_1.jpg","description":"\u307e\u3058\u3067\u3059\u3054\u3044","6":"\u307e\u3058\u3067\u3059\u3054\u3044","created_at":"2020-08-27 16:29:20.734291","7":"2020-08-27 16:29:20.734291","updated_at":"2020-09-24 11:43:34.240384","8":"2020-09-24 11:43:34.240384","delete_flg":"0","9":"0","cart_id":"249","10":"249","product_id":"4","11":"4","num":"11","12":"11"},{"id":"42","0":"42","name":"\u30af\u30ec\u30b8\u30c3\u30c8\uff06\u4ee3\u91d1\u5f15\u63db\uff06\u9280\u884c","1":"\u30af\u30ec\u30b8\u30c3\u30c8\uff06\u4ee3\u91d1\u5f15\u63db\uff06\u9280\u884c","sub_name":"credit&cash","2":"credit&cash","day":"20","3":"20","price":"2000","4":"2000","img":"20201012130135_item_1.jpg","5":"20201012130135_item_1.jpg","description":"","6":"","created_at":"2020-10-01 16:18:51.119407","7":"2020-10-01 16:18:51.119407","updated_at":"2020-10-16 12:44:51.901222","8":"2020-10-16 12:44:51.901222","delete_flg":"0","9":"0","cart_id":"250","10":"250","product_id":"42","11":"42","num":"1","12":"1"}]

    for (let i = 0; i < json.length; i++) {
        console.log(json[i][0]);
        // product_id['id'] = json[i][0];
        num[json[i][0]]= json[i][12];
        price[json[i][0]]= json[i][4];
    }
    console.log(price);
    //商品の個数 * 値段の合計値
    function test(id)
    {
        id = Number(id);
        num[id] = document.getElementById('item_num' + id).value;
        price[id] = document.getElementById('item_price' + id).value;
        item_sub_price[id] = document.getElementById('item_sub_price' + id);
        item_sub_price[id].innerHTML = '<td>' + Number(price[id] * num[id]).toLocaleString() + '</td>';
    }

    function total_price()
    {

    }
</script>
<!-- フッター -->
<?php require_once('./template/footer.php'); ?>
