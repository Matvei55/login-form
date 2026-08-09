<?php
namespace App\Controllers;

use App\Core\Application;
use App\Core\Controller;
use App\Core\EventDispatcher;
use App\Core\EventDispatcherInterface;
use App\Core\Session;
use App\Core\View;
use App\Core\Request;
use App\Events\PostPendingModerationEvent;
use App\Services\PostService;
use App\DTO\CreatePostDTO;
use App\Container\ContainerInterface;

class PostsController extends Controller
{
    public function __construct(
        Request $request,
        View $view,
        Session $session,
        EventDispatcherInterface $dispatcher,
        private PostService $postService,
    ){
        parent::__construct($request, $view, $session,$dispatcher);
    }

    public function index(Request $request): void
    {
        $this->requireAuth();

        $userId = $this->session->getUserId();
        $userPosts = $this->postService->getUserPosts($userId, 'approved');

        $data = [
            'user' => $this->getUser(),
            'userPosts' => $userPosts,
            'errors' => $this->getErrors(),
            'success' => $this->getSuccess(),
        ];
        echo $this->render('posts', $data);
        $this->clearSession();
    }

    public function store(Request $request): void
    {
        $this->requireAuth();
        try{
            $dto = new CreatePostDTO(
                trim($request->postParam('title','')),
                trim($request->postParam('content','')),
                array_filter(array_map('trim',explode(',',$request->postParam('tags','')))),
                $request->postParam('category_id') ? (int)$request->postParam('category_id') : null
            );
            $userId = $this->session->getUserId();
            $postId = $this->postService->create($dto, $userId);

            if ($postId) {
                $post = $this->postService->getPost($postId);
                $user = $this->getUser();
                $event = new PostPendingModerationEvent($post, $user);
                  $app = Application::getInstance();
                  $dispatcher = $app->getDispatcher();
                  $dispatcher->dispatch($event);
                $this->dispatchEvent($event);
                $this->setSuccess('пост успешно создан');
            }else {
                $this->setError('не удалось создать пост');
            }
        }catch (\InvalidArgumentException $e){
            $this->setError($e->getMessage());
        }
        $this->redirect('/posts');
    }
}