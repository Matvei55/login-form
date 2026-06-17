<?php
namespace App;
use App\Models\Posts;
use App\Models\Tags;
use App\Models\Users;

class PostsController
{
    private Posts $postModel;
    private Users $userModel;
    private Tags $tagModel;

    private View $view;

    public function __construct()
    {
        $this->postModel = new Posts();
        $this->userModel = new Users();
        $this->tagModel = new Tags();
        $this->view = new View();
    }

    public function index(Request $request): void
    {
        if(!isset($_SESSION['user_id'])){
            header('Location:/login');
            exit();
        }
        $user =$this->userModel->load($_SESSION['user_id']);
        $userPosts = $this->postModel->getPostsByUserId($user);
        foreach ($userPosts as $post) {
            $tags= $this->tagModel->getPostTags($post->getId());
            $post->setTags($tags);
        }
        $data = [
            'user' => $user,
            'userPosts' => $userPosts,
            'errors' => $_SESSION['errors'] ?? [],
            'success' => $_SESSION['success'] ?? '',
        ];
        echo $this->view->render('posts', $data);
        unset($_SESSION['errors'],$_SESSION['success']);
    }

    public function store(Request $request): void
    {
        if(!isset($_SESSION['user_id'])){
            header('Location:/login');
            exit();
        }
        $title = trim($request->post()->getString('title', ''));
        $content = trim($request->post()->getString('content', ''));
        $tagsInput = trim($request->post()->getString('tags', ''));
        $errors = [];
        if(empty($title)){
            $errors[]= 'заголовок обязателен';
        }elseif(mb_strlen($title) <3){
            $errors[] = 'заголовок минимум 3 символа';
        }
        if(empty($errors)){
            $user = $this->userModel->load($_SESSION['user_id']);
            $postId = $this->postModel->setUser($user)
                ->setData([
                    'title' => $title,
                    'content' => $content,
                ])->save();
            if($postId && !empty($tagsInput)){
                $tagNames = array_unique(array_filter(explode(',', $tagsInput)));
                foreach ($tagNames as $tagName) {
                    $tag = $this->tagModel->findOrCreate($tagName);
                    $this->postModel->attachTag($tag);
                }
            }
            if($postId){
                $_SESSION['success'] = 'пост успешно создан';
            }else{
                $errors[]= 'не удалось создать пост';
            }
            if(!empty($errors)){
                $_SESSION['errors'] = $errors;
            }
            header('Location:/posts');
            exit();
        }
    }
}