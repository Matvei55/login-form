<?php
namespace App\Core;
class Get
{
    private array $params;
    public function __construct()
    {
        $this->params = $_GET;
    }


    public function get(string $key, $default = null): mixed
    {
        return $this->params[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->params;
    }

    public function has(string $key): bool
    {
        return isset($this->params[$key]);
    }

    public function getInt(string $key, int $default = 0): int
    {
        return (int)($this->params[$key] ?? $default);
    }

    public function getString(string $key, string $default = ''): string
    {
        return trim(htmlspecialchars($this->params[$key] ?? $default));
    }
}
