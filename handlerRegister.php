<?php
require_once 'database.php';

class HandlerRegister {
    private $db;

    public function __construct() {
        // Создаем Database внутри класса
        $this->db = new Database();
    }

    public function processRegistration() {
        if ($_POST['username'] && $_POST['password']) {
            $username = trim($_POST['username']);
            $password = $_POST['password'];

            // Валидация входных данных
            if (empty($username) || empty($password)) {
                $_SESSION['errors'] = ['Заполните все поля'];
                return;
            }

            try {
                // Проверяем существование пользователя
                $stmt = $this->db->query("SELECT id FROM users WHERE username = ?", [$username]);

                if ($stmt->fetch()) {
                    $_SESSION['errors'] = ["Пользователь '$username' уже существует"];
                } else {
                    // Регистрируем нового пользователя
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $result = $this->db->query(
                        "INSERT INTO users (username, password) VALUES (?, ?)",
                        [$username, $password_hash]
                    );

                    if ($result->rowCount() > 0) {
                        $_SESSION['success'] = "Пользователь '$username' зарегистрирован";
                    } else {
                        $_SESSION['errors'] = ["Не удалось зарегистрировать пользователя"];
                    }
                }

            } catch (PDOException $e) {
                $_SESSION['errors'] = ["Ошибка базы данных: " . $e->getMessage()];
            }
        } else {
            $_SESSION['errors'] = ['Заполните все поля'];
        }
    }
}

// Создаем экземпляр БЕЗ параметров
$handlerRegister = new HandlerRegister();
$handlerRegister->processRegistration();