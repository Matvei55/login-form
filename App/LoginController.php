<?php
namespace App;

use App\Models\Users;

class LoginController
{
    private Users $userModel;
    private View $view;

    public function __construct()
    {
        $this->userModel = new Users();
        $this->view = new View();
    }

    public function showLogin(): void
    {
        $data = [
            'errors' => $_SESSION['errors'] ?? [],
            'success' => $_SESSION['success'] ?? [],
        ];

        echo $this->view->render('login', $data);
        unset($_SESSION['errors'], $_SESSION['success']);
    }

    public function login(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $errors = [];

        if (empty($username) || empty($password)) {
            $errors[] = 'заполните все поля';
        }else{
            $user = $this->userModel->findByName($username);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['success'] = "добро пожаловать {$username}";
                header('Location: /posts');
                exit();
            }else{
                $errors[] = 'неправильное имя или пароль';
            }
        }
        $_SESSION['errors'] = $errors;
        header('Location: /login');
        exit();
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: /login');
        exit();
    }
}