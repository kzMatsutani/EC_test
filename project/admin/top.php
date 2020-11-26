<?php
require_once('system/library.php');
//ログイン画面からログインされていなかった場合はログイン画面へ戻す
confirmAuthAdmin();
?>

<!-- ヘッダー -->
<?php require_once('./template/header.php')?>
<main class="adm-top-main">
    <div class="container">
    <section class="btn">
        <button type="submit" disabled><?php getPage() ?></button>
    </section>
    <section class="adm-top-alert">
        <p>アラートはありません</p>
    </section>
        <section class="adm-top-news">
            <h2>新着情報</h2>
            <table>
                <tr>
                    <th>2020-8-18</th>
                    <td>テストで文字をいれました。課題1の途中です</td>
                </tr>
                <tr>
                    <th>2020-7-01</th>
                    <td>Lorem ipsum dolor sit amet consectetur adipisicing elit. Odio, architecto!</td>
                </tr>
            </table>
        </section>
        <section class="adm-top-task">
        </section>
    </div>
</main>
<!-- フッター -->
<?php require_once('./template/footer.php')?>
