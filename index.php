<?php
//require_once 'render.php';
//session_start();
//
//// Обработка POST запросов
//if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//    $page = $_GET['page'] ?? '';
//
//    switch ($page) {
//        case 'registerSubmit':
//            require_once 'handlerRegister.php';
//            break;
//
//        case 'loginSubmit':
//            require_once 'handlerLogin.php';
//            break;
//    }
//
//    // Редирект после обработки POST
//    header('Location: http://localhost:81/?page=form');
//    exit;
//}
//$render = new Render();
//$data = [
//    'errors' => $_SESSION['errors'] ?? [],
//    'error' => $_SESSION['error'] ?? []
//];
//
//echo $render->render('form', $data);
session_start();
require_once 'database.php';
require_once 'render.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $page = $_GET["page"] ?? '';

    switch ($page) {
        case 'registerSubmit':
            require 'handlerRegister.php';
            break;

        case 'loginSubmit':
            require 'handlerLogin.php';
            break;
    }

    header("location: http://localhost:81/?page=form");
    exit();
}

$render = new Render();
$data = [
    'errors' => $_SESSION['errors'] ?? [],
    'error' => $_SESSION['error'] ?? [],
    'success' => $_SESSION['success'] ?? '',
    'isLoggedIn' => isset($_SESSION['user_id'])
];

echo $render->render('form', $data);
unset($_SESSION['errors'],$_SESSION['error'], $_SESSION['success']);