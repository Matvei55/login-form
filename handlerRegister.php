<?php
require_once "auth.php";
class RegistrationValidator
{
    private $username;
    private $password;

    public function __construct($username, $password)
    {
        $this->username = trim($username);
        $this->password = trim($password);
    }

    public function validateRegistration()
    {
        $errors = [];

        if (!$this->validateUsername()) {
            $errors[] = "Имя пользователя должно содержать только латинские буквы, цифры, дефисы и подчеркивания";
        }

        if (!$this->validatePassword()) {
            $errors[] = "Пароль должен содержать только латинские буквы, цифры, дефисы и подчеркивания";
        }

        if (strlen($this->username) < 3) {
            $errors[] = "Имя пользователя должно быть не менее 3 символов";
        }

        if (strlen($this->password) < 6) {
            $errors[] = "Пароль должен быть не менее 6 символов";
        }

        return empty($errors) ? true : $errors;
    }

    private function validateUsername()
    {
        // Должно возвращать TRUE если валидно, FALSE если невалидно
        return preg_match("/^[a-zA-Z0-9_-]+$/", $this->username);
    }

    private function validatePassword()
    {
        // Должно возвращать TRUE если валидно, FALSE если невалидно
        return preg_match("/^[a-zA-Z0-9_-]+$/", $this->password);
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;

    }
}
//--------------------------------------------------------------------
//if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
//    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
//
//    $validator = new RegistrationValidator($username, $password);
//    $result = $validator->validateRegistration();
//
//    if ($result === true) {
//        $userManager = new Registration();
//        $saveResult = $userManager->saveUser($username, $password);
//
//        if ($saveResult) {
//            echo "Регистрация успешна! Имя пользователя: " . htmlspecialchars($username) . "<br>";
//        } else {
//            // ✅ Используем метод для получения сообщения об ошибке
//            $errorMessage = $userManager->getErrorMessage($username, $password);
//            echo "Ошибка при сохранении: " . htmlspecialchars($errorMessage) . "<br>";
//        }
//    } else {
//        echo "Ошибки валидации:<br>";
//        foreach ($result as $error) {
//            echo "- " . htmlspecialchars($error) . "<br>";
//        }
//    }
//}
//-----------------------------------------------------------------хз говорю




