<?php
$host = "mysql_db";
$dbname = "db";
$username = "user";
$password = "myrootpassword";
//переменные
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

    // Проверка подключения
    echo "Успешное подключение к базе данных!";

} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

