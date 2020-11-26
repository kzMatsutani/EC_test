<?php
require_once('system/library.php');
//ログイン画面からログインされていなかった場合はログイン画面へ戻す
confirmAuthAdmin();
?>

<!-- ヘッダー -->
<?php require_once('./template/header.php')?>
<main class="adm-error">
    <div class="container">
        <section class="btn">
            <button type="submit" disabled><?php getPage() ?></button>
        </section>
        <section class="error">
            <p class="error">
                <?php getErrorMessage($_GET['message'] ?? NULL, $_GET['word'] ?? NULL)?>
            </p>
        </section>
    </div>
</main>
<!-- フッター -->
<?php require_once('./template/footer.php')?>
</body>
</html>