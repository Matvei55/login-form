<?php
require_once 'database.php';

try {
    $pdo = getPDO();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_POST['username'] && $_POST['password']) {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->fetch()) {
            $_SESSION['errors'] = ["Пользователь '$username' уже существует"];
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");

            if ($stmt->execute([$username, $password_hash])) {
                $_SESSION['success'] = "Пользователь '$username' зарегистрирован";
            } else {
                $_SESSION['errors'] = ["Не удалось зарегистрировать пользователя"];
            }
        }
    } else {
        $_SESSION['errors'] = ['Заполните все поля'];
    }
} catch (PDOException $e) {
    $_SESSION['errors'] = ["Ошибка базы данных: " . $e->getMessage()];
}


