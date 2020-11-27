<?php
/*
テストのローカルホスト用
*/
define('HOST', 'localhost');
define('DBNAME', 'EC_test');
define('DBUSER', 'root');
define('DBPASS', '');
//イメージの絶対パス
define('ABSOLUTE_PRODUCT_IMAGE_PATH', '/Applications/XAMPP/htdocs/EC/project/img/product/');


/*
共通の画像とパス
*/
// //admin側の画像パス
define('ADMIN_PRODUCT_IMAGE_PATH', '../img/product/');
// //main側の画像パス
define('MAIN_PRODUCT_IMAGE_PATH', './img/product/');
//商品画像がなかった場合のNO IMAGE画像
define('NO_IMAGE_FILE', 'no_image.jpg');

/*
エラー用
 */
const ERROR_MESSAGE = [
    'fail' => '%sに失敗しました 時間をおいてもう一度試すか、管理者に連絡してください',
    'parameter' => '不適切なパラメーターの変更が確認されました。',
    'system' => 'システムエラーが発生しました。',
    'transition' => '不適切な画面の移動が確認されました。',
    'buyProduct' => '商品の購入手続きに失敗しました。　大変申し訳ございませんが、もう一度お試しください。',
    'other' => 'エラーが発生しました。'
];

const ERROR_WORD = [
    'deleteProduct' => '商品削除',
    'updateProduct' => '商品編集',
    'createProduct' => '商品登録',
    'searchProduct' => '商品検索',
    'getProductList' => '商品リストの取得',
    'getProduct' => '商品データの取得',
    'getPayment' => '支払い方法の取得',
    'salesManagement' => '売上データの取得',
    'other' => '処理'
];

/*
メール送信元
 */
//購入していただいたお客様に自動送信するメールアドレス
define('SALE_MAIL_ADDRESS', 'admin@example.com');