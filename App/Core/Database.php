<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;
    private function __construct()
    {
        $config = Config::getDatabaseConfig();

        try {
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['name']};charset=utf8mb4";
            $this->pdo = new PDO($dsn, $config['user'], $config['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }catch (PDOException $e){
        die("ошибка подключения к бд: " . $e->getMessage());
        }
    }
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}

