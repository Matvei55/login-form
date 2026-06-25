<?php
namespace Core;

class Post
{
    private array $params;

    public function __construct()
    {
        $this->params = $_POST;
    }

    public function get(string $key, $default = null): mixed
    {
        return $this->params[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->params;
    }

    public function has(string $key): bool //существует ли параметр с таким ключом
    {
        return isset($this->params[$key]);
    }

    public function getInt(string $key, int $default = 0): int //преобразовываю значение в целое число
    {
        return (int)($this->params[$key] ?? $default);
    }

    public function getString(string $key, string $default = ''): string //получение строки
    {
        return trim(htmlspecialchars($this->params[$key] ?? $default));
    }
    


}