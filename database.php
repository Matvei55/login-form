<?php
class Database {
    private $host = "mysql";
    private $dbname = "db";
    private $username = "user";
    private $password = "password";
    private $pdo;

    public function __construct(){
        try {
            $this->pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    // Дополнительные опции PDO для надежности
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );

            // ШАГ 2: Устанавливаем кодировку через SQL команды
            $this->pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->pdo->exec("SET CHARACTER SET utf8mb4");
            $this->pdo->exec("SET collation_connection = 'utf8mb4_unicode_ci'");
        } catch (PDOException $e) {
            die('Ошибка подключения: ' . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->pdo;
    }

    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}

