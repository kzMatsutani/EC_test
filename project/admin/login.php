<?php
require_once('system/library.php');

//ログイン認証を行っていてもログイン画面に入った時点でログイン認証、セッションの破棄。
if (isset($_SESSION['authenticated'])) {
    session_destroy();
}

//フォームから送信された場合にバリデーション
if (!empty($_POST['login'])) {
    //ポスト送信されたデータが入力されているかのバリデーション
    if ($_POST['id'] == '' || $_POST['pass'] == '') {
        $error = 'IDかパスワードが入力されていません';
    } else {
        $auth = new LoginAdmin();
        //loginメソッド内でDB内のid,passの照合を行い、OKならメソッド内からTOPに移動、NGならエラー文が返ってくる
        $error = $auth->login($_POST['id'], $_POST['pass']);
    }
}
?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0">
    <meta name="robots" content="noindex">
    <title>管理画面トップ</title>
    <!-- stylesheet -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.0/css/bootstrap-reboot.min.css">
    <link href="css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="login">
        <h1>管理画面ログイン</h1>
        <p class="error"><span><?=isset($error) ? $error : ''?></span></p>
        <form action="" method="post">
            <table>
                <tr>
                    <th>ログインID</th>
                    <td><input type="text" name="id" value="<?=isset($_POST['id']) ? h($_POST['id']) : ''?>"></td>
                </tr>
                <tr>
                    <th>パスワード</th>
                    <td><input type="password" name="pass"></td>
                </tr>
            </table>
            <p><input type="submit" name="login" value="認証"></p>
        </form>
    </div>
</body>
</html>