<?php
namespace App\Controllers;

use App\Core\Application;
use App\Core\Controller;
use App\Core\Session;
use App\Core\View;
use App\Events\PostCreatedEvent;
use App\Models\Posts;
use App\Models\Tags;
use App\Models\Users;
use App\Core\Request;
use App\Container\ContainerInterface;
use App\Middleware\AuthMiddleware;

class PostsController extends Controller
{
    public function __construct(Request $request, View $view,Session $session ,private Users $userModel, private Posts $postModel, private Tags $tagModel)
    {
        parent::__construct($request, $view, $session);
    }

    public function getMiddlewareConfig():array
    {
        return [
            'index' => [AuthMiddleware::class],
            'store' => [AuthMiddleware::class],
        ];
    }
    
    public function index(Request $request): void
    {
        $this->requireAuth();

        $userId = $this->session->getUserId();
        $user = $this->userModel->load($userId);
        $userPosts = $this->postModel->getPostsByUserId($user);

        foreach ($userPosts as $post) {
            $tags = $this->tagModel->getPostTags($post->getId());
            $post->setTags($tags);
        }

        $data = [
            'user' => $user->getData(),
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

        $title = trim($request->postParam('title', ''));
        $content = trim($request->postParam('content', ''));
        $tagsInput = trim($request->postParam('tags', ''));

        if (empty($title)) {
            $this->setError('Заголовок обязателен');
        } elseif (mb_strlen($title) < 3) {
            $this->setError('Заголовок минимум 3 символа');
        }

        if (empty($content)) {
            $this->setError('Содержание обязательно');
        }

        if ($this->hasErrors()) {
            $this->redirect('/posts');
            return;
        }

        $user = $this->userModel->load($this->session->getUserId());
        $postId = $this->postModel->setUser($user)
            ->setData([
                'title' => $title,
                'content' => $content,
            ])->save();

        if ($postId) {
            if (!empty($tagsInput)) {
                $tagNames = array_unique(array_filter(explode(',', $tagsInput)));
                foreach ($tagNames as $tagName) {
                    $tag = $this->tagModel->findOrCreate($tagName);
                    $this->postModel->attachTag($tag);
                }
            }
            $event = new PostCreatedEvent($this->postModel, $user);
            Application::getInstance()->getDispatcher()->dispatch($event);

            $this->setSuccess('Пост успешно создан!');
        }

        $this->redirect('/posts');
    }
}