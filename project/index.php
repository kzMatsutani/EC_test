<?php
require_once('./admin/system/library.php');
$product = new Product();
$order = new Order();
//商品一覧の取得
if (($product_list = $product->getProductList()) === false) {
    header('Location:error.php?message=system');
    exit;
}

//商品をカートに入れる
if (!empty($_POST['cart'])) {
    //ログインしていなかった場合はカートに入れる商品データを保持しながらログイン画面へ
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php', true, 307);
        exit;
    }
    if (!$order->addProductInCart($_SESSION['user_id'], $_POST['product_id'], $_POST['num'])) {
        header('Location:error.php?message=system');
        exit;
    }
    header('Location: cart.php');
    exit;
}
?>

<!-- ヘッダー -->
<?php require_once('./template/header.php'); ?>
<!-- メイン -->

<!-- 購入 -->
<section class="order-container" id="order-container">
    <!-- 商品DBからの表示 -->
    <?php foreach ($product_list as $item) : ?>
        <div class="order-option">
            <div class="order-option-title">
                <h2><i class="fa fa-thumb-tack fa-2x" aria-hidden="true"></i><?=h($item['description'])?></h2>
            </div>
        </div>
        <div class="option">
            <div class="option-img">
                <img src="<?=MAIN_PRODUCT_IMAGE_PATH . (isset($item['img']) ? h($item['img']) : NO_IMAGE_FILE)?>" alt="商品">
            </div>
            <div class="order-text">
                <p><?=h($item['sub_name'])?></p>
                <h2><?=h($item['name'])?></h2>
                <h3>《<?=h($item['day'])?>日分》<span class="font15"><?=h(number_format($item['price']))?>円</span></h3>
                <h4>総額1万円以上は送料無料!</h4>
                <form method="post" action="" target="_top">
                    <input type="hidden" name="product_id" value="<?=h($item['id'])?>">
                    <select name="num">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                    <input type="submit" name="cart" value="カートへ">
                </form>
                <br />
                <div class="order-payment-detail">
                    <div class="order-payment">
                        <h4>お支払い方法</h4>
                        <?php $payment = $product->getSelectedPaymentOfProduct($item['id']); ?>
                        <p>
                            <?php foreach ($payment as $val) : ?>
                                <i class="fa <?=h($val['class'])?>" aria-hidden="true"></i><?=h($val['name'])?><br>
                            <?php endforeach; ?>
                        </p>
                    </div>
                    <div class="jumpto_detail">
                        <a class="jump" href="#detail-img">
                            <p>商品詳細はこちら<i class="fa fa-chevron-circle-right" aria-hidden="true"></i></p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<!-- 商品DBからの表示終わり -->
</section>
<!-- フッター -->
<?php require_once('./template/footer.php'); ?>
