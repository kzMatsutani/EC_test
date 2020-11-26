<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0">
    <meta name="robots" content="noindex">
    <title>管理画面 </title>
    <!-- stylesheet -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.0/css/bootstrap-reboot.min.css">
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
</head>
<body>
<div class="nav-container">
    <section class="g-nav-order clearfix">
        <nav class="global-nav">
            <ul>
                <?php if (!empty($_SESSION['user_id'])) : ?>
                    <li class="nav-user">ようこそ <?=h($_SESSION['user_name'])?>さん</li>
                <?php endif; ?>
                <?php if (!strstr($_SERVER['REQUEST_URI'], 'matsutani/index') && !strstr(basename($_SERVER['REQUEST_URI']), 'tougan-matsutani')) : ?>
                    <li class="nav-item"><a href="index">トップ</a></li>
                <?php endif; ?>
                <?php if (empty($_SESSION['user_id']) && !strstr($_SERVER['REQUEST_URI'], 'matsutani/login')) : ?>
                    <li class="nav-item"><a href="login">ログイン</a></li>
                <?php endif; ?>
                <?php if (!strstr($_SERVER['REQUEST_URI'], 'matsutani/cart') && !strstr($_SERVER['REQUEST_URI'], 'matsutani/login')) : ?>
                    <li class="nav-item"><a href="cart">カートを見る</a></li>
                <?php endif; ?>
                <li class="nav-item"><a href="">ご利用案内</a></li>
                <li class="nav-item"><a href="">お問い合わせ</a></li>
                <?php if (!empty($_SESSION['user_id'])) : ?>
                    <li class="nav-item"><a href="logout">ログアウト</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </section>
</div>