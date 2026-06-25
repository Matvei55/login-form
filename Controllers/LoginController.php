<?php
namespace Controllers;

use App\Models\Users;
use Core\Request;
use Core\View;

class LoginController
{
    private Users $userModel;
    private View $view;

    public function __construct()
    {
        $this->userModel = new Users();
        $this->view = new View();
    }

    public function index(Request $request): void
    {
        $data = [
            'errors' => $_SESSION['errors'] ?? [],
            'success' => $_SESSION['success'] ?? '',
        ];

        echo $this->view->render('login', $data);
        unset($_SESSION['errors'], $_SESSION['success']);
    }

    public function store(Request $request): void
    {
        $username = trim($request->post()->getString('username', ''));
        $password = trim($request->post()->get('password', ''));
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

    public function logout(Request $request): void
    {
        session_destroy();
        header('Location: /login');
        exit();
    }
}