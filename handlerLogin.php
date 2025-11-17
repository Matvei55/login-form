<?php
require_once 'database.php';
//
//try {
//    $pdo = getPDO();
//    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//
//    if ($_POST['username'] && $_POST['password']) {
//        $username = trim($_POST['username']);
//        $password = $_POST['password'];
//
//        // Ищем пользователя в базе
//        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
//        $stmt->execute([$username]);
//        $user = $stmt->fetch();
//
//        if ($user) {
//            // Проверяем пароль
//            if (password_verify($password, $user['password'])) {
//                // Успешный вход
//                $_SESSION['user_id'] = $user['id'];
//                $_SESSION['username'] = $user['username'];
//                $_SESSION['success'] = "Вход выполнен, добро пожаловать '$username'!";
//            } else {
//                $_SESSION['error'] = ["Неверный пароль"];
//            }
//        } else {
//            $_SESSION['error'] = ["Пользователь '$username' не найден"];
//        }
//    } else {
//        $_SESSION['error'] = ['Заполните все поля'];
//    }
//} catch (PDOException $e) {
//    $_SESSION['error'] = ["Ошибка базы данных: " . $e->getMessage()];
//}
class handlerLogin{
    private $db;
    public function __construct(){
        $this->db = new Database();
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
$handlerLogin = new handlerLogin();
$handlerLogin->processLogin();