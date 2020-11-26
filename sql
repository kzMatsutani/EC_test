CREATE DATABASE EC_test CHARACTER SET utf8 COLLATE utf8_general_ci;

管理ユーザーテーブル構成
id //自動連番のID
login_id //ログインフォームで入力確認するid 文字数は?
password //ログインフォームで入力確認するpass 文字数?
email //メールアドレス  必要?
name //ログイン者の名前　必要?

CREATE TABLE admins (
    id SERIAL PRIMARY KEY,
    login_id VARCHAR(30) NOT NULL,
    password VARCHAR(30) NOT NULL,
    email TEXT NOT NULL,
    name VARCHAR(30) NOT NULL,
    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6),
    updated_at TIMESTAMP(6) NULL DEFAULT NULL,
    delete_flg BOOLEAN DEFAULT FALSE
);


productsテーブル構成
id //自動連番のID
name //商品名
price //商品価格(税抜)
img //商品画像パス
title //商品サブメッセージ (必要?)
body //商品詳細コメント
qty //在庫数　smallint?
point //ポイント
shipping //出荷目安日
category //カテゴリ
sub_category //サブカテゴリ
public_status //掲載状況 0でメイン非掲載 1でメイン掲載
sales_status //販売状況 0で販売中　1で売り切れ中　2で予約受付中　3で
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
(name, price, title, body, qty, point, shipping, public_status, sales_status)
VALUES ('test', 1000, 'タイトル', 'ボディ', 10, 20, 3, 1, 1);

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
)

sub_categoriesテーブル構成
id //自動連番
name //サブカテゴリー名
category_id //親カテゴリーid
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
