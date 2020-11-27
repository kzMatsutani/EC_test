<?php
require_once('system/library.php');
//ログイン画面からログインされていなかった場合はログイン画面へ戻す
confirmAuthAdmin();

$category = new Product();
$category_list = $category->getCategoryList();

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
        <section class="category-table">
            <table>
                <tr>
                    <td>カテゴリーID</td>
                    <td>カテゴリー名</td>
                    <td>登録日時</td>
                    <td>更新日時</td>
                    <td><a href="product_edit.php?type=create"><button type="button">新規登録</button></a></td>
                </tr>
                <?php if (empty($category_list)) : ?>
                    <tr>
                        <td colspan="7">データが見つかりませんでした</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($category_list as $item) : ?>
                        <tr>
                            <td class="nowrap"><?=h($item['id'])?></td>
                            <td class="nowrap"><?=h($item['name'])?></td>
                            <td></td>
                            <td></td>
                            <td>
                                <a href="product_edit.php?type=update&id=<?=h($item['id'])?>"><button type="button" id="btn">編集</button></a>
                                <form action="" method="post">
                                    <button type="submit" name="delete" value="<?=$item['id']?>" onclick="return deleteProductCart('<?=h($item['id'])?>', '<?=h($item['name'])?>')">削除</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </table>
        </section>
    </div>
</main>
<!-- フッター -->
<?php require_once('./template/footer.php')?>