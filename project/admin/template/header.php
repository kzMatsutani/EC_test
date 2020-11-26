<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0">
    <meta name="robots" content="noindex">
    <title>冬瓜ダイエット 桜物産 管理画面 <?php getPage() ?></title>
    <!-- stylesheet -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.0/css/bootstrap-reboot.min.css">
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <!-- jQuery -->
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="jquery.photoswipe.js"></script>
    <script type="text/javascript"></script>
    <script src="breakpoints.js"></script>
    <script src="jquery.marquee.js"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
</head>
<body>
<header class="admgl-header">
    <div class="container">
        <section class="admgl-top">
            <h2><?=h($_SESSION['name'])?>さん、ご機嫌いかがですか？</h2>
            <p> <a href="logout.php">ログアウトする</a></p>
        </section>
        <nav class="admgl-nav">
            <ul>
                <li><a href="top.php" class="top">top</a></li>
                <li><a href="product_list.php" class="trainee-management">商品管理</a></li>
                <li><a href="sales_management_list.php" class="trainee-task">売上管理</a></li>
                <li><a href="category_list" class="trainee-skill">支払い方法管理</a></li>
                <li><a href="#" class="communication">コミュニケーション管理</a></li>
                <li><a href="#" class="admin-management">管理者管理</a></li>
            </ul>
        </nav>
    </div>
</header>