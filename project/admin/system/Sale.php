<?php
class Sale extends Model
{
    //1ヶ月の日別売上データの取得
    public function getSalesManagementOneMonth($month)
    {
        try {
            //データベースに接続
            parent::connect();
            //指定なしor今月の場合は今月1日から当日までの取得
            if (empty($month) || $month == date('Y-m', strtotime('today'))) {
                $beginning_day = date('Y-m-01', strtotime('today'));
                $end_day = date('Y-m-d', strtotime('today'));
            } else {
                $beginning_day = $month . '-01';
                $end_day = date('Y-m-t', strtotime($month));
            }
            $sql =
                ' SELECT '
                    . ' cl.date , '
                    . ' GROUP_CONCAT(distinct od.name separator ",") AS product_name , '
                    . ' SUM(od.num) AS total_num , '
                    . ' SUM(odr.shipping_price) AS total_shipping_price , '
                    . ' SUM(odr.total_price) AS total_price '
                . ' FROM '
                    . ' calendar cl '
                . ' LEFT JOIN ( '
                    . ' SELECT '
                        . ' * '
                    . ' FROM '
                        . ' `order` '
                    . ' WHERE '
                        . ' delete_flg = 0 '
                . ' ) AS odr '
                . ' ON '
                    . ' odr.created_at LIKE CONCAT(cl.date , "%") '
                . ' LEFT JOIN '
                    . ' order_detail od '
                . ' ON '
                    . ' odr.id = od.order_id '
                . ' WHERE '
                    . ' cl.date BETWEEN ? AND ? '
                . ' GROUP BY '
                    . ' cl.date '
                . ' ORDER BY '
                    . ' cl.date DESC '
            ;
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$beginning_day, $end_day]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    //売上データをcsvファイルに変換
    public function dlSalesManagementList($sales_list, $month)
    {
        if (empty($sales_list)) {
            return '※選択した月のデータはDBに登録されていません';
        }
        //月が選択されていなかった場合は今月をファイル名に
        $month = !empty($month) ? $month : date('Y-m', strtotime('today'));
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $month . '.csv');
        header('Content-Transfer-Encoding: binary');
        //1行目の作成
        $csv = '日付, 商品名, 個数, 送料, 総額' . "\n";
        //2行目以降のデータを入力
        foreach ($sales_list as $value) {
            $csv .=
                '"'
                . $value['date']
                . '","'
                . $value['product_name']
                . '","'
                . number_format($value['total_num'])
                . '","'
                . number_format($value['total_shipping_price'])
                . '","'
                . number_format($value['total_price'])
                . '"'
                . "\n"
            ;
        }
        echo $csv;
        exit();
    }
}
