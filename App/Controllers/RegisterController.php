<?php
namespace App\Controllers;

use App\Core\Application;
use App\Core\Controller;
use App\Core\Session;
use App\Core\View;
use App\Events\UserRegisteredEvent;
use App\Models\Users;
use App\Core\Request;
use App\Container\ContainerInterface;

class RegisterController extends Controller
{
    public function __construct(Request $request, View $view, Session $session,private Users $userModel)
    {
        parent::__construct($request, $view, $session);
    }

    public function index(Request $request): void
    {

        $data = [
            'errors' => $this->getErrors(),
            'success' => $this->getSuccess(),
        ];

        echo $this->render('register', $data);
        $this->clearSession();
    }

    public function store(Request $request): void
    {
        $username = trim($request->postParam('username', ''));
        $password = trim($request->postParam('password', ''));

        if (empty($username)) {
            $this->setError('Имя пользователя обязательно');
        } elseif (mb_strlen($username) < 3) {
            $this->setError('Имя пользователя должно быть минимум 3 символа');
        } elseif (mb_strlen($username) > 67) {
            $this->setError('Имя пользователя не должно быть больше 67 символов');
        }

        if (empty($password)) {
            $this->setError('Пароль обязателен');
        } elseif (mb_strlen($password) < 6) {
            $this->setError('Пароль должен быть минимум 6 символов');
        }

        if ($this->hasErrors()) {
            $this->redirect('/register');
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $userId = $this->userModel->setData([
            'name' => $username,
            'password' => $hashedPassword,
        ])->save();

        if ($userId) {
            $event = new UserRegisteredEvent($this->userModel);
            Application::getInstance()->getDispatcher()->dispatch($event);

            $this->session->setUser($userId);
            $this->setSuccess("Добро пожаловать, {$username}!");
            $this->redirect('/posts');
        }
    }
}
