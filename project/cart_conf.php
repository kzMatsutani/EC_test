<?php
require_once('./admin/system/library.php');
confirmAuthUser();
//トークンが生成されていなければエラー画面へ
authToken($_POST['token']);
$order = new Order();
$user = new User();
//ユーザー情報とカート情報の取得
if (!$user_info = $user->getUserInfo($_SESSION['user_id'])) {
    header('Location: error.php?message=system');
    exit;
}
if (!$cart_in_product = $order->getProductInCart($_SESSION['user_id'])) {
    header('Location: error.php?message=system');
    exit;
}
if (!$payment = $order->getPaymentInfo($cart_in_product)) {
    header('Location: error.php?message=system');
    exit;
}
//小計と合計数の初期値
$num_total = 0;
$sub_total = 0;

//送付先を変更するにチェックしていた場合はPOST値、変更しないにチェックしていた場合は請求先を代入
$delivery_address = $_POST['shipping'] == 1 ? $_POST : $user_info + $_POST;

// バリデーションを行い、エラーがある場合はcart_editへ戻す 選択できない支払い方法のidが指定された場合はエラー画面へ遷移
$order->validateCartEdit($delivery_address, $payment);
if (!empty($_SESSION['error'])) {
    header('Location:cart_edit.php', true, 307);
    exit;
}
?>

<!-- ヘッダー -->
<?php require_once('./template/header.php'); ?>
<div class="cart-container">
    <div class="cart-edit">
        <form action="cart_done.php" method="post">
            <h2>確認</h2>
            <div class="cart-edit-item">
                <table>
                    <tr>
                        <th>商品画像</th>
                        <th>商品名</th>
                        <th>個数</th>
                        <th>単価</th>
                        <th>販売価格</th>
                    </tr>
                    <?php foreach ($cart_in_product as $product) : ?>
                        <tr>
                            <td>
                                <img src="<?=MAIN_PRODUCT_IMAGE_PATH . (isset($product['img']) ? h($product['img']) : NO_IMAGE_FILE)?>">
                            </td>
                            <td><?=h($product['name'])?></td>
                            <td>
                                <?=h($product['num'])?>
                                <?php $num_total += $product['num']; ?>
                            </td>
                            <td><?=h(number_format($product['price']))?>円</td>
                            <?php $price = $product['price'] * $product['num']; ?>
                            <?php $sub_total += $price; ?>
                            <td><?=number_format($price)?>円</td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="2" class="cart-left">小計</td>
                        <td><?=$num_total?></td>
                        <td></td>
                        <td><?=number_format($sub_total)?>円</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="cart-left">送料</td>
                        <?php $shipping_price = $sub_total < 10000 ? 1000 : 0; ?>
                        <td><?=number_format($shipping_price)?>円</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="cart-left">総額</td>
                        <td><?=number_format($sub_total + $shipping_price)?>円</td>
                    </tr>
                </table>
                <input type="hidden" name="sub_total" value="<?=$sub_total?>">
                <input type="hidden" name="shipping_price" value="<?=$shipping_price?>">
                <input type="hidden" name="tax" value="0">
                <input type="hidden" name="total_price" value="<?=$sub_total + $shipping_price?>">
            </div>
            <h2>送付先情報</h2>
            <div class="cart-edit-shipping">
                <div class="shipping-change">
                    <table>
                        <tr>
                            <th>郵便番号</th>
                            <td><?=h($delivery_address['postal_code1'])?>-<?=h($delivery_address['postal_code2'])?></td>
                        </tr>
                        <tr>
                            <th>住所</th>
                            <td>
                                <?=getPref($delivery_address['pref'])?>
                                <?=h($delivery_address['city'])?>
                                <?=h($delivery_address['address'])?>
                                <?=h($delivery_address['other'])?>
                            </td>
                        </tr>
                        <tr>
                            <th>電話番号</th>
                            <td><?=h($delivery_address['tel1'])?>-<?=h($delivery_address['tel2'])?>-<?=h($delivery_address['tel3'])?></td>
                        </tr>
                        <tr>
                            <th>お名前</th>
                            <td>
                                <ul>
                                    <li><?=h($delivery_address['name_kana'])?></li>
                                    <li><?=h($delivery_address['name'])?></li>
                                </ul>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <input type="hidden" name="shipping" value="<?=h($_POST['shipping'])?>">
            <input type="hidden" name="name" value="<?=h($delivery_address['name'])?>">
            <input type="hidden" name="name_kana" value="<?=h($delivery_address['name_kana'])?>">
            <input type="hidden" name="tel1" value="<?=h($delivery_address['tel1'])?>">
            <input type="hidden" name="tel2" value="<?=h($delivery_address['tel2'])?>">
            <input type="hidden" name="tel3" value="<?=h($delivery_address['tel3'])?>">
            <input type="hidden" name="postal_code1" value="<?=h($delivery_address['postal_code1'])?>">
            <input type="hidden" name="postal_code2" value="<?=h($delivery_address['postal_code2'])?>">
            <input type="hidden" name="pref" value="<?=h($delivery_address['pref'])?>">
            <input type="hidden" name="city" value="<?=h($delivery_address['city'])?>">
            <input type="hidden" name="address" value="<?=h($delivery_address['address'])?>">
            <input type="hidden" name="other" value="<?=h($delivery_address['other'])?>">
            <input type="hidden" name="payment_id" value="<?=h($_POST['payment_id'])?>">
            <input type="hidden" name="token" value="<?=h($_POST['token'])?>">
            <h2>請求先情報</h2>
            <div class="cart-edit-bill">
                <table>
                    <tr>
                        <th>郵便番号</th>
                        <td><?=h($user_info['postal_code1'])?>-<?=h($user_info['postal_code2'])?></td>
                    </tr>
                    <tr>
                        <th>住所</th>
                        <td><?=getPref($user_info['pref'])?> <?=h($user_info['city'])?> <?=h($user_info['address'])?> <?=h($user_info['other'])?></td>
                    </tr>
                    <tr>
                        <th>電話番号</th>
                        <td><?=h($user_info['tel1'])?>-<?=h($user_info['tel2'])?>-<?=h($user_info['tel3'])?></td>
                    </tr>
                    <tr>
                        <th>メールアドレス</th>
                        <td><?=h($user_info['mail'])?></td>
                    </tr>
                    <tr>
                        <th>お名前</th>
                        <td>
                            <ul>
                                <li><?=h($user_info['name_kana'])?></li>
                                <li><?=h($user_info['name'])?></li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </div>
            <h2>支払い方法</h2>
            <div class="cart-edit-payment">
                <table>
                    <tr>
                        <th>支払い方法</th>
                        <td><?=$payment[$_POST['payment_id']]['name']?></td>
                    </tr>
                </table>
            </div>
            <div class="cart-conf-submit">
                <input class="sell" type="submit" name="sell" value="購入する">
                <input type="submit" formaction="cart_edit.php" name="reedit" value="修正する">
            </div>
        </form>
    </div>
</div>
<!-- フッター -->
<?php require_once('./template/footer.php'); ?>
