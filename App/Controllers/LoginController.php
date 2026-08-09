<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\EventDispatcherInterface;
use App\Core\Request;
use App\Core\Session;
use App\Core\View;
use App\Services\UserService;
use App\DTO\LoginUserDTO;
class LoginController extends Controller
{

    public function __construct(
        Request $request,
        View $view,
        Session $session,
        EventDispatcherInterface $dispatcher,
        private UserService $userService
    ){
        parent::__construct($request, $view, $session,$dispatcher);
    }

    public function index(Request $request): void
    {
        if($this->session->has('user_id')){
            $this->redirect('/posts');
            return;
        }
        $data = [
            'errors' => $this->getErrors(),
            'success' => $this->getSuccess(),
        ];

        echo $this->render('login', $data);
        $this->clearSession();
    }

    public function store(Request $request): void
    {
        try {
            $dto = new LoginUserDTO(
                trim($request->postParam('username', '')),
                trim($request->postParam('password', ''))
            );
            $user = $this->userService->login($dto);
            if($user){
                $this->session->setUser($user['id']);
                $this->setSuccess("добро пожаловать, {$user['name']}!");
                $this->redirect('/posts');
                return;
            }
            $this->setError('неправильное имя пользователя или пароль');
        }catch (\InvalidArgumentException $e) {
            $this->setError($e->getMessage());
        }
        $this->redirect('/login');
    }
}
