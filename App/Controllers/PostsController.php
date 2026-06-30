<?php
////namespace App\Controllers;
////
////use App\Core\Controller;
////use App\Models\Posts;
////use App\Models\Tags;
////use App\Models\Users;
////use App\Core\Request;
////
////class PostsController extends Controller
////{
////    private Posts $postModel;
////    private Users $userModel;
////    private Tags $tagModel;
////
////    public function __construct()
////    {
////        parent::__construct();
////        $this->postModel = new Posts();
////        $this->userModel = new Users();
////        $this->tagModel = new Tags();
////    }
//////    public function index(Request $request): void
//////    {
//////        $this->requireAuth();
//////
//////        $user = $this->getUser();
//////        $userPosts = $this->postModel->getPostsByUserId($user);
//////
//////        foreach ($userPosts as $post) {
//////            $tags = $this->tagModel->getPostTags($post->getId());
//////            $post->setTags($tags);
//////        }
//////
//////        $data = [
//////            'user' => $user,
//////            'userPosts' => $userPosts,
//////            'errors' => $this->getErrors(),
//////            'success' => $this->getSuccess(),
//////        ];
//////
//////        echo $this->render('posts', $data);
//////        $this->clearSession();
//////    }
////    public function index(Request $request): void
////    {
////        $this->requireAuth();
////
////        // Загружаем объект Users
////        $userId = $this->session->getUserId();
////        $user = $this->userModel->load($userId);
////
////        // Передаём объект
////        $userPosts = $this->postModel->getPostsByUserId($user);
////
////        foreach ($userPosts as $post) {
////            $tags = $this->tagModel->getPostTags($post->getId());
////            $post->setTags($tags);
////        }
////
////        $data = [
////            'user' => $user->getData(),
////            'userPosts' => $userPosts,
////            'errors' => $this->getErrors(),
////            'success' => $this->getSuccess(),
////        ];
////
////        echo $this->render('posts', $data);
////        $this->clearSession();
////    }
////    public function store(Request $request): void
////    {
////        $this->requireAuth();
////
////        $title = trim($request->postParam('title', ''));
////        $content = trim($request->postParam('content', ''));
////        $tagsInput = trim($request->postParam('tags', ''));
////
////        // Валидация
////        if (empty($title)) {
////            $this->setError('Заголовок обязателен');
////        } elseif (mb_strlen($title) < 3) {
////            $this->setError('Заголовок минимум 3 символа');
////        }
////
////        if (empty($content)) {
////            $this->setError('Содержание обязательно');
////        }
////
////        if ($this->hasErrors()) {
////            $this->redirect('/posts');
////            return;
////        }
////
////        $user = $this->userModel->load($this->session->getUserId());
////        $postId = $this->postModel->setUser($user)
////            ->setData([
////                'title' => $title,
////                'content' => $content,
////            ])->save();
////
////        if ($postId) {
////
////            if (!empty($tagsInput)) {
////                $tagNames = array_unique(array_filter(explode(',', $tagsInput)));
////                foreach ($tagNames as $tagName) {
////                    $tag = $this->tagModel->findOrCreate($tagName);
////                    $this->postModel->attachTag($tag);
////                }
////            }
////
////            $this->setSuccess('Пост успешно создан!');
////        } else {
////            $this->setError('Не удалось создать пост');
////        }
////
////        $this->redirect('/posts');
////    }
////}
//namespace App\Controllers;
//
//use App\Core\Controller;
//use App\Models\Posts;
//use App\Models\Tags;
//use App\Models\Users;
//use App\Core\Request;
//
//class PostsController extends Controller
//{
//    private Posts $postModel;
//    private Users $userModel;
//    private Tags $tagModel;
//
//    public function __construct()
//    {
//        parent::__construct();
//        $this->postModel = new Posts();
//        $this->userModel = new Users();
//        $this->tagModel = new Tags();
//    }
//
//    public function index(Request $request): void
//    {
//        $this->requireAuth();
//
//        // Загружаем объект Users
//        $userId = $this->session->getUserId();
//        $user = $this->userModel->load($userId);
//
//        // Передаём объект
//        $userPosts = $this->postModel->getPostsByUserId($user);
//
//        foreach ($userPosts as $post) {
//            $tags = $this->tagModel->getPostTags($post->getId());
//            $post->setTags($tags);
//        }
//
//        $data = [
//            'user' => $user->getData(),
//            'userPosts' => $userPosts,
//            'errors' => $this->getErrors(),
//            'success' => $this->getSuccess(),
//        ];
//
//        echo $this->render('posts', $data);
//        $this->clearSession();
//    }
//
//    public function store(Request $request): void
//    {
//        $this->requireAuth();
//
//        $title = trim($request->postParam('title', ''));
//        $content = trim($request->postParam('content', ''));
//        $tagsInput = trim($request->postParam('tags', ''));
//
//        if (empty($title)) {
//            $this->setError('Заголовок обязателен');
//        } elseif (mb_strlen($title) < 3) {
//            $this->setError('Заголовок минимум 3 символа');
//        }
//
//        if (empty($content)) {
//            $this->setError('Содержание обязательно');
//        }
//
//        if ($this->hasErrors()) {
//            $this->redirect('/posts');
//            return;
//        }
//
//        $user = $this->userModel->load($this->session->getUserId());
//        $postId = $this->postModel->setUser($user)
//            ->setData([
//                'title' => $title,
//                'content' => $content,
//            ])->save();
//
//        if ($postId) {
//            if (!empty($tagsInput)) {
//                $tagNames = array_unique(array_filter(explode(',', $tagsInput)));
//                foreach ($tagNames as $tagName) {
//                    $tag = $this->tagModel->findOrCreate($tagName);
//                    $this->postModel->attachTag($tag);
//                }
//            }
//
//            $this->setSuccess('Пост успешно создан!');  // ✅ Теперь строка
//        } else {
//            $this->setError('Не удалось создать пост');
//        }
//
//        $this->redirect('/posts');
//    }
//}
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Posts;
use App\Models\Tags;
use App\Models\Users;
use App\Core\Request;

class PostsController extends Controller
{
    private Posts $postModel;
    private Users $userModel;
    private Tags $tagModel;

    public function __construct()
    {
        parent::__construct();
        $this->postModel = new Posts();
        $this->userModel = new Users();
        $this->tagModel = new Tags();
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

            $this->setSuccess('Пост успешно создан!');
        } else {
            $this->setError('Не удалось создать пост');
        }

        $this->redirect('/posts');
    }
}