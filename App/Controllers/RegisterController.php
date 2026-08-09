<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\EventDispatcherInterface;
use App\Core\Session;
use App\Core\View;
use App\Core\Request;
use App\Services\UserService;
use App\DTO\RegisterUserDTO;


class RegisterController extends Controller
{
    public function __construct(
        Request $request,
        View $view,
        Session $session,
        EventDispatcherInterface $dispatcher,
        private UserService $userService,
    ){
        parent::__construct($request, $view, $session, $dispatcher);
    }

    public function index(Request $request):void
    {
        if($this->session->has('user_id')){
            $this->redirect('/posts');
            return;
        }
        $data = [
            'errors' => $this->getErrors(),
            'success' => $this->getSuccess(),
        ];
        echo $this->render('register', $data);
        $this->clearSession();
    }

    public function store(Request $request):void
    {
        try {
            $dto = new RegisterUserDTO(
                trim($request->postParam('username', '')),
                trim($request->postParam('password', '')),
            );
            $user = $this->userService->register($dto);
            if($user){
                $this->session->setUser($user['id']);
                $this->setSuccess("добро пожаловать, {$user['name']}");
                $this->redirect('/posts');
                return;
            }
        }catch (\InvalidArgumentException $e){
            $this->setError($e->getMessage());
        }catch (\Exception $e){
            $this->setError('произошла ошибка');
        }
        $this->redirect('/register');
    }
}
