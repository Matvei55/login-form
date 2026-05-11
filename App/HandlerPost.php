<?php
use App\Models\Posts;
use App\Models\Tags;
use App\Models\Users;
use App\QueryBuilder;

if (!isset($_SESSION['user_id'])) {
    header("Location: /index.php?page=login");
    exit();
}

$postModel = new Posts();
$tagModel = new Tags();
$userModel = new Users();

$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$tagsInput = trim($_POST['tags'] ?? '');
$errors = [];

if (empty($title)) {
    $errors[] = 'Заголовок обязателен';
} elseif (mb_strlen($title) < 3) {
    $errors[] = 'Заголовок минимум 3 символа';
}

if (empty($errors)) {
    // Получаем пользователя
    $user = $userModel->load($_SESSION['user_id']);

    $postModel->setUser($user)
        ->setData([
            'title' => $title,
            'content' => $content
        ]);

    $postId = $postModel->save();

    if ($postId && !empty($tagsInput)) {
        $tagNames = array_unique(array_filter(explode(' ', $tagsInput)));
        foreach ($tagNames as $tagName) {
            $existingTag = $tagModel->findByName($tagName);
            if ($existingTag) {
                $tagId = $existingTag['id'];
            } else {
                $tagId = $tagModel->setData(['name' => $tagName])->save();
            }
            if ($tagId) {
                $tagModel->builder->attachTag($postId, $tagId);
            }
        }
    }

    if ($postId) {
        $_SESSION['success'] = "Пост '{$title}' успешно создан!";
    } else {
        $errors[] = 'Не удалось создать пост';
    }
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
}

header("Location: /index.php?page=posts");
exit();