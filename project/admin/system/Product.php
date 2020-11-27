<?php
class Product extends Model
{
    //商品データ一覧の取得
    public function getProductList($column = NULL, $order = NULL)
    {
        try {
            //データベースに接続
            parent::connect();
            $sql =
                ' SELECT '
                    . ' id , '
                    . ' name , '
                    . ' price , '
                    . ' img1 , '
                    . ' qty , '
                    . ' point , '
                    . ' public_status , '
                    . ' sales_status , '
                    . ' DATE_FORMAT(created_at, \'%Y-%m-%d %k:%i:%s\') AS created_at , '
                    . ' DATE_FORMAT(updated_at, \'%Y-%m-%d %k:%i:%s\') AS updated_at '
                . ' FROM '
                    . ' products '
                . ' WHERE '
                    . ' delete_flg = 0 '
            ;
            //引数の配列に入っているはずのワードのバリデーション
            $vali = ['id', 'name', 'price', 'created_at', 'updated_at'];
            $sort = ['ASC', 'DESC'];
            if (in_array($column, $vali) && in_array($order, $sort)) {
                $sql .=
                    ' ORDER BY '
                        . $column
                        . ' IS NULL ASC , '
                        . $column
                        . ' = \'\' ASC , '
                        . $column
                        . ' '
                        . $order
                        . ' , '
                        . ' id DESC '
                ;
            } else {
                $sql .=
                    ' ORDER BY '
                        . ' id DESC '
                ;
            }
            $stmt = $this->dbh->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return false;
        }
    }

    //商品の検索
    public function searchProduct($name, $price1, $price2, $category)
    {
        try {
            //データベースに接続
            parent::connect();
            $where = [];
            $param = [];
            $sql =
                ' SELECT '
                    . ' pr.id , '
                    . ' pr.name , '
                    . ' pr.sub_name , '
                    . ' pr.day , '
                    . ' pr.price , '
                    . ' pr.img , '
                    . ' pr.description , '
                    . ' DATE_FORMAT(pr.created_at, \'%Y-%m-%d %k:%i:%s\') AS created_at , '
                    . ' DATE_FORMAT(pr.updated_at, \'%Y-%m-%d %k:%i:%s\') AS updated_at '
                . ' FROM '
                    . ' products pr '
                . ' LEFT JOIN '
                    . ' product_payment pp '
                . ' ON '
                    . ' pr.id = pp.product_id '
                . ' WHERE '
                    . ' delete_flg = 0 '
            ;
            if (!empty($name)) {
                $where[] = ' pr.name LIKE ? ';
                $param[] = ['value' => '%' . $name . '%', 'param' => PDO::PARAM_STR];
            }
            if (!empty($price1)) {
                $where[] = ' pr.price >= ? ';
                $param[] = ['value' => $price1, 'param' => PDO::PARAM_INT];
            }
            if (!empty($price2)) {
                $where[] = ' pr.price <= ? ';
                $param[] = ['value' => $price2, 'param' => PDO::PARAM_INT];
            }
            if (!empty($where)) {
                $sql .= ' AND ' . implode(' AND ' , $where);
            }
            //現在の支払い方法の全てを取得しIDが一致するかのチェック
            $category_id = $this->getcategoryList();
            foreach ($category as $val) {
                if (!empty($category_id[$val])) {
                    $cash[] = ' ? ';
                    $param[] = ['value' => $val, 'param' => PDO::PARAM_INT];
                }
            }
            if (!empty($cash)) {
                $sql .=
                    ' AND '
                        . ' pp.payment_id '
                    . ' IN ( '
                        . implode(' , ', $cash)
                    . ' ) '
                ;
            }
            $sql .=
                ' GROUP BY '
                    . ' pr.id DESC '
            ;
            $stmt = $this->dbh->prepare($sql);
            for ($i = 1; $i <= count($param); $i++) {
                $stmt->bindValue($i, $param[$i - 1]['value'], $param[$i - 1]['param']);
            }
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return false;
        }
    }

    //商品データ単体の取得
    public function getProduct($id)
    {
        try {
            //データベースに接続
            parent::connect();
            $sql =
                ' SELECT '
                    . ' * '
                . ' FROM '
                    . ' products '
                . ' WHERE '
                    . ' id = ? '
                . ' AND '
                    . ' delete_flg = 0 '
            ;
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    //
    public function getCategoryList()
    {
        try {
            //データベースに接続
            parent::connect();
            $sql =
                ' SELECT '
                    . ' cate.* , '
                    . ' cate.id '
                . ' FROM '
                    . ' categories cate'
                . ' WHERE '
                    . ' cate.delete_flg = 0 '
            ;
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function testtt($product_id = NULL)
    {
        try {
            //データベースに接続
            parent::connect();
            $sql =
                ' SELECT '
                    . ' mp.* , '
                    . ' mp.id , '
                    . ' CASE '
                        . ' WHEN ( '
                            . ' SELECT '
                                . ' payment_id '
                            . ' FROM '
                                . ' product_payment '
                            . ' WHERE '
                                . ' product_id = ? '
                            . ' AND '
                                . 'mp.id = payment_id '
                        . ' ) THEN 1 '
                        . ' ELSE 0 '
                    . ' END AS status '
                . ' FROM '
                    . ' categories '
            ;
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$product_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
        } catch (PDOException $e) {
            // return false;
            var_dump($e);
        }
    }

    //対象の商品に選択されている支払い方法のみ取得
    public function getSelectedPaymentOfProduct($product_id)
    {
        try {
            //データベースに接続
            parent::connect();
            $sql =
                ' SELECT '
                    . ' mp.* '
                . ' FROM '
                    . ' m_payment mp '
                . ' LEFT JOIN '
                    . ' product_payment pp '
                . ' ON '
                    . ' mp.id = pp.payment_id '
                . ' WHERE '
                    . ' pp.product_id = ? '
            ;
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$product_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return false;
        }
    }


    //商品編集
    public function updateProduct($id, $name, $sub_name, $day, $price, $description, $selected_payment)
    {
        try {
            //データベースに接続
            parent::connect();
            //商品のデータを取得
            $sql =
                ' UPDATE '
                    . ' products '
                . ' SET '
                    . ' name = ? , '
                    . ' sub_name = ? , '
                    . ' day =  ? , '
                    . ' price =  ? , '
                    . ' description = ? , '
                    . ' updated_at = NOW(6) '
                . ' WHERE '
                    . ' id = ? '
            ;
            //トランザクション開始
            $this->dbh->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $this->dbh->beginTransaction();
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$name, $sub_name, $day, $price, $description, $id]);
            //指定商品の支払い方法の削除
            $sql =
                ' DELETE '
                . ' FROM  '
                    . ' product_payment '
                . ' WHERE '
                    . ' product_id = ? '
            ;
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$id]);
            //現在の全ての支払い方法を取得
            if (($payment = $this->getPaymentList()) === false) {
                throw new PDOException;
            }
            //支払い方法が選択されており支払い方法のIDが実際に存在していれば再登録
            if (!empty($selected_payment)) {
                foreach ($selected_payment as $key => $value) {
                    if (!empty($payment[$key])) {
                        $sql =
                            ' INSERT INTO product_payment '
                            . ' VALUES ( '
                                . ' ? ,'
                                . ' ? '
                            . ' ) '
                        ;
                        $stmt = $this->dbh->prepare($sql);
                        $stmt->execute([$id, $key]);
                    }
                }
            }
            $this->dbh->commit();
            return true;
        } catch (PDOException $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    //商品新規登録
    function createProduct($name, $sub_name, $day, $price, $description, $selected_payment)
    {
        try {
            //データベースに接続
            parent::connect();
            //商品のデータを新規登録
            $sql =
                ' INSERT INTO products ( '
                    . ' name , '
                    . ' sub_name , '
                    . ' day , '
                    . ' price , '
                    . ' description '
                . ' ) VALUES ( '
                    . ' ? , '
                    . ' ? , '
                    . ' ? , '
                    . ' ? , '
                    . ' ? '
                . ' ) '
            ;
            //トランザクション開始
            $this->dbh->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $this->dbh->beginTransaction();
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$name, $sub_name, $day, $price, $description]);
            $insertId = $this->dbh->lastInsertId();
            //現在の支払い方法の全てを取得
            if (($payment = $this->getPaymentList()) === false) {
                throw new PDOException;
            }
            //支払い方法が選択されており支払い方法のIDが実際に存在していれば登録
            if (!empty($selected_payment)) {
                foreach ($selected_payment as $key => $value) {
                    if (!empty($payment[$key])) {
                        $sql =
                            ' INSERT INTO product_payment '
                            . ' VALUES ( '
                                . ' ? ,'
                                . ' ? '
                            . ' ) '
                        ;
                        $stmt = $this->dbh->prepare($sql);
                        $stmt->execute([$insertId, $key]);
                    }
                }
            }
            $this->dbh->commit();
            return true;
        } catch (PDOException $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    //商品画像の登録
    function uploadProductImage($image, $id)
    {
        if ($image['error'] == UPLOAD_ERR_NO_FILE) {
            return 'ファイルが選択されませんでした';
        }
        try {
            //データベースに接続
            parent::connect();
            if ($image['error'] == UPLOAD_ERR_OK) {
                $image_path = mb_convert_encoding(date('YmdGis') . '_' . $image['name'], 'utf8', 'cp932');
                $sql =
                    ' UPDATE '
                        . ' products '
                    . ' SET '
                        . ' img = ? , '
                        . ' updated_at = NOW(6) '
                    . ' WHERE '
                        . ' id = ? '
                ;
                //トランザクション開始
                $this->dbh->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
                $this->dbh->beginTransaction();
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute([$image_path, $id]);
                //権限変更
                exec('sudo chmod 0777 ' . ABSOLUTE_PRODUCT_IMAGE_PATH);
                if (move_uploaded_file($image['tmp_name'], ABSOLUTE_PRODUCT_IMAGE_PATH . $image_path)) {
                    //成功時に権限を戻して空値を返す
                    exec('sudo chmod 0755 ' . ABSOLUTE_PRODUCT_IMAGE_PATH);
                    $this->dbh->commit();
                    return '';
                }
                //画像が一時フォルダから移動出来なかった場合はロールバックし権限を戻す。
                $this->dbh->rollback();
                exec('sudo chmod 0755 ' . ABSOLUTE_PRODUCT_IMAGE_PATH);
                return 'ファイルのアップロードに失敗しました';
            }
            return 'ファイルのアップロードに失敗しました';
        } catch (PDOException $e) {
            $this->dbh->rollback();
            exec('sudo chmod 0755 ' . ABSOLUTE_PRODUCT_IMAGE_PATH);
            return 'DBエラーによりファイルのアップロードに失敗しました';
        }
    }

    //商品をリストから除外 (delete_flgを下げる)
    public function deleteProduct($id)
    {
        try {
            //データベースに接続
            parent::connect();
            //商品のデータを取得
            $sql =
                ' UPDATE '
                    . ' products '
                . ' SET '
                    . ' delete_flg = 1 '
                . ' WHERE '
                    . ' id = ? '
            ;
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$id]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    //商品登録や商品編集にてGETパラメーターが変更された場合はエラー画面へ
    public function checkProductEditType()
    {
        if ($_GET['type'] != 'update' && $_GET['type'] != 'create') {
            header('Location: error.php?message=parameter');
            exit;
        }
        if ($_GET['type'] == 'update' && (empty($_GET['id']) || !($item = $this->getProduct($_GET['id'])))) {
            header('Location: error.php?message=parameter');
            exit;
        }
    }
}
