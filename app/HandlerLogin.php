<?php
namespace app;

use PDOException;
use app\Database;
use app\QueryBuilder;

class HandlerLogin {
    private $qb;

    public function __construct() {
        $this->qb = new QueryBuilder();
    }

    public function processLogin() {
        if($_POST['username'] && $_POST['password']){
            $username = trim($_POST['username']);
            $password = $_POST['password'];

            try {
                $user = $this->qb->table('users')
                                ->where('username', $username)
                                ->first();

                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['success'] = "вход выполнен в аккаунт '$username'";
                } else {
                    $_SESSION['error'] = ["Неверный логин или пароль"];
                }
            } catch (PDOException $e) {
                $_SESSION['error'] = ["ошибка базы данных"];
            }
        }
    }
}

$handlerLogin = new HandlerLogin();
$handlerLogin->processLogin();
$handlerLogin->processLogin();