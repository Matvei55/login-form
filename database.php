<?php
function  getPDO()
{
    $host = 'mysql';
    $dbname = 'db';  // Используем 'db' из MYSQL_DATABASE
    $username = 'user';  // Используем 'user' из MYSQL_USER
    $password = 'password';  // Используем 'password' из MYSQL_PASSWORD

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
} catch (PDOException $e) {
    die(" Ошибка подключения: " . $e->getMessage());
}
}
