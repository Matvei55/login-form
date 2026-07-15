<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Users;
use App\Core\Request;
use App\Core\View;
use App\Core\Session;

class LoginController extends Controller
{
    private Users $userModel;

    public function __construct(
        Request $request,
        View $view,
        Session $session,
        Users $userModel
    ){
        parent::__construct($request,$view,$session);
    }

    protected function getMiddlewareConfig(): array
    {
        return [
            'index' => [GuestMiddleware::class],
            'store' => [GuestMiddleware::class],
        ];
    }

    public function index(Request $request): void
    {
        $data = [
            'errors' => $this->getErrors(),
            'success' => $this->getSuccess(),
        ];

        echo $this->render('login', $data);
        $this->clearSession();
    }

    public function store(Request $request): void
    {
        $username = trim($request->postParam('username', ''));
        $password = trim($request->postParam('password', ''));

        if (empty($username)) {
            $this->setError('Имя пользователя обязательно');
        }

        if (empty($password)) {
            $this->setError('Пароль обязателен');
        }

        if ($this->hasErrors()) {
            $this->redirect('/login');
            return;
        }

        $user = $this->userModel->findByName($username);

        if ($user && password_verify($password, $user['password'])) {
            $this->session->setUser($user['id']);
            $this->setSuccess("Добро пожаловать, {$username}!");
            $this->redirect('/posts');
        } else {
            $this->setError('Неправильное имя пользователя или пароль');
            $this->redirect('/login');
        }
    }
        public function logout(Request $request): void
    {
        $this->session->logout();
        $this->setSuccess('Вы вышли из системы');
        $this->redirect('/login');
    }
}
