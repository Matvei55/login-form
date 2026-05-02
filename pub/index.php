<?php
require __DIR__ . '/../App/Autolouder.php';
session_start();
use App\Render;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $page = $_GET["page"] ?? '';

    switch ($page) {
        case 'registerSubmit':
            require __DIR__ . '/../app/HandlerRegister.php';
            break;

        case 'loginSubmit':
            require __DIR__ . '/../app/HandlerLogin.php';
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