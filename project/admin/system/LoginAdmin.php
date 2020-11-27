<?php
//ログイン時の認証を行うクラス
class LoginAdmin extends Model
{
    public function login($id, $pass)
    {
        try {
            //データベースに接続
            parent::connect();
            $sql =
                    ' SELECT '
                        . ' login_id, '
                        . ' password, '
                        . ' name '
                    . ' FROM '
                        . '  admins '
                    . ' WHERE '
                        . '  login_id = ? '
                    . ' AND '
                        . ' delete_flg = 0 '
                    ;
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute([$id]);
            $admin = $stmt->fetch();
        } catch (PDOException $e) {
            return 'システムエラーが発生しました 管理者に連絡をお願いします';
        }
        if ($admin == false) {
            return 'そのようなIDは存在しません';
        }
        if (!password_verify($pass, $admin['password'])) {
            return 'パスワードが正しくありません';
        }
        session_regenerate_id();
        $_SESSION['name'] = $admin['name'];
        $_SESSION['authenticated'] = 1;
        header('Location: top.php');
        exit;
    }
}
