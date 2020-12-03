CREATE DATABASE EC_test CHARACTER SET utf8 COLLATE utf8_general_ci;

******************************************
管理ユーザーテーブル構成
id //自動連番のID
login_id //ログインフォームで入力確認するid 文字数は?
password //ログインフォームで入力確認するpass 文字数?
email //メールアドレス  必要?
name //ログイン者の名前　必要?

CREATE TABLE admins (
    id SERIAL PRIMARY KEY,
    login_id VARCHAR(30) NOT NULL,
    password text NOT NULL,
    email TEXT NOT NULL,
    name VARCHAR(30) NOT NULL,
    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6),
    updated_at TIMESTAMP(6) NULL DEFAULT NULL,
    delete_flg BOOLEAN DEFAULT FALSE
);

INSERT INTO admins (
    login_id,
    password,
    email,
    name
) VALUES (
    'admin',
    '$2y$10$JxJJ8hmi8925nSkQpCj2luYMzfP0qbCXhpFYFT8woMtJd91rPgWJC',
    'admin@example.com',
    'アドミン'
);

******************************************
備考：タイトルを作品名に変更する


productsテーブル構成
id //自動連番のID
name //商品名
price //商品価格(税込み)
title //作品タイトル
body //商品詳細コメント
img1 //商品画像パス
img2 //商品画像パス
img3 //商品画像パス
img4 //商品画像パス
img5 //商品画像パス
qty //在庫数　smallint?
point //ポイント
shipping //出荷目安日
category_id //カテゴリテーブル 外部キーが必要?
sub_category_id //現状必要なし
public_status //掲載状況 0でメイン非掲載 1でメイン掲載
sales_status_id //販売状況 0で販売中　1で売り切れ中　2で予約受付中　　sales_status.idと外部キー成約を結ぶべき？
created_at //作成日時
updated_at //更新日時
delete_flg //論理削除


CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL,
    price INT UNSIGNED NOT NULL,
    img1 TEXT,
    img2 TEXT,
    img3 TEXT,
    img4 TEXT,
    img5 TEXT,
    title TEXT NOT NULL,
    body TEXT NOT NULL,
    qty SMALLINT UNSIGNED NOT NULL,
    point INT UNSIGNED NOT NULL,
    shipping SMALLINT UNSIGNED NOT NULL,
    category SMALLINT UNSIGNED,
    sub_category SMALLINT UNSIGNED,
    public_status TINYINT UNSIGNED NOT NULL,
    sales_status TINYINT UNSIGNED NOT NULL,
    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6),
    updated_at TIMESTAMP(6) NULL DEFAULT NULL,
    delete_flg BOOLEAN DEFAULT FALSE
);

INSERT INTO products
(name, price, title, body, qty, point, shipping, category, public_status, sales_status)
VALUES
('テスト', 2000, 'タイトル', 'ボディ', 10, 20, 3, 1, 1, 1),
('なにか', 1000, 'タイトル', 'ボディ', 10, 20, 3, 2, 1, 1),
('tett', 5000, 'タイトル', 'ボディ', 10, 20, 3, 1, 1, 1),
('補完', 200, 'タイトル', 'ボディ', 10, 20, 3, 1, 1, 1),
('DVD', 11100, 'タイトル', 'ボディ', 10, 20, 3, 1, 1, 1),
('缶バッジ', 5000, 'タイトル', 'ボディ', 10, 20, 3, 1, 1, 1),
('人形', 9000, 'タイトル', 'ボディ', 10, 20, 3, 1, 1, 1),
('CD', 10000, 'タイトル', 'ボディ', 10, 20, 3, 1, 1, 1),
('ほうじ茶', 12000, 'タイトル', 'ボディ', 10, 20, 3, 1, 1, 1),
('水', 15000, 'タイトル', 'ボディ', 10, 20, 3, 1, 1, 1),
('ノート', 12000, 'タイトル', 'ボディ', 10, 20, 3, 1, 1, 1),
('メモ帳', 23000, 'タイトル', 'ボディ', 10, 20, 3, 1, 1, 1),
('コード', 300, 'タイトル', 'ボディ', 10, 20, 3, 2, 1, 1),
('ギター', 200, 'タイトル', 'ボディ', 10, 20, 3, 1, 1, 1),
('コンセント', 100, 'タイトル', 'ボディ', 10, 20, 3, 1, 1, 1);


******************************************
categoriesテーブル情報
id //自動連番
name //カテゴリー名
img //カテゴリーバナー
public_status //掲載状況 0でメイン非掲載 1でメイン掲載
created_at //作成日時
updated_at //更新日時
delete_flg //論理削除

CREATE TABLE categories (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL,
    img TEXT,
    public_status TINYINT UNSIGNED NOT NULL,
    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6),
    updated_at TIMESTAMP(6) NULL DEFAULT NULL,
    delete_flg BOOLEAN DEFAULT FALSE
);

INSERT INTO categories (
    name,
    public_status
) VALUES (
    'Youtuber',
    '1'
);
******************************************
sub_categoriesテーブル構成
id //自動連番
name //サブカテゴリー名
category_id //親カテゴリーid     外部キーは必要?
img //カテゴリーバナー
public_status //掲載状況 0でメイン非掲載 1でメイン掲載
created_at //作成日時
updated_at //更新日時
delete_flg //論理削除

CREATE TABLE sub_categoryies (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL,
    category_id BIGINT,
    img TEXT,
    public_status TINYINT UNSIGNED NOT NULL,
    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6),
    updated_at TIMESTAMP(6) NULL DEFAULT NULL,
    delete_flg BOOLEAN DEFAULT FALSE
);


******************************************
sales_statusテーブル構成
id // productと紐づくid  productと外部キー成約を結ぶべき？
name //
sub_name //HTMLクラス用




CREATE TABLE sales_status (
    id TINYINT NOT NULL PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    sub_name VARCHAR(30)
);

INSERT INTO sales_status VALUES
(0, '売り切れ中' , 'sold-out'),
(1, '販売中' , 'now_sale'),
(2, '販売予定' , 'plan'),
(99, '予約受付中' , 'reservation');

******************************************

contactテーブル構成
id //自動連番のid
name // 投稿者の名前
email //　メールアドレス
member_id// 会員id(ログイン済みであれば自動取得) 、未ログイン者は0
title //内容の種類
other_title //その他の種類
message //本文
status //返信状況  0は未対応　1は対応中　99は対応完了


CREATE TABLE contacts (
    id SERIAL PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    email TEXT NOT NULL,
    member_id VARCHAR(30) NOT NULL,
    title TINYINT NOT NULL,
    other_title TEXT,
    message TEXT NOT NULL,
    status TINYINT NOT NULL,
    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6),
    updated_at TIMESTAMP(6) NULL DEFAULT NULL,
    delete_flg BOOLEAN DEFAULT FALSE
)

******************************************




******************************************
ユーザーテーブル構成
id //自動連番のid
password //ログイン時のパスワード
first_name // 名前
last_name // 性
name_kana // カナ
birth_year //年
birth_month //月
birth_day //日
gender //性別
email //メールアドレス
password //ログイン時のパスワード
tel //分割するか?
postal_code //分割するか?
pref //　都道府県
city //市区町村
address //町名番地
other //マンション等
status //状況
point //所有ポイント
payment_info //前回の支払い方法
memo //備考
last_login_date //最終ログイン日
created_at //
updated_at //
delete_flg //


*****************************************
addressテーブル構成

id //
user_id //紐付けるユーザー
first_name // 名前
last_name // 性
name_kana // カナ
year //何歳
gender //性別
email //メールアドレス
password //ログイン時のパスワード
tel1 //分割するか?
tel2 //分割するか?
tel3 //分割するか?
postal_code1 //分割するか?
postal_code2
pref //都道府県
city //市区町村
address //町名番地
other //マンション等
created_at //
updated_at //
delete_flg //



とりあえず実装内容
未ログインではカートやお気に入り等のコンテンツは利用できず、遷移しようとしてもログイン画面に移動する。
ログイン画面からアカウント作成ページにも遷移できる。
アカウント作成に必要な項目は少なく、送付先も登録しなくても良い。
マイページでカート、お気に入り、商品購入履歴が見れる。退会やログアウトも可能。
マイページで送付先、クレジットカード情報も追加できる。(複数追加もできるようにする)
カート画面で商品の個数が変更できる。プラス・マイナスボタンを押すと再読み込みが走る。0になるときはどうする？
カート内の商品をお気に入りに、お気に入りをカートに移動もできる。商品購入が完了した場合カート内のアイテムを消すのは当然としてお気に入りも消す？
商品購入履歴で画像も表示していたが、画像はどうする？
