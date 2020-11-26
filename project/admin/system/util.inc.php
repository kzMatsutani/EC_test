<?php
//クロススプリクティング対策
function h($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

//ログインされていなかった場合はログイン画面へ遷移(アドミン)
function confirmAuthAdmin()
{
    if (empty($_SESSION['authenticated'])) {
        header('Location: login.php');
        exit;
    }
}

//ログインされていなかった場合はログイン画面へ遷移(ユーザー)
function confirmAuthUser()
{
    if (empty($_SESSION['userAuthenticated'])) {
        $path = pathinfo($_SERVER['REQUEST_URI']);
        $path = $path['basename'];
        header('Location: login.php?url=' . $path);
        exit;
    }
}

//ユーザーがログインしている状態でログインページに遷移した場合はトップへ(ユーザー)
function confirmAuthUserTop()
{
    if (!empty($_SESSION['userAuthenticated'])) {
        header('Location: index.php');
        exit;
    }
}

//現在のページ(URI)を取得し、ページ名を返す
function getPage()
{
    //URIの取得
    $path = pathinfo($_SERVER['REQUEST_URI']);
    $first_word = [
        'top'     => '管理トップ',
        'product' => '商品',
        'sales_management' => '売上',
        'category' => 'カテゴリー',
        'error'   => 'エラー',
        'index'   => 'トップ',
        'cart'    => 'カート',
        'login'   => 'ログイン',
        'tougan-matsutani' => 'トップ'
    ];
    $type_word = [
        'update' => '編集',
        'create' => '新規登録',
        ''       => ''
    ];
    $second_word = [
        '_list' => '管理リスト',
        '_edit' => '',
        '_done' => '完了',
        '_conf' => '確認',
        '_graph' => '管理グラフ',
        ''      => ''
    ];
    $type = $type_word[isset($_GET['type']) ? $_GET['type'] : ''];
    $second = substr(strrchr($path['filename'], '_'), 0);
    $first = str_replace($second, '', $path['filename']);
    echo $first_word[$first] . $type . $second_word[$second];
}

//エラーメッセージの取得
function getErrorMessage($message, $word)
{
    $message = !empty(ERROR_MESSAGE[$message]) ? ERROR_MESSAGE[$message] : ERROR_MESSAGE['other'];
    $word = !empty(ERROR_WORD[$word]) ? ERROR_WORD[$word] : ERROR_WORD['other'];
    printf($message, $word);
}

//トークン生成
function getToken()
{
    return hash('sha256',session_id() . microtime(true));
}

//トークンが生成されていなかった場合はエラー画面へ遷移
function authToken($postToken)
{
    if (empty($_SESSION['token']) || $_SESSION['token'] != $postToken) {
        header('Location: error.php?message=transition');
        exit;
    }
}

//都道府県の取得、又は検索
function getPref($num = NULL)
{
    $pref = [
        0 => '未選択',
        1 => '北海道' ,
        2 => '青森県' ,
        3 => '岩手県' ,
        4 => '宮城県' ,
        5 => '秋田県' ,
        6 => '山形県' ,
        7 => '福島県' ,
        8 => '茨城県' ,
        9 => '栃木県' ,
        10 => '群馬県' ,
        11 => '埼玉県' ,
        12 => '千葉県' ,
        13 => '東京都' ,
        14 => '神奈川県' ,
        15 => '新潟県' ,
        16 => '富山県' ,
        17 => '石川県' ,
        18 => '福井県' ,
        19 => '山梨県' ,
        20 => '長野県' ,
        21 => '岐阜県' ,
        22 => '静岡県' ,
        23 => '愛知県' ,
        24 => '三重県' ,
        25 => '滋賀県' ,
        26 => '京都府' ,
        27 => '大阪府' ,
        28 => '兵庫県' ,
        29 => '奈良県' ,
        30 => '和歌山県' ,
        31 => '鳥取県' ,
        32 => '島根県' ,
        33 => '岡山県' ,
        34 => '広島県' ,
        35 => '山口県' ,
        36 => '徳島県' ,
        37 => '香川県' ,
        38 => '愛媛県' ,
        39 => '高知県' ,
        40 => '福岡県' ,
        41 => '佐賀県' ,
        42 => '長崎県' ,
        43 => '熊本県' ,
        44 => '大分県' ,
        45 => '宮崎県' ,
        46 => '鹿児島県' ,
        47 => '沖縄県'
    ];
    return isset($num) ? $pref[$num] : $pref;
}
