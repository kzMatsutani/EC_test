<?php
class SendMail
{
    //商品購入時にお客様に送信するサンクスメール
    public function sendMailForUser($post, $user_info, $cart_in_product, $payment, $price)
    {
        mb_language('Japanese');
        mb_internal_encoding('UTF-8');
        $to = $user_info['mail'];
        $subject = '【冬瓜ダイエット 桜物産】商品のご注文ありがとうございます';
        $headers = 'From:' . SALE_MAIL_ADDRESS;
        $format =
            '%s様' . "\n"
            . "\n"
            . 'お世話になっております。' . "\n"
            . '冬瓜ダイエット専門店 桜物産でございます。' . "\n"
            . 'この度は当店の商品をご購入いただきまして、誠にありがとうございます。' . "\n"
            . "\n"
            . '%s様が購入手続きをされました商品について' . "\n"
            . 'お間違えのないようメールをお送りさせていただきました。' . "\n"
            . '今一度ご購入商品などに、お間違えのないようご確認いただけましたら幸いです。' . "\n"
            . '_________________________________________________________'. "\n"
            . '  [購入情報]' . "\n"
            . "\n"
            . '%s'  //商品情報を表示
            . '小計:%s円' . "\n"
            . '送料:%s円' . "\n"
            . '総額:%s円' . "\n"
            . '_________________________________________________________' . "\n"
            . '  [お支払い方法]' . "\n"
            . '%s'
            . "\n"
            . '%s' //支払い方法に適応した文を表示
            . '_________________________________________________________' . "\n"
            . '  [送付先情報]' . "\n"
            . "\n"
            . 'お名前：%s'. "\n"
            . 'フリガナ：%s' . "\n"
            . '電話番号：%s-%s-%s' . "\n"
            . '郵便番号：%s-%s' . "\n"
            . '都道府県：%s' . "\n"
            . '市区町村：%s' . "\n"
            . '番地：%s' . "\n"
            . 'マンション名等：%s' . "\n"
            . '_________________________________________________________' . "\n"
            . '  [請求先情報]' . "\n"
            . "\n"
            . 'お名前：%s' . "\n"
            . 'フリガナ：%s' . "\n"
            . '電話番号：%s-%s-%s' . "\n"
            . 'メールアドレス：%s' . "\n"
            . '郵便番号：%s-%s' . "\n"
            . '都道府県：%s' . "\n"
            . '市区町村：%s' . "\n"
            . '番地：%s' . "\n"
            . 'マンション名等：%s' . "\n"
            . '_________________________________________________________' . "\n"
            . "\n"
            . '商品ご到着まで今しばらくお待ち下さい。' . "\n"
            . "\n"
            . '*********************************************************' . "\n"
            . '有限会社 桜物産' . "\n"
            . '00-0000-0000' . "\n"
            . '沖縄県 東岸市 ダイエット町999-999' . "\n"
            . '●●●●●●●●●@●●●.●●' . "\n"
            . 'https://extremesites.tokyo/training/tougan-matsutani/' . "\n"
            . '*********************************************************' . "\n"
        ;
        //商品情報の展開
        $product_info = '';
        foreach ($cart_in_product as $product) {
            $product_info .=
                '商品名：' . $product['name'] . "\n"
                . '商品単価：' . number_format($product['price']) . "\n"
                . '数量：' . $product['num'] . "\n"
                . '-----------------------------------------------------' . "\n"
            ;
        }
        //支払い方法が銀行振り込みの場合
        $payment_info = '';
        if ($post['payment_id'] == 3) {
            $payment_info =
                "\n"
                . '【お振込先】' . "\n"
                . '銀行 : 〇〇銀行' . "\n"
                . '支店 : 〇〇支店' . "\n"
                . '支店コード : 000' . "\n"
                . '口座番号：0000000' . "\n"
            ;
        }
        //各情報の配列化
        $message_info = [
            $user_info['name'], $user_info['name'],
            $product_info, number_format($price['sub_total']),
            number_format($price['shipping_price']), number_format($price['total_price']),
            $payment[$post['payment_id']]['name'], $payment_info,
            $post['name'], $post['name_kana'],
            $post['tel1'], $post['tel2'],
            $post['tel3'], $post['postal_code1'],
            $post['postal_code2'], getPref($post['pref']),
            $post['city'], $post['address'],
            $post['other'], $user_info['name'],
            $user_info['name_kana'],$user_info['tel1'],
            $user_info['tel2'],$user_info['tel3'],
            $user_info['mail'], $user_info['postal_code1'],
            $user_info['postal_code2'], getPref($user_info['pref']),
            $user_info['city'], $user_info['address'],
            $user_info['other']
        ];
        $message = vsprintf($format, $message_info);
        return mb_send_mail($to, $subject, $message, $headers);
    }
}
