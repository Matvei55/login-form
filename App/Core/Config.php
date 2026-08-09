<?php
namespace App\Core;

class Config
{
    private static array $config = [];

    public static function load(string $path): void
    {
        if (!file_exists($path)) {
            throw new \Exception("Файл .env не найден:" . realpath(dirname($path)));
        }

        $dotenv = \Dotenv\Dotenv::createImmutable(dirname($path));
        $dotenv->load();

        self::$config = [
            'db' => [
                'host' => $_ENV['DB_HOST'] ?? 'mysql_db',
                'port' => (int) ($_ENV['DB_PORT'] ?? 3306),
                'name' => $_ENV['DB_NAME'] ?? 'db',
                'user' => $_ENV['DB_USER'] ?? 'user',
                'password' => $_ENV['DB_PASSWORD'] ?? 'password',
            ],
            'app' => [
                'env' => $_ENV['APP_ENV'] ?? 'development',
                'debug' => (bool) ($_ENV['APP_DEBUG'] ?? true),
                'url' => $_ENV['APP_URL'] ?? 'http://localhost:81',
            ],
            'session' => [
                'lifetime' => (int) ($_ENV['SESSION_LIFETIME'] ?? 120),
            ],

        ];
        $configPath = dirname($path) . '/config';
        if(is_dir($configPath)) {
            foreach (glob($configPath . '/*.php') as $file) {
                $key = basename($file, '.php');
                self::$config[$key] = require $file;
            }
        }
        self::$config['proxy'] = [
        'enabled' => true,
        'host' => $_ENV['PROXY_HOST'] ?? '127.0.0.1',
        'port' => (int) ($_ENV['PROXY_PORT'] ?? 1080),
        'type' => $_ENV['PROXY_TYPE'] ?? 'socks5',
        ];

    }

    public static function getDatabaseConfig(): array
    {
        return self::$config['db'] ?? [];
    }

    public static function get(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $segment) {
            if (!isset($value[$segment])) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    public static function getProxyConfig(): array
    {
        return self::$config['proxy'] ?? [];
    }
}

