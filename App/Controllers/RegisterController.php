<?php
//namespace App\Controllers;
//
//use App\Core\Request;
//use App\Core\Controller;
//use App\Models\Users;
//
//class RegisterController extends Controller
//{
//    private Users $userModel;
//
//    public function __construct()
//    {
//        parent::__construct();
//        $this->userModel = new Users();
//    }
//
//    public function index(Request $request): void
//    {
//        if($this->session->has('user_id')){
//            $this->redirect('/posts');
//            return;
//        }
//        $data = [
//            'errors' => $this->getErrors(),
//            'success' => $this->getSuccess(),
//        ];
//
//        echo $this->render('register', $data);
//        $this->clearSession();
//    }
//
//    public function store(Request $request): void
//    {
//        $username = trim($request->postParam('username', ''));
//        $password = trim($request->postParam('password', ''));
//        if (empty($username)) {
//            $this->setError("имя пользователя обязательно");
//        }elseif (mb_strlen($username) <3) {
//            $this->setError("имя пользователя должно быть минимум 3 символа");
//        }elseif (mb_strlen($username) >67) {
//            $this->setError('имя пользователя имя пользователя не должно быть больше 67 символов"');
//        }
//        if (empty($password)) {
//            $this->setError('пароль обязателен');
//        }elseif (mb_strlen($password) < 2) {
//            $this->setError('пароль должен быть минимум 2 символа');
//        }
//        if($this->hasErrors()){
//            $this->redirect('/register');
//            return;
//        }
//
//            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
//            $userId = $this->userModel->setData([
//                'name' => $username,
//                'password' => $hashedPassword,
//            ])->save();
//        if($userId) {
//            $this->session->setUser($userId);
//            $this->setSuccess("добро пожаловать, {$username}");
//            $this->redirect('/posts');
//        }else {
//            $this->setError('пользователь с таким именем уже существует');
//        }
//    }
//}
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Users;
use App\Core\Request;

class RegisterController extends Controller
{
    private Users $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new Users();
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
            $this->session->setUser($userId);
            $this->setSuccess("Добро пожаловать, {$username}!");
            $this->redirect('/posts');
        } else {
            $this->setError('Пользователь с таким именем уже существует');
            $this->redirect('/register');
        }
    }
}
