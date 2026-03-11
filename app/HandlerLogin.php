<?php

namespace Ren;
use PDOException;
use Ren\Database;
class HandlerLogin{
    private $db;
    public function __construct(){
        $this->db = Database::getInstance();
    }

    public function processLogin(){
        if($_POST['username'] && $_POST['password']){
            $username = trim($_POST['username']);
            $password = $_POST['password'];

            if (empty($username) || empty($password)) {
                $_SESSION['error'] = ['заполните все поля'];
                return;
            }

            try {
                $stmt = $this->db->query("SELECT * FROM users WHERE username = ?", [$username]);
                $user = $stmt->fetch();

                if ($user) {
                    if (password_verify($password, $user['password'])) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['success'] = "вход выполнен в аккаунт '$username'";
                    }else{
                        $_SESSION['error'] = ["Неверный пароль"];
                    }
                }else{
                    $_SESSION['error'] = ["Пользователь '$username' не найден"];
                }
            }catch (PDOException $e){
                $_SESSION['error'] = ["ошибка базы данных: ".$e->getMessage()];
            }
        }else {
            $_SESSION['error'] = ['заполните все поля'];
        }
    }
}
$handlerLogin = new HandlerLogin();
$handlerLogin->processLogin();