<?php
require_once('./admin/system/library.php');
confirmAuthUser();
//トークンが生成されていなければエラー画面へ
authToken($_POST['token']);
$order = new Order();

//confから購入ボタンを押された時
if ($order->sellProduct($_POST, $_SESSION['user_id']) === false) {
    header('Location: error.php?message=buyProduct');
    exit;
}
unset($_SESSION['token']);
?>

<!-- ヘッダー -->
<?php require_once('./template/header.php'); ?>
<div class="cart-container">
    <div class="cart-done">
        <p class="order-thanks">ご注文が完了しました。ご利用ありがとうございました。</p>
        <p class="order-mail">
            <span>登録されたメールアドレスに確認メールをお送りしましたのでご確認ください。</span>
            <span>確認メールが届いていない場合にはメールアドレスが誤っているか、迷惑メールフォルダ等に振り分けられている可能性がありますので、</span>
            <span>再度ご確認をお願いいたします。</span>
        </p>
    </div>
</div>
<!-- フッター -->
<?php require_once('./template/footer.php'); ?>
