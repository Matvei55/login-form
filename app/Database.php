<?php
//namespace Ren;
//
//use PDO;
//use PDOException;
//
//class Database {
//    private $host = "mysql";
//    private $dbname = "db";
//    private $username = "user";
//    private $password = "password";
//    private $pdo;
//
//    public function __construct() {
//        try {
//            $this->pdo = new PDO(
//                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
//                $this->username,
//                $this->password,
//                [
//                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
//                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
//                    PDO::ATTR_EMULATE_PREPARES => false
//                ]
//            );
//            $this->pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
//            $this->pdo->exec("SET CHARACTER SET utf8mb4");
//            $this->pdo->exec("SET collation_connection = 'utf8mb4_unicode_ci'");
//
//        } catch (PDOException $e) {
//            die('Ошибка подключения: ' . $e->getMessage());
//        }
//    }
//
//    public function getConnection() {
//        return $this->pdo;
//    }
//
//    public function query($sql, $params = []) {
//        $stmt = $this->pdo->prepare($sql);
//        $stmt->execute($params);
//        return $stmt;
//    }
//}
namespace Ren;

use PDO;
use PDOException;

class Database {
    private static $instance = null;

    private $host = 'mysql';
    private $dbname = 'db';
    private $username = 'user';
    private $password = 'password';
    private $pdo;

    private function __construct() {
        $this->connect();
    }

    private function connect() {
        try{
            $this->pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );

        $this->pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
        $this->pdo->exec("SET CHARACTER SET utf8mb4");
        $this->pdo->exec("SET collation_connection = 'utf8mb4_unicode_ci'");

        } catch (PDOException $e) {
            die('ошибка подключения: ' . $e->getMessage());
        }
    }
    private function __clone() {}

    public function __wakeup() {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function getConnection() {
        return $this->pdo;
    }

    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }

    public function rollBack() {
        return $this->pdo->rollBack();
    }
    public function close() {
        $this->pdo = null;
        self::$instance = null;
    }

}

