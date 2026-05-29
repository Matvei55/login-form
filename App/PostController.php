<?php
namespace App;
use App\Models\Posts;
use App\Models\Tags;
use App\Models\Users;

class PostController
{
    private Posts $postModel;
    private Users $userModel;
    private Tags $tagModel;

    public function __construct()
    {
        $this->postModel = new Posts();
        $this->userModel = new Users();
        $this->tagModel = new Tags();
    }

//    public function createPost(array $postData): void
//    {
//        if(!isset($_SESSION['user_id'])) {
//            $this->redirect('/index.php?page=login');
//            return;
//        }
//        $title = trim($postData['title'] ?? '');
//        $content = trim($postData['content'] ?? '');
//        $tagsInput = trim($postData['tags'] ?? '');
//        $errors = [];
//
//        if(empty($title)) {
//            $errors[] = 'заголовок обязателен';
//        }elseif (mb_strlen($title) < 3) {
//            $errors[] = 'заголовок минимум 3 символа';
//        }
//
//        if(empty($errors)) {
//            $user = $this->userModel->load($_SESSION['user_id']);
//            $postId = $this->postModel->setUser($user)
//                ->setData([
//                    'title' => $title,
//                    'content' => $content
//                ])->save();
//            if($postId && !empty($tagsInput)) {
//                $tagNames = array_unique(array_filter(explode(' ', $tagsInput)));
//                foreach ($tagNames as $tagName) {
//                    $existingTag = $this->tagModel->findByName($tagName);
//                    if($existingTag) {
//                        $tagId = $existingTag['id'];
//                    }else {
//                        $tagId = $this->tagModel->setData(['title' => $tagName])->save();
//                    }
//
//                    if($tagId){
//                        $this->tagModel->attachTag($postId, $tagId);
//                    }
//                }
//            }
//            if($postId) {
//                $_SESSION['success'] = "пост успешно создан";
//            }else{
//                $errors[] = "не удалось создать пост";
//            }
//            if(!empty($errors)) {
//                $_SESSION['errors'] = $errors;
//            }
//            $this->redirect('/index.php?page=posts');
//        }
//    }
    private function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    public function createPost(array $postData): void
{
    if (!isset($_SESSION['user_id'])) {
        $this->redirect('/index.php?page=login');
        return;
    }

    $title = trim($postData['title'] ?? '');
    $content = trim($postData['content'] ?? '');
    $tagsInput = trim($postData['tags'] ?? '');
    $errors = [];

    if (empty($title)) {
        $errors[] = 'Заголовок обязателен';
    } elseif (mb_strlen($title) < 3) {
        $errors[] = 'Заголовок минимум 3 символа';
    }

    if (empty($errors)) {
        $user = $this->userModel->load($_SESSION['user_id']);
        $postId = $this->postModel->setUser($user)
            ->setData([
                'title' => $title,
                'content' => $content
            ])->save();

        if ($postId && !empty($tagsInput)) {
            $tagNames = array_unique(array_filter(explode(' ', $tagsInput)));

            foreach ($tagNames as $tagName) {
                // ✅ Ищем или создаём тег
                $tagId = $this->tagModel->findOrCreate($tagName);

                if ($tagId) {
                    $this->postModel->attachTag($postId, $tagId);
                }
            }
        }

        if ($postId) {
            $_SESSION['success'] = "Пост успешно создан!";
        } else {
            $errors[] = 'Не удалось создать пост';
        }
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
    }

    $this->redirect('/index.php?page=posts');
}

}