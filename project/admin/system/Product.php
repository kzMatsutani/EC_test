<?php
class Product extends Model
{
    //商品データ一覧の取得
    public function getProductList($column = NULL, $order = NULL, $page = 0, $limit = 10)
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
                . ' delete_flg = 0 ';
            //ソート
            $sql .= $this->sortProductAdmin($column, $order);
            $sql .= ' LIMIT ? , ? ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(1, $page, PDO::PARAM_INT);
            $stmt->bindValue(2, $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            // return false;
            var_dump($e);
        }
    }


    function sortValidation($keyword)
    {
        $validate = [
            'created_ASC',
            'created_DESC',
            'price_ASC',
            'price_DESC',
            'name_ASC',
            'name_DESC',
        ];
        return !empty($validate[$keyword]) ? $validate[$keyword] : 'created_DESC';
    }

    /**
     * admin/product_listページ用のソート
     *
     * @param [string] $keyword  並び替え用のワード
     * @return string
     */
    public function sortProductAdmin($keyword)
    {
        $order = substr(strrchr($keyword, '_'), 1);
        $column = str_replace('_' . $order, '', $keyword);
        $vali = ['id', 'name', 'price', 'created_at', 'updated_at'];
        $sort = ['ASC', 'DESC'];
        if (in_array($column, $vali) && in_array($order, $sort)) {
            return
                ' ORDER BY '
                . $column
                . ' IS NULL ASC , '
                . $column
                . ' = \'\' ASC , '
                . $column
                . ' '
                . $order
                . ' , '
                . ' id DESC ';
        } else {
            return ' ORDER BY id DESC ';
        }
    }

    //商品件数の取得
    public function getCountProducts($name = null, $price1 = null, $price2 = null, $category = null)
    {
        try {
            //データベースに接続
            parent::connect();
            $where = [];
            $param = [];
            $sql =
                ' SELECT '
                . ' count(pr.id) as count'
                . ' FROM '
                . ' products pr'
                . ' WHERE '
                . ' pr.delete_flg = 0 ';
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
            if (!empty($category)) {
                $where[] = ' pr.category = ? ';
                $param[] = ['value' => $category, 'param' => PDO::PARAM_INT];
            }
            if (!empty($where)) {
                $sql .= ' AND ' . implode(' AND ', $where);
            }
            $stmt = $this->dbh->prepare($sql);
            for ($i = 1; $i <= count($param); $i++) {
                $stmt->bindValue($i, $param[$i - 1]['value'], $param[$i - 1]['param']);
            }
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    //商品の検索
    public function searchProduc($name, $price1, $price2, $category, $sub_category, $column, $order, $page = 0, $limit = 10)
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
                . ' pr.price , '
                . ' pr.img1 , '
                . ' pr.qty , '
                . ' pr.point , '
                . ' pr.public_status , '
                . ' pr.sales_status , '
                . ' DATE_FORMAT(pr.created_at, \'%Y-%m-%d %k:%i:%s\') AS created_at , '
                . ' DATE_FORMAT(pr.updated_at, \'%Y-%m-%d %k:%i:%s\') AS updated_at '
                . ' FROM '
                . ' products pr '
                . ' WHERE '
                . ' delete_flg = 0 ';
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
            if (!empty($category)) {
                $where[] = ' pr.category = ? ';
                $param[] = ['value' => $category, 'param' => PDO::PARAM_INT];
            }
            if (!empty($where)) {
                $sql .= ' AND ' . implode(' AND ', $where);
            }
            $sql .=
                ' GROUP BY '
                . ' pr.id DESC';
            $sql .= $this->sortProductAdmin($column, $order);
            $sql .= ' LIMIT :page , :limit ';
            $stmt = $this->dbh->prepare($sql);
            for ($i = 1; $i <= count($param); $i++) {
                $stmt->bindValue($i, $param[$i - 1]['value'], $param[$i - 1]['param']);
            }
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':page', $page, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            // return false;
            var_dump($e);
        }
    }

    //商品の検索
    public function searchProduct($name, $price1, $price2, $category, $sub_category, $keyword, $page = 0, $limit = 10)
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
                . ' pr.price , '
                . ' pr.img1 , '
                . ' pr.qty , '
                . ' pr.point , '
                . ' pr.public_status , '
                . ' pr.sales_status , '
                . ' DATE_FORMAT(pr.created_at, \'%Y-%m-%d %k:%i:%s\') AS created_at , '
                . ' DATE_FORMAT(pr.updated_at, \'%Y-%m-%d %k:%i:%s\') AS updated_at '
                . ' FROM '
                . ' products pr '
                . ' WHERE '
                . ' delete_flg = 0 ';
            if (!empty($name)) {
                $where[] = ' pr.name LIKE :name ';
                $param[] = ['prepare' => ':name', 'value' => '%' . $name . '%', 'param' => PDO::PARAM_STR];
            }
            if (!empty($price1)) {
                $where[] = ' pr.price >= :price1 ';
                $param[] = ['prepare' => ':price1', 'value' => $price1, 'param' => PDO::PARAM_INT];
            }
            if (!empty($price2)) {
                $where[] = ' pr.price <= :price2 ';
                $param[] = ['prepare' => ':price2', 'value' => $price2, 'param' => PDO::PARAM_INT];
            }
            if (!empty($category)) {
                $where[] = ' pr.category = :category ';
                $param[] = ['prepare' => ':category', 'value' => $category, 'param' => PDO::PARAM_INT];
            }
            if (!empty($where)) {
                $sql .= ' AND ' . implode(' AND ', $where);
            }
            $sql .=
                ' GROUP BY '
                . ' pr.id DESC';
            $sql .= $this->sortProductAdmin($keyword);
            $sql .= ' LIMIT :page , :limit ';
            $stmt = $this->dbh->prepare($sql);
            for ($i = 1; $i <= count($param); $i++) {
                $stmt->bindValue($param[$i - 1]['prepare'], $param[$i - 1]['value'], $param[$i - 1]['param']);
            }
            $stmt->bindValue(':page', $page, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            // return $sql;
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            // return false;
            var_dump($e);
        }
    }


    /**
     * 商品単体の取得
     *
     * @param [int] $id
     * @return array|false
     */
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
                . ' delete_flg = 0 ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * カテゴリーの取得
     *
     * @return array|false
     */
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
                . ' cate.delete_flg = 0 ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * 商品販売ステータスの取得
     *
     * @return void
     */
    public function getSalesStatusList()
    {
        try {
            //データベースに接続
            parent::connect();
            $sql =
                ' SELECT '
                . ' sst.* , '
                . ' sst.id '
                . ' FROM '
                . ' sales_status sst';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
        } catch (PDOException $e) {
            return false;
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
                . ' pp.product_id = ? ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$product_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return false;
        }
    }


    //商品編集
    public function updateProduct($id, $post, $img)
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
                . ' price = ? , '
                . ' qty = ? , '
                . ' point =  ? , '
                . ' shipping =  ? , '
                . ' public_status = ? , '
                . ' sales_status = ? , '
                . ' category = ? , '
                . ' title = ? , '
                . ' body = ? , '
                . ' updated_at = NOW(6) '
                . ' WHERE '
                . ' id = ? ';
            //トランザクション開始
            $this->dbh->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $this->dbh->beginTransaction();
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([
                $post['name'],
                $post['price'],
                $post['qty'],
                $post['point'],
                $post['shipping'],
                $post['public_status'],
                $post['sales_status'],
                $post['category'],
                $post['title'],
                $post['body'],
                $id
            ]);
            // 画像の削除と登録
            // for ($i = 1; $i <= 5; $i++) {
            //     if ($this->getProductImage($id, $img['img' . $i]) !== null) {
            //         $this->deleteProductImage($id, 'img' . $i);
            //     }
            //     // $this->uploadProductImage1($img['img' . $i], $id);
            // }
            if ($this->getProductImage($id, $img['img1']) === true) {
                $this->deleteProductImage($id, 'img1');
            }
            if ($this->getProductImage($id, $img['img2']) === true) {
                $this->deleteProductImage($id, 'img2');
            }
            if ($this->getProductImage($id, $img['img3']) === true) {
                $this->deleteProductImage($id, 'img3');
            }
            if ($this->getProductImage($id, $img['img4']) === true) {
                $this->deleteProductImage($id, 'img4');
            }
            if ($this->getProductImage($id, $img['img5']) === true) {
                $this->deleteProductImage($id, 'img5');
            }
            $this->uploadProductImage1($img['img1'], $id);
            $this->uploadProductImage2($img['img2'], $id);
            $this->uploadProductImage3($img['img3'], $id);
            $this->uploadProductImage4($img['img4'], $id);
            $this->uploadProductImage5($img['img5'], $id);
            $this->dbh->commit();
            return true;
        } catch (PDOException $e) {
            $this->dbh->rollback();
            var_dump($e);
        }
    }

    //商品新規登録
    function createProduct($post, $img)
    {
        try {
            //データベースに接続
            parent::connect();
            //商品のデータを新規登録
            $sql =
                ' INSERT INTO products ( '
                . ' name , '
                . ' price , '
                . ' qty , '
                . ' point , '
                . ' shipping , '
                . ' public_status , '
                . ' sales_status , '
                . ' category , '
                . ' title , '
                . ' body  '
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
                . ' ?  '
                . ' ) ';
            //トランザクション開始
            $this->dbh->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            $this->dbh->beginTransaction();
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([
                $post['name'],
                $post['price'],
                $post['qty'],
                $post['point'],
                $post['shipping'],
                $post['public_status'],
                $post['sales_status'],
                $post['category'],
                $post['title'],
                $post['body']
            ]);
            $insertId = $this->dbh->lastInsertId();
            //画像を登録
            if (!empty($img['img1'])) {
                $this->uploadProductImage1($img['img1'], $insertId);
            }
            if (!empty($img['img2'])) {
                $this->uploadProductImage2($img['img2'], $insertId);
            }
            if (!empty($img['img3'])) {
                $this->uploadProductImage3($img['img3'], $insertId);
            }
            if (!empty($img['img4'])) {
                $this->uploadProductImage4($img['img4'], $insertId);
            }
            if (!empty($img['img5'])) {
                $this->uploadProductImage5($img['img5'], $insertId);
            }
            $this->dbh->commit();
            return true;
        } catch (PDOException $e) {
            $this->dbh->rollback();
            var_dump($e);

        }
    }


    /**
     * 画像の拡張子チェック
     *
     * @param [string] $filename
     * @return bool
     */
    function checkExt($filename)
    {
        $check = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, $check);
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
                . ' id = ? ';
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


    public function getProductImage($id, $img)
    {
        if ($img['error'] == UPLOAD_ERR_NO_FILE) {
            return;
        }
        try {
            //データベースに接続
            parent::connect();
            $check = ['img1', 'img2', 'img3', 'img4', 'img5'];
            //値が予期せぬ場合はリターン
            if (!in_array($img['name'], $check)) {
                return false;
            }
            $sql =
                ' SELECT '
                    . $img
                . ' FROM '
                    . ' products '
                . ' WHERE '
                    . 'id = ? '
            ;
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$id]);
            $image_path = $stmt->fetch();
            if ($image_path[0] == null) {
                return false;
            }
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }


    /**
     * 商品の画像削除 DB & productフォルダ
     *
     * @param [int] $id
     * @param [string] $img
     * @return bool
     */
    public function deleteProductImage($id, $img)
    {
        try {
            //データベースに接続
            parent::connect();
            $check = ['img1', 'img2', 'img3', 'img4', 'img5'];
            //値が予期せぬ場合はリターン
            if (!in_array($img, $check)) {
                return;
            }
            //DBから画像のパスの取得
            $sql =
                ' SELECT '
                    . $img
                . ' FROM '
                    . ' products '
                . ' WHERE '
                    . 'id = ? '
            ;
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$id]);
            $img_path = $stmt->fetch();
            if ($img_path[0] == null) {
                return;
            }
            $sql =
                ' UPDATE '
                    . ' products '
                . ' SET '
                    . $img
                . ' = null '
                . ' WHERE '
                    . ' id = ? ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$id]);
            unlink(ABSOLUTE_PRODUCT_IMAGE_PATH . $img_path[$img]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * 商品DBに画像1を登録
     *
     * @param [files] $img
     * @param [int] $id
     * @return bool
     */
    function uploadProductImage1($img, $id)
    {
        if ($img['error'] == UPLOAD_ERR_NO_FILE || !$this->checkExt($img['name'])) {
            return;
        }
        try {
            if ($img['error'] == UPLOAD_ERR_OK) {
                $image_path = mb_convert_encoding(date('YmdGis') . '_' . $img['name'], 'utf8', 'cp932');
                $sql =
                    ' UPDATE '
                    . ' products '
                    . ' SET '
                    . ' img1 = ? , '
                    . ' updated_at = NOW(6) '
                    . ' WHERE '
                    . ' id = ? ';
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute([$image_path, $id]);
                //権限変更
                exec('sudo chmod 0777 ' . ABSOLUTE_PRODUCT_IMAGE_PATH);
                if (move_uploaded_file($img['tmp_name'], ABSOLUTE_PRODUCT_IMAGE_PATH . $image_path)) {
                    //成功時に権限を戻して空値を返す
                    exec('sudo chmod 0755 ' . ABSOLUTE_PRODUCT_IMAGE_PATH);
                    return true;
                }
                exec('sudo chmod 0755 ' . ABSOLUTE_PRODUCT_IMAGE_PATH);
                return false;
            }
            return false;
        } catch (PDOException $e) {
            exec('sudo chmod 0755 ' . ABSOLUTE_PRODUCT_IMAGE_PATH);
            return false;
        }
    }
    /**
     * 商品DBに画像2を登録
     *
     * @param [files] $img
     * @param [int] $id
     * @return bool
     */
    function uploadProductImage2($img, $id)
    {
        if ($img['error'] == UPLOAD_ERR_NO_FILE || !$this->checkExt($img['name'])) {
            return;
        }
        try {
            if ($img['error'] == UPLOAD_ERR_OK) {
                $image_path = mb_convert_encoding(date('YmdGis') . '_' . $img['name'], 'utf8', 'cp932');
                $sql =
                    ' UPDATE '
                    . ' products '
                    . ' SET '
                    . ' img2 = ? , '
                    . ' updated_at = NOW(6) '
                    . ' WHERE '
                    . ' id = ? ';
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute([$image_path, $id]);
                //権限変更
                exec('sudo chmod 0777 ' . ABSOLUTE_PRODUCT_IMAGE_PATH);
                if (move_uploaded_file($img['tmp_name'], ABSOLUTE_PRODUCT_IMAGE_PATH . $image_path)) {
                    //成功時に権限を戻して空値を返す
                    exec('sudo chmod 0755 ' . ABSOLUTE_PRODUCT_IMAGE_PATH);
                    return true;
                }
                //画像が一時フォルダから移動出来なかった場合はロールバックし権限を戻す。
                exec('sudo chmod 0755 ' . ABSOLUTE_PRODUCT_IMAGE_PATH);
                return false;
            }
            return false;
        } catch (PDOException $e) {
            exec('sudo chmod 0755 ' . ABSOLUTE_PRODUCT_IMAGE_PATH);
            return false;
        }
    }
    /**
     * 商品DBに画像3を登録
     *
     * @param [files] $img
     * @param [int] $id
     * @return bool
     */
    function uploadProductImage3($img, $id)
    {
        if ($img['error'] == UPLOAD_ERR_NO_FILE || !$this->checkExt($img['name'])) {
            return;
        }
        try {
            if ($img['error'] == UPLOAD_ERR_OK) {
                $image_path = mb_convert_encoding(date('YmdGis') . '_' . $img['name'], 'utf8', 'cp932');
                $sql =
                    ' UPDATE '
                    . ' products '
                    . ' SET '
                    . ' img3 = ? , '
                    . ' updated_at = NOW(6) '
                    . ' WHERE '
                    . ' id = ? '
                ;
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute([$image_path, $id]);
                //権限変更
                exec('sudo chmod 0777 ' . ABSOLUTE_PRODUCT_IMAGE_PATH);
                if (move_uploaded_file($img['tmp_name'], ABSOLUTE_PRODUCT_IMAGE_PATH . $image_path)) {
                    //成功時に権限を戻して空値を返す
                    exec('sudo chmod 0755 ' . ABSOLUTE_PRODUCT_IMAGE_PATH);
                    return true;
                }
                //画像が一時フォルダから移動出来なかった場合はロールバックし権限を戻す。
                exec('sudo chmod 0755 ' . ABSOLUTE_PRODUCT_IMAGE_PATH);
                return false;
            }
            return false;
        } catch (PDOException $e) {
            exec('sudo chmod 0755 ' . ABSOLUTE_PRODUCT_IMAGE_PATH);
            return false;
        }
    }

    /**
     * 商品DBに画像4を登録
     *
     * @param [files] $img
     * @param [int] $id
     * @return bool
     */
    function uploadProductImage4($img, $id)
    {
        if ($img['error'] == UPLOAD_ERR_NO_FILE || !$this->checkExt($img['name'])) {
            return;
        }
        try {
            if ($img['error'] == UPLOAD_ERR_OK) {
                $image_path = mb_convert_encoding(date('YmdGis') . '_' . $img['name'], 'utf8', 'cp932');
                $sql =
                    ' UPDATE '
                    . ' products '
                    . ' SET '
                    . ' img4 = ? , '
                    . ' updated_at = NOW(6) '
                    . ' WHERE '
                    . ' id = ? ';
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute([$image_path, $id]);
                //権限変更
                exec('sudo chmod 0777 ' . ABSOLUTE_PRODUCT_IMAGE_PATH);
                if (move_uploaded_file($img['tmp_name'], ABSOLUTE_PRODUCT_IMAGE_PATH . $image_path)) {
                    //成功時に権限を戻して空値を返す
                    exec('sudo chmod 0755 ' . ABSOLUTE_PRODUCT_IMAGE_PATH);
                    return true;
                }
                //画像が一時フォルダから移動出来なかった場合はロールバックし権限を戻す。
                exec('sudo chmod 0755 ' . ABSOLUTE_PRODUCT_IMAGE_PATH);
                return false;
            }
            return false;
        } catch (PDOException $e) {
            exec('sudo chmod 0755 ' . ABSOLUTE_PRODUCT_IMAGE_PATH);
            return false;
        }
    }
    /**
     * 商品DBに画像5を登録
     *
     * @param [files] $img
     * @param [int] $id
     * @return bool
     */
    function uploadProductImage5($img, $id)
    {
        if ($img['error'] == UPLOAD_ERR_NO_FILE || !$this->checkExt($img['name'])) {
            return;
        }
        try {
            if ($img['error'] == UPLOAD_ERR_OK) {
                $image_path = mb_convert_encoding(date('YmdGis') . '_' . $img['name'], 'utf8', 'cp932');
                $sql =
                    ' UPDATE '
                    . ' products '
                    . ' SET '
                    . ' img5 = ? , '
                    . ' updated_at = NOW(6) '
                    . ' WHERE '
                    . ' id = ? ';
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute([$image_path, $id]);
                //権限変更
                exec('sudo chmod 0777 ' . ABSOLUTE_PRODUCT_IMAGE_PATH);
                if (move_uploaded_file($img['tmp_name'], ABSOLUTE_PRODUCT_IMAGE_PATH . $image_path)) {
                    //成功時に権限を戻して空値を返す
                    exec('sudo chmod 0755 ' . ABSOLUTE_PRODUCT_IMAGE_PATH);
                    return true;
                }
                //画像が一時フォルダから移動出来なかった場合はロールバックし権限を戻す。
                exec('sudo chmod 0755 ' . ABSOLUTE_PRODUCT_IMAGE_PATH);
                return false;
            }
            return false;
        } catch (PDOException $e) {
            exec('sudo chmod 0755 ' . ABSOLUTE_PRODUCT_IMAGE_PATH);
            return false;
        }
    }


}
