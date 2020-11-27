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
<header class="admgl-header">
    <div class="container">
        <section class="admgl-top">
            <h2>ログインネーム : <?=h($_SESSION['name'])?></h2>
            <p> <a href="logout.php">ログアウトする</a></p>
        </section>
        <nav class="admgl-nav">
            <ul>
                <li><a href="top">top</a></li>
                <li><a href="product_list">商品管理</a></li>
                <li><a href="image_list">画像管理</a></li>
                <li><a href="sales_management_list">売上管理</a></li>
                <li><a href="category_list">カテゴリー管理</a></li>
                <li><a href="user_list">ユーザー管理</a></li>
                <li><a href="contact_list">問い合わせ管理</a></li>
            </ul>
        </nav>
    </div>
</header>