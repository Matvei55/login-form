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
                // Используем двоеточие как разделитель
                $parts = explode(":", $line, 2);
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
                // Используем двоеточие как разделитель
                $parts = explode(":", $line, 2);
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
//-----------------------------------------------------------
// ПЕРЕМЕЩАЕМ проверку файла ВНУТРЬ обработки логина
// Не проверяем файл заранее, так как при регистрации его может не быть

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Определяем, это регистрация или вход (например, по наличию кнопки)
    $isRegistration = isset($_POST['register']);

    if ($isRegistration) {
        // Обработка регистрации
        $registration = new Registration();
        if ($registration->saveUser($username, $password)) {
            echo "Регистрация успешна!";
        } else {
            echo $registration->getErrorMessage($username, $password);
        }
    } else {
        // Обработка входа - ТОЛЬКО здесь проверяем файл
        $checkFile = $_SERVER['DOCUMENT_ROOT'] . '/creds.txt';
        if (!file_exists($checkFile)) {
            echo "ОШИБКА: Файл с учетными данными не найден<br>";
            echo "Возможно, вы еще не зарегистрированы";
            exit;
        }

        $loginHandler = new handlerLogin($username, $password);
        $result = $loginHandler->validate();

        if ($result === true) {
            echo "Успешный вход! Добро пожаловать, " . htmlspecialchars($username);
        } else {
            echo "Ошибки:<br>";
            foreach ($result as $error) {
                echo "- " . htmlspecialchars($error) . "<br>";
            }
        }
    }
}
//----------------------------------------------------------

