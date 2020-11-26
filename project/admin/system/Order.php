<?php
class Order extends Model
{
    //ログインしているユーザーのカート内商品の一覧
    public function getProductInCart($user_id)
    {
        try {
            //データベースに接続
            parent::connect();
            $sql =
                ' SELECT '
                    . ' pr.* , '
                    . ' ct.id AS cart_id , '
                    . ' ct.product_id , '
                    . ' ct.num '
                . ' FROM '
                    . ' product pr '
                . ' LEFT JOIN '
                    . ' cart ct '
                . ' ON '
                    . ' pr.id = ct.product_id '
                . ' WHERE '
                    . ' delete_flg = 0 '
                . ' AND '
                    . ' ct.user_id = ? '
                . ' ORDER BY '
                    . ' ct.id '
            ;
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$user_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return false;
        }
    }

    //カート内に商品を追加(カート内に同一商品がある場合は数量を追加)
    public function addProductInCart($user_id, $product_id, $num)
    {
        try {
            //データベースに接続
            parent::connect();
            //一度カート内に同じ商品IDがないか検索。
            $sql =
                ' SELECT '
                    . ' * '
                . ' FROM '
                    . ' cart '
                . ' WHERE '
                    . ' user_id = ? '
                . ' AND '
                    . ' product_id = ?'
            ;
            //トランザクション開始
            $this->dbh->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $this->dbh->beginTransaction();
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$user_id, $product_id]);
            $product = $stmt->fetch();
            //カート内に同一商品がない場合は新規追加
            if ($product == '') {
                $sql =
                    ' INSERT INTO cart '
                    . ' VALUES ( '
                        . ' NULL , '//自動連番
                        . ' ? , '
                        . ' ? , '
                        . ' ? '
                    . ' ) '
                ;
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute([$user_id, $product_id, $num]);
                $this->dbh->commit();
                return true;
            }
            //カート内に同一商品がある場合は数量を追加(101以上は100に固定)
            $update_num = ($product['num'] + $num) < 101 ? $product['num'] + $num : 100;
            $sql =
                ' UPDATE '
                    . ' cart '
                . ' SET '
                    . ' num = ? '
                . ' WHERE '
                    . ' id = ? '
            ;
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$update_num, $product['id']]);
            $this->dbh->commit();
            return true;
        } catch (PDOException $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    //カート内商品の数の変更
    public function changeNumProductInCart($num, $product_id)
    {
        try {
            //データベースに接続
            parent::connect();
            //もし数量が0になっていた場合は削除
            if ($num == 0) {
                $sql =
                    ' DELETE '
                    . ' FROM '
                        . ' cart '
                    . ' WHERE '
                        . ' id = ? '
                ;
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute([$product_id]);
                return true;
            }
            $sql =
                ' UPDATE '
                    . ' cart '
                . ' SET '
                    . ' num = ? '
                . ' WHERE '
                    . ' id = ? '
            ;
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$num, $product_id]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    //対象の商品をカートから削除
    public function deleteProductInCart($product_id)
    {
        try {
            //データベースに接続
            parent::connect();
            $sql =
                ' DELETE '
                . ' FROM '
                    . ' cart '
                . ' WHERE '
                    . ' id = ? '
            ;
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$product_id]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    //カートにある商品をカートから全て削除
    public function deleteAllProductInCart($user_id)
    {
        try {
            //データベースに接続
            parent::connect();
            $sql =
                ' DELETE '
                . ' FROM '
                    . ' cart '
                . ' WHERE '
                    . ' user_id = ? '
            ;
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$user_id]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    //カートにある商品から支払情報の絞り込み
    public function getPaymentInfo($cart_in_product)
    {
        try {
            //データベースに接続
            parent::connect();
            $sql =
                ' SELECT '
                    . ' mp.* , '
                    . ' mp.id , '
                    . ' count(mp.id) AS count'
                . ' FROM '
                    . ' m_payment mp '
                . ' LEFT JOIN '
                    . ' product_payment pp '
                . ' ON '
                    . ' mp.id = pp.payment_id '
                . ' WHERE '
                    . ' pp.product_id '
                . ' IN ( '
            ;
            foreach ($cart_in_product as $product) {
                $in[] = ' ? ';
                $param[] = ['value' => $product['id']];
            }
            $sql .=
                implode(' , ' , $in)
                . ' ) GROUP BY '
                    . ' mp.id '
                . ' HAVING '
                    . ' count = ? '
            ;
            $stmt = $this->dbh->prepare($sql);
            for ($i = 1; $i <= count($param); $i++) {
                $stmt->bindValue($i, $param[$i - 1]['value'], PDO::PARAM_INT);
            }
            $stmt->bindValue(count($param) + 1, count($cart_in_product), PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
        } catch (PDOException $e) {
            return false;
        }
    }

    //order, order_detilテーブルに注文情報を登録
    public function createOrderInfo($post, $user, $cart_in_product, $price)
    {
        try {
            //データベースに接続
            parent::connect();
            //送付先を変更している場合
            $sql =
                ' INSERT INTO `order` ( '
                    . ' user_id , '
                    . ' name , '
                    . ' name_kana , '
                    . ' mail , '
                    . ' tel1 , '
                    . ' tel2 , '
                    . ' tel3 , '
                    . ' postal_code1 , '
                    . ' postal_code2 , '
                    . ' pref , '
                    . ' city , '
                    . ' address , '
                    . ' other , '
                    . ' payment_id , '
                    . ' sub_price , '
                    . ' shipping_price , '
                    . ' tax , '
                    . ' total_price '
                . ' ) VALUES ( '
                    . ' ? , '
                    . ' ? , '
                    . ' ? , '
                    . ' ? , '
                    . ' ? , '
                    . ' ? , '
                    . ' ? , '
                    . ' ? , '
                    . ' ? , '
                    . ' ? , '
                    . ' ? , '
                    . ' ? , '
                    . ' ? , '
                    . ' ? , '
                    . ' ? , '
                    . ' ? , '
                    . ' ? , '
                    . ' ?  '
                . ' ) '
            ;
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(1, $user['id'], PDO::PARAM_INT);
            $stmt->bindValue(2, $post['name'], PDO::PARAM_STR);
            $stmt->bindValue(3, $post['name_kana'], PDO::PARAM_STR);
            $stmt->bindValue(4, $user['mail'], PDO::PARAM_STR);
            $stmt->bindValue(5, $post['tel1'], PDO::PARAM_STR);
            $stmt->bindValue(6, $post['tel2'], PDO::PARAM_STR);
            $stmt->bindValue(7, $post['tel3'], PDO::PARAM_STR);
            $stmt->bindValue(8, $post['postal_code1'], PDO::PARAM_STR);
            $stmt->bindValue(9, $post['postal_code2'], PDO::PARAM_STR);
            $stmt->bindValue(10, $post['pref'], PDO::PARAM_INT);
            $stmt->bindValue(11, $post['city'], PDO::PARAM_STR);
            $stmt->bindValue(12, $post['address'], PDO::PARAM_STR);
            $stmt->bindValue(13, $post['other'], PDO::PARAM_STR);
            $stmt->bindValue(14, $post['payment_id'], PDO::PARAM_INT);
            $stmt->bindValue(15, $price['sub_total'], PDO::PARAM_INT);
            $stmt->bindValue(16, $price['shipping_price'], PDO::PARAM_INT);
            $stmt->bindValue(17, $price['tax'], PDO::PARAM_INT);
            $stmt->bindValue(18, $price['total_price'], PDO::PARAM_INT);
            $stmt->execute();
            $sql =
                ' SELECT '
                    . ' AUTO_INCREMENT '
                . ' FROM '
                    . ' INFORMATION_SCHEMA.TABLES '
                . ' WHERE '
                    . ' TABLE_SCHEMA = \'k_matsutani\' '
                . ' AND '
                    . ' TABLE_NAME   = \'order\' '
            ;
            $stmt =$this->dbh->query($sql);
            $insertId = $stmt->fetch();
            $sql =
                ' INSERT INTO order_detail '
                . ' VALUES ( '
                    . ' NULL , ' //idの自動連番
                    . ' ? , '
                    . ' ? , '
                    . ' ? , '
                    . ' ? , '
                    . ' ? '
                . ' ) '
            ;
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(1, $insertId[0] - 1, PDO::PARAM_INT);
            foreach ($cart_in_product as $product) {
                $stmt->bindValue(2, $product['id'], PDO::PARAM_INT);
                $stmt->bindValue(3, $product['name'], PDO::PARAM_STR);
                $stmt->bindValue(4, $product['price'], PDO::PARAM_INT);
                $stmt->bindValue(5, $product['num'], PDO::PARAM_INT);
                $stmt->execute();
            }
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    //orderテーブルにインサート、カート内を全て削除、メール送信を行う
    public function sellProduct($post, $user_id)
    {
        try {
            //データベースに接続
            parent::connect();
            $user = new User();
            $mail = new SendMail();
            //トランザクション開始
            $this->dbh->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $this->dbh->beginTransaction();
            //ユーザー情報とカート内情報を取得
            if (!$user_info = $user->getUserInfo($user_id)) {
                throw new Exception;
            }
            if (!$cart_in_product = $this->getProductInCart($user_id)) {
                throw new Exception;
            }
            if (!$payment = $this->getPaymentInfo($cart_in_product)) {
                throw new Exception;
            }
            //カート内共通の支払い方法の確認
            if (empty($payment[$post['payment_id']])) {
                throw new Exception;
            }
            //DBから取得したカート内の小計、送料、総額を計算
            $price['sub_total'] = 0;
            foreach ($cart_in_product as $product) {
                $price['sub_total'] += $product['price'] * $product['num'];
            }
            $price['tax'] = 0;
            $price['shipping_price'] = $price['sub_total'] < 10000 ? 1000 : 0;
            $price['total_price'] = $price['sub_total'] + $price['shipping_price'];
            //order, order_detailテーブルに登録
            if (!$this->createOrderInfo($post, $user_info, $cart_in_product, $price)) {
                throw new Exception;
            }
            //カートの商品を全て削除
            if (!$this->deleteAllProductInCart($user_id)) {
                throw new Exception;
            }
            //最後にメールを送信
            if (!$mail->sendMailForUser($post, $user_info, $cart_in_product, $payment, $price)) {
                throw new Exception;
            }
            $this->dbh->commit();
            return true;
        } catch (Excaption $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    //cart_edit.phpで入力したユーザー情報のバリデーションチェック
    public function validateCartEdit($post, $payment)
    {
        //郵便番号
        if ($post['postal_code2'] == '' || $post['postal_code1'] == '') {
            $_SESSION['error']['postal_code'] = '※郵便番号が入力されていません 上3桁、下4桁をご入力ください' ;
        } elseif (!preg_match('/^\A[0-9]{3}$/u', $post['postal_code1']) || !preg_match('/^\A[0-9]{4}$/u', $post['postal_code2'])) {
            $_SESSION['error']['postal_code'] = '※上3桁、下4桁を半角数字でご入力ください';
        }

        //名前
        if ($post['name_kana'] == '' ) {
            $_SESSION['error']['name_kana'] = '※フリガナが入力されていません';
        } elseif (!preg_match('/^[ァ-ヶ][ァ-ヶ 　 ー]{0,19}$/u', $post['name_kana'])) {
            $_SESSION['error']['name_kana'] = '※20文字以内のカタカナでご入力ください';
        }
        if ($post['name'] == '') {
            $_SESSION['error']['name']= '※名前が入力されていません';
        } elseif (!preg_match('/^[^ 　].{1,15}$/u', $post['name'])) {
            $_SESSION['error']['name'] = '※15文字以内でご入力ください';
        }

        //住所
        if ($post['pref'] == 0) {
            $_SESSION['error']['pref'] = '※都道府県が選択されていません';
        }
        if ($post['city'] == '') {
            $_SESSION['error']['city'] = '※市区町村が入力されていません';
        } elseif (!preg_match('/^[^ 　].{1,15}$/u', $post['city'])) {
            $_SESSION['error']['city'] = '※15文字以内でご入力ください';
        }
        if ($post['address'] == '') {
            $_SESSION['error']['address'] = '※町名、番地が入力されていません';
        } elseif (!preg_match('/^[^ 　].{1,100}$/u', $post['address'])) {
            $_SESSION['error']['address'] = '※100文字以内でご入力ください';
        }
        //建物情報は未入力を認める
        if (!preg_match('/^.{0,100}$/u', $post['other'])) {
            $_SESSION['error']['other'] = '※100文字以内でご入力ください';
        }

        //電話番号
        if ($post['tel1'] == '' || $post['tel2'] == '' || $post['tel3'] == '') {
            $_SESSION['error']['tel'] = '※電話番号が入力されていない箇所があります。';
        } elseif (!preg_match('/^\A[0-9]{1,5}+$/u', $post['tel1'])) {
            $_SESSION['error']['tel'] = '※市外局番は1~5桁の半角数字でご入力ください';
        } elseif (!preg_match('/^\A[0-9]{1,5}+$/u', $post['tel2'])) {
            $_SESSION['error']['tel'] = '※市内局番は1~5桁の半角数字でご入力ください';
        } elseif (!preg_match('/^\A[0-9]{1,5}+$/u', $post['tel3'])) {
            $_SESSION['error']['tel'] = '※加入者番号は1~5桁の半角数字でご入力ください';
        }

        //支払情報、もし選択できないはずの支払い方法が選択されていた場合はエラー画面へ
        if ($post['payment_id'] == '') {
            $_SESSION['error']['payment_id'] = '※支払い方法が選択されていません';
        } elseif (empty($payment[$post['payment_id']])) {
            header('Location: error.php?message=parameter');
            exit;
        }
    }
}
