<?php
require_once 'database.php';

try {
    $pdo = getPDO();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_POST['username'] && $_POST['password']) {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        // Ищем пользователя в базе
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user) {
            // Проверяем пароль
            if (password_verify($password, $user['password'])) {
                // Успешный вход
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['success'] = "Вход выполнен, добро пожаловать '$username'!";
            } else {
                $_SESSION['error'] = ["Неверный пароль"];
            }
        } else {
            $_SESSION['error'] = ["Пользователь '$username' не найден"];
        }
    } else {
        $_SESSION['error'] = ['Заполните все поля'];
    }
} catch (PDOException $e) {
    $_SESSION['error'] = ["Ошибка базы данных: " . $e->getMessage()];
}

