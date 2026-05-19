<?php
use App\Models\Users;
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$errors = [];

if (empty($username) || empty($password)) {
    $errors[]='заполните все поля';
}else{
    $userModel = new Users();
    $user = $userModel->findByName($username);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['success'] = "добро подаловать , {$username}";
        header('Location: /index.php?page=posts');
        exit();
    }else{
        $errors[] = 'неправильное имя пользователя и пароль';
    }
}
$_SESSION['errors'] = $errors;
header('Location: /index.php?page=login');
exit();
