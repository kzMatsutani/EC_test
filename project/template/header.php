<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0">
    <meta name="keywords" content="ダイエット,冬瓜,とうがん,冬瓜ダイエット,桜物産">
    <meta name="description" content="あなたはいつまで食事制限のダイエットを続けるつもりですか？食べたいものをガマンするってつらくないですか？そんなあなたに朗報です！！”冬瓜ダイエット”という手もあります。">
    <meta name="robots" content="noindex">
    <title>冬瓜ダイエット 桜物産 <?php getPage()?></title>
    <!-- stylesheet -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css">
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/photoswipe.css">
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="jquery.photoswipe.js"></script>
    <script type="text/javascript"></script>
    <script src="breakpoints.js"></script>
    <script src="jquery.marquee.js"></script>
    <script src="jquery.indexScript.js"></script>
</head>
<body>
<header id="header">
    <div class="title-container">
        <div class="title-box">
            <div class="title">
                <div class="header-logo">
                    <img src="img/sakura_logo.gif" alt="ロゴ">
                </div>
                <div class="site-title">
                    <p>冬瓜ダイエット 通販専門<br>
                        とうがんダイエット　冬瓜ダイエット専門店［桜物産］mari</p>
                    <h1>冬瓜ダイエット専門店　桜物産</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="payment-contact-container">
        <div class="payment-contact">
            <div class="payment">
                <p><img src="img/payment.jpg" alt="支払い"></p>
            </div>
            <div class="freedial">
                <p>冬瓜ダイエットのご購入・お問合せ</p>
                <div class="phone-number">
                    <p><img src="img/freedial.gif" alt="支払い"><span class="font1">0120-20-3249</span><br>（受付時間:9:00〜18:00）</p>
                </div>
            </div>
        </div>
    </div>
</header>
<div class="nav-container">
    <section class="g-nav-order clearfix">
        <nav class="global-nav">
            <ul>
                <?php if (!empty($_SESSION['user_id'])) : ?>
                    <li class="nav-user">ようこそ <?=h($_SESSION['user_name'])?>さん</li>
                <?php endif; ?>
                <?php if (!strstr($_SERVER['REQUEST_URI'], 'matsutani/index') && !strstr(basename($_SERVER['REQUEST_URI']), 'tougan-matsutani')) : ?>
                    <li class="nav-item"><a href="index.php">トップ</a></li>
                <?php endif; ?>
                <?php if (empty($_SESSION['user_id']) && !strstr($_SERVER['REQUEST_URI'], 'matsutani/login')) : ?>
                    <li class="nav-item"><a href="login.php">ログイン</a></li>
                <?php endif; ?>
                <?php if (!strstr($_SERVER['REQUEST_URI'], 'matsutani/cart.php') && !strstr($_SERVER['REQUEST_URI'], 'matsutani/login.php')) : ?>
                    <li class="nav-item"><a href="cart.php">カートを見る</a></li>
                <?php endif; ?>
                <li class="nav-item"><a href="">ご利用案内</a></li>
                <li class="nav-item"><a href="">お問い合わせ</a></li>
                <?php if (!empty($_SESSION['user_id'])) : ?>
                    <li class="nav-item"><a href="logout.php">ログアウト</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </section>
</div>