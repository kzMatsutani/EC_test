<?php
require_once('LoginAdmin.php');
require_once('Product.php');
require_once('User.php');
require_once('Order.php');
require_once('Sale.php');

//データベース接続
class Model
{
    protected $dbh;
    public function connect()
    {
        if (!empty($this->dbh)) {
            return;
        }
        try {
            $this->dbh = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME, DBUSER, DBPASS,);
            $this->dbh->exec('set names utf8');
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }
}