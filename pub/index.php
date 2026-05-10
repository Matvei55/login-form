<?php
require __DIR__ . '/../App/Autoloader.php';
session_start();
use App\Models\Users;
use App\Render;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $page = $_GET["page"] ?? '';

    switch ($page) {
        case 'userSubmit':
            require __DIR__ . '/../App/HandlerUser.php';
            break;

        case 'postSubmit':
            require __DIR__ . '/../App/HandlerPost.php';
            break;
    }

    header("Location: /index.php?page=form");
    exit();
}

$render = new Render();
$data = [
    'userErrors' => $_SESSION['userErrors'] ?? [],
    'userSuccess' => $_SESSION['userSuccess'] ?? '',
    'postErrors' => $_SESSION['postErrors'] ?? [],
    'postSuccess' => $_SESSION['postSuccess'] ?? '',
];

echo $render->render('form', $data);

// Очищаем сессию после вывода
unset($_SESSION['userErrors'], $_SESSION['userSuccess'], $_SESSION['postErrors'], $_SESSION['postSuccess']);