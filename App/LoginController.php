<?php
namespace App;
use App\Models\Users;
//$username = trim($_POST['username'] ?? '');
//$password = $_POST['password'] ?? '';
//$errors = [];
//
//if (empty($username) || empty($password)) {
//    $errors[]='заполните все поля';
//}else{
//    $userModel = new Users();
//    $user = $userModel->findByName($username);
//
//    if ($user && password_verify($password, $user['password'])) {
//        $_SESSION['user_id'] = $user['id'];
//        $_SESSION['success'] = "добро подаловать , {$username}";
//        header('Location: /index.php?page=posts');
//        exit();
//    }else{
//        $errors[] = 'неправильное имя пользователя и пароль';
//    }
//}
//$_SESSION['errors'] = $errors;
//header('Location: /index.php?page=login');
//exit();
class LoginController
{
    private Users $userModel;

    public function __construct()
    {
        $this->userModel = new Users();
    }

    public function login(array $postData): void
    {
        $username = trim($postData['username'] ?? '');
        $password = $postData['password'] ?? '';
        $errors = [];
        if (empty($username) || empty($password)) {
            $errors[] = 'заполните все поля';
        }else {
            $user = $this->userModel->findByName($username);
        }

        if($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['success'] = "Добро пожаловать, {$username}!";
            $this->redirect('/index.php?page=posts');
            return;
        }else{
            $errors[] = 'Неправильное имя пользователя или пароль';
            $_SESSION['errors'] = $errors;
            $this->redirect('/index.php?page=login');
        }
    }

    private function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }
}