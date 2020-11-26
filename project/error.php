<?php
require_once('./admin/system/library.php');
?>

<!-- ヘッダー -->
<?php require_once('./template/header.php'); ?>
    <div class="error-container">
        <p class="error-message">
            <?php getErrorMessage($_GET['message'] ?? NULL, $_GET['word'] ?? NULL) ?>
            <span>再度お試しいただいてもエラーが発生する場合はお電話、またはメールにてご相談ください。</span>
        </p>
    </div>
<!-- フッター -->
<?php require_once('./template/footer.php'); ?>