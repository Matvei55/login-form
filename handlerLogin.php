<?php
class handlerLogin
{
    private $username;
    private $password;
    private $credentialFile;

    public function __construct($username, $password)
    {

        $this->username = $username !== null ? trim($username) : '';
        $this->password = $password !== null ? ($password) : '';


        $this->credentialFile = $_SERVER['DOCUMENT_ROOT'] . '/creds.txt';
    }

    public function validate()
    {
        $errors = [];


        if (!file_exists($this->credentialFile)) {
            $errors[] = "Файл с учетными данными не найден";
            return $errors;
        }

        if (!$this->validateUsername()) {
            $errors[] = "такого имени не существует";
        }
        if (!$this->validatePassword()) {
            $errors[] = "неверный пароль";
        }
        return empty($errors) ? true : $errors;
    }

    private function validateUsername()
    {
        if (!file_exists($this->credentialFile)) {
            return false;
        }

        $handle = fopen($this->credentialFile, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $line = trim($line);
                // Используем пробел как разделитель
                $parts = explode(" ", $line, 2);
                if (count($parts) === 2) {
                    $fileUsername = trim($parts[0]);
                    if ($fileUsername === $this->username) {
                        fclose($handle);
                        return true; // пользователь найден
                    }
                }
            }
            fclose($handle);
        }
        return false;
    }

    private function validatePassword()
    {
        if (!file_exists($this->credentialFile)) {
            return false;
        }

        $handle = fopen($this->credentialFile, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $line = trim($line);
                $parts = explode(" ", $line, 2);
                if (count($parts) === 2) {
                    $fileUsername = trim($parts[0]);
                    $filePassword = trim($parts[1]);
                    if ($fileUsername === $this->username && $filePassword === $this->password) {
                        fclose($handle);
                        return true;
                    }
                }
            }
            fclose($handle);
        }
        return false;
    }
}
$checkFile = $_SERVER['DOCUMENT_ROOT'] . '/creds.txt';
if (!file_exists($checkFile)) {
    die("ОШИБКА: Файл не найден: " . $checkFile .
        "<br>Проверьте путь и права доступа");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ? $_POST[ 'username'] : '';
    $password = $_POST['password'] ? $_POST[ 'password' ] : '';

    $loginHandler = new handlerLogin($username, $password);
    $result = $loginHandler->validate();

    // Обработка результата
    if ($result === true) {
        echo "Успешный вход! Добро пожаловать, " . htmlspecialchars($username);
    } else {
        echo "Ошибки:<br>";
        foreach ($result as $error) {
            echo "- " . htmlspecialchars($error) . "<br>";
        }
    }
}

