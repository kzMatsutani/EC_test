<?php
require_once('admin/system/library.php');
//ログインしている場合はインデックスへ
confirmAuthUserTop();

//フォームから送信された場合にバリデーション
if (!empty($_POST['login'])) {
    //ポスト送信されたデータが入力されているかのバリデーション
    if ($_POST['id'] == '' || $_POST['pass'] == '') {
        $error = '※IDかパスワードが入力されていません';
    } else {
        $user = new User();
        //loginメソッド内でDB内のid,passの照合を行いOKならtrue、NGならエラー文が返ってくる
        $error = $user->loginUser($_POST['id'], $_POST['pass']);
    }

    if (($error === true)) {
        //トップから商品をカートに入れる選択をした場合はカートに商品を入れてcart.phpに遷移
        if (!empty($_POST['product_id']) && !empty($_POST['num'])) {
            $order = new Order();
            if (!$order->addProductInCart($_SESSION['user_id'], $_POST['product_id'], $_POST['num'])) {
                header('Location: error.php?message=system');
                exit;
            }
            header('Location: cart.php');
            exit;
        }
        //会員専用ページから遷移してきた場合はその会員ページに遷移
        if (!empty($_GET['url'])) {
            header('Location:' . $_GET['url']);
            exit;
        }
        header('Location: index.php');
        exit;
    }

}
?>

<!-- ヘッダー -->
<?php require_once('./template/header.php'); ?>
<div class="login">
    <div class="login-container">
        <h2>会員ログイン</h2>
        <p class="error"><span><?=isset($error) ? $error : ''?></span></p>
        <form action="" method="post">
            <table>
                <tr>
                    <th>ログインID</th>
                    <td><input type="text" name="id" value="<?=isset($_POST['id']) ? $_POST['id'] : '' ?>"></td>
                </tr>
                <tr>
                    <th>パスワード</th>
                    <td><input type="password" name="pass"></td>
                </tr>
            </table>
            <input type="hidden" name="product_id" value="<?=isset($_POST['product_id']) ? $_POST['product_id'] : '' ?>">
            <input type="hidden" name="num" value="<?=isset($_POST['num']) ? $_POST['num'] : '' ?>">
            <p class="login-submit">
                <input type="submit" name="login" value="ログイン">
            </p>
        </form>
        <div class="login-option">
            <p><a href="">パスワードをお忘れですか?</a></p>
            <p><a href="">アカウントを作成する</a></p>
        </div>
    </div>
<!-- フッター -->
<?php require_once('./template/footer.php'); ?>