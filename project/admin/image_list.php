<?php
require_once('system/library.php');
//ログイン画面からログインされていなかった場合はログイン画面へ戻す
confirmAuthAdmin();



?>

<!-- ヘッダー -->
<?php require_once('./template/header.php')?>
<main class="adm-sales-list">
    <div class="container">
        <div class="list-top clearfix">
            <section class="btn">
                <button type="submit" disabled><?php getPage() ?></button>
            </section>
        </div>
        
    </div>
</main>
<!-- フッター -->
<?php require_once('./template/footer.php')?>