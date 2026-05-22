<?php
require_once __DIR__ . '/../App/Autoloader.php';
session_start();
use App\Render;
use App\Models\Posts;
use App\Models\Users;
use App\Models\Tags;

$page = $_GET['page'] ?? 'login';
$postModel = new Posts();
$userModel = new Users();
$tagModel = new Tags();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'login':
            require __DIR__ . '/../App/HandlerLogin.php';
            break;
        case 'register':
            require __DIR__ . '/../App/HandlerRegister.php';
            break;
        case 'createPost':
            require __DIR__ . '/../App/HandlerPost.php';
            break;
    }
}
$user = null;
$userPosts = [];
$errors = $_SESSION['errors'] ?? [];
$success = $_SESSION['success'] ?? [];

if (isset($_SESSION['user_id'])) {
    $user = $userModel->load($_SESSION['user_id'])->getData();
    $userPosts = $postModel->getPostsByUser($_SESSION['user_id']);

    foreach ($userPosts as &$post) {
        $post['tags'] = $tagModel->getPostTags($post['id']);
    }
}



$render = new Render();
$data = [
    'page' => $page,
    'user' => $user,
    'userPosts' => $userPosts,
    'errors' => $errors,
    'success' => $success
];

echo $render->render($page, $data);
unset($_SESSION['errors'], $_SESSION['success']);