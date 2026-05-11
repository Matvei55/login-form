<?php
//require __DIR__ . '/../App/Autoloader.php';
//session_start();
//use App\Models\Users;
//use App\Render;
//
//if ($_SERVER["REQUEST_METHOD"] === "POST") {
//    $page = $_GET["page"] ?? '';
//
//    switch ($page) {
//        case 'userSubmit':
//            require __DIR__ . '/../App/HandlerUser.php';
//            break;
//
//        case 'postSubmit':
//            require __DIR__ . '/../App/HandlerPost.php';
//            break;
//    }
//
//    header("Location: /index.php?page=form");
//    exit();
//}
//
//$render = new Render();
//$data = [
//    'userErrors' => $_SESSION['userErrors'] ?? [],
//    'userSuccess' => $_SESSION['userSuccess'] ?? '',
//    'postErrors' => $_SESSION['postErrors'] ?? [],
//    'postSuccess' => $_SESSION['postSuccess'] ?? '',
//];
//
//echo $render->render('form', $data);
//
//// Очищаем сессию после вывода
//unset($_SESSION['userErrors'], $_SESSION['userSuccess'], $_SESSION['postErrors'], $_SESSION['postSuccess']);
require_once __DIR__ . '/../App/Autoloader.php';
session_start();
use App\Render;
use App\Models\Posts;
use App\Models\Users;

$page = $_GET['page'] ?? 'login';
$postModel = new Posts();
$userModel = new Users();

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
$userPost = [];
$errors = $_SESSION['errors'] ?? [];
$success = $_SESSION['success'] ?? [];

if (isset($_SESSION['user_id'])) {
    $user = $userModel->load($_SESSION['user_id'])->getData();
    $userPosts = $postModel->getPostsByUser($_SESSION['user_id']);
}

unset($_SESSION['errors'], $_SESSION['success']);

$render = new Render();
$data = [
    'page' => $page,
    'user' => $user,
    'userPosts' => $userPost,
    'errors' => $errors,
    'success' => $success,
];

echo $render->render($page, $data);
