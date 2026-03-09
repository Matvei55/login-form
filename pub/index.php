<?php
require __DIR__ . '/../app/autoloader.php';
session_start();
use Ren\Render;

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