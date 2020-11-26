<?php
class User extends Model
{
    //ユーザーログイン認証
    public function loginUser($login_id, $pass)
    {
        try {
            //データベースに接続
            parent::connect();
            $sql =
                ' SELECT '
                    . ' id , '
                    . ' login_pass , '
                    . ' name '
                . ' FROM '
                    . ' user '
                . ' WHERE '
                    . ' login_id = ? '
                . ' AND '
                    . ' status = 1 '
            ;
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$login_id]);
            $user = $stmt->fetch();
            if ($user == false) {
                return '※そのようなIDは存在しません';
            }
            if ($user['login_pass'] != $pass) {
                return '※パスワードが正しくありません';
            }
            //ログイン日時をDBに登録
            $sql =
                ' UPDATE '
                    . ' user '
                . ' SET '
                    . ' last_login_date = NOW(6) '
                . ' WHERE '
                    . ' id = ? '
            ;
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$_SESSION['user_id']]);
            session_regenerate_id();
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['userAuthenticated'] = 1;
            return true;
        } catch (PDOException $e) {
            return 'システムエラーが発生しました。';
        }
    }

    //ユーザー情報の取得
    public function getUserInfo($user_id)
    {
        try {
            //データベースに接続
            parent::connect();
            $sql =
                ' SELECT '
                    . ' * '
                . ' FROM '
                    . ' user '
                . ' WHERE '
                    . ' id = ? '
            ;
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$user_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
}
