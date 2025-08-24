<?php
require_once "handlerRegister.php";

class Registration
{
    private $filename = '/var/www/html/creds.txt';

    public function saveUser($username, $password)
    {
        // Проверяем, существует ли пользователь
        if ($this->userExists($username)) {
            return false; // Пользователь уже существует
        }

        $data = trim($username) . ':' . trim($password) . PHP_EOL;
        return file_put_contents($this->filename, $data, FILE_APPEND) !== false;
    }

    private function userExists($username)
    {
        // Проверяем существование файла
        if (!file_exists($this->filename)) {
            return false;
        }

        $handle = fopen($this->filename, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $line = trim($line);
                // Разделяем строку по двоеточию
                $parts = explode(":", $line, 2);
                if (count($parts) === 2) {
                    $fileUsername = trim($parts[0]);
                    if ($fileUsername === trim($username)) {
                        fclose($handle);
                        return true; // Пользователь найден
                    }
                }
            }
            fclose($handle);
        }
        return false; // Пользователь не найден
    }

    // Дополнительный метод для получения сообщения об ошибке
    public function getErrorMessage($username, $password)
    {
        if ($this->userExists($username)) {
            return "Ошибка: Пользователь '$username' уже существует!";
        }

        if (empty(trim($username))) {
            return "Ошибка: Имя пользователя не может быть пустым!";
        }

        if (empty(trim($password))) {
            return "Ошибка: Пароль не может быть пустым!";
        }

        return "Неизвестная ошибка при регистрации";
    }
}

