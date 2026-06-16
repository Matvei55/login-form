<?php
namespace App;

use App\Models\Users;
class RegisterController
{
    private Users $userModel;
    private View $view;

    public function __construct()
    {
        $this->userModel = new Users();
        $this->view = new View();
    }

    public function showRegister(Request $request): void
    {
        $data = [
            'errors' => $_SESSION['errors'] ?? [],
            'success' => $_SESSION['success'] ?? '',
        ];

        echo $this->view->render('register', $data);
        unset($_SESSION['errors'], $_SESSION['success']);
    }

    public function register(Request $request): void
    {
        $username = trim($request->post()->getString('username', ''));
        $password = $request->post()->get('password', '');
        $errors = [];
        if (empty($username)) {
            $errors[] = "имя пользователя обязательно";
        }elseif (mb_strlen($username) <3) {
            $errors[] = "имя пользователя должно быть минимум 3 символа";
        }elseif (mb_strlen($username) >67) {
            $errors[] = 'имя пользователя имя пользователя не должно быть больше 67 символов"';
        }
        if (empty($password)) {
            $errors[] = 'пароль обязателен';
        }elseif (mb_strlen($password) < 2) {
            $errors[] = 'пароль должен быть минимум 2 символа';
        }
        if (empty($errors)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $userId = $this->userModel->setData([
                'name' => $username,
                'password' => $hashedPassword,
            ])->save();
        }
        if($userId) {
            $_SESSION['user_id'] = $userId;
            $_SESSION['success'] = "добро пожаловать, {$username}";
            header('Location: /posts');
            exit();
        }else {
            $errors[] = 'пользователь с таким именем уже существует';
        }
        $_SESSION['errors'] = $errors;
        header('Location: /register');
        exit();
    }
}