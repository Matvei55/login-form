<?php
namespace App;

use App\Models\Users;

//$userModel = new Users();
//$username =trim($_POST['username'] ?? '');
//$password= $_POST['password'] ?? '';
//$errors = [];
//$success = null;
//
//if(empty($username)){
//    $errors[] = "имя пользователя надо заполнить";
//}elseif (mb_strlen($username) < 1){
//    $errors[] = "имя пользователя должно содержать не меньше одного символа";
//}elseif(mb_strlen($username) > 67){
//    $errors[]= "имя пользователя не должно быть меньше 67 символов";
//}
//
//if(empty($password)){
//    $errors[] = "поля пароля обязательно для заполнения";
//}elseif (strlen($password) < 3){
//    $errors[]="пароль должен быть не меньше 4 символов";
//}
//
//if(empty($errors)){
//    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
//    $userId = $userModel->setData([
//        'name' => $username,
//        'password' => $hashedPassword,
//    ])->save();
//
//    if($userId){
//        $success= "Пользователь '{$username}' успешно создан!";
//    }else{
//        $errors[] = 'не удалось создать пользователя';
//    }
//}
//
//$_SESSION['errors'] = $errors;
//$_SESSION['success'] = $success;
class RegisterController
{
    private Users $userModel;

    public function __construct()
    {
        $this->userModel = new Users();
    }

    public function register(array $postData): void
    {
        $username = trim($postData['username'] ?? '');
        $password = $postData['password'] ?? '';
        $errors = [];

        if (empty($username)) {
            $errors[] = 'Имя пользователя обязательно';
        } elseif (mb_strlen($username) < 3) {
            $errors[] = 'Имя пользователя минимум 3 символа';
        } elseif (mb_strlen($username) > 67) {
            $errors[] = 'Имя пользователя не должно превышать 67 символов';
        }

        if (empty($password)) {
            $errors[] = 'Пароль надо заполнить';
        } elseif (strlen($password) < 4) {
            $errors[] = 'Пароль минимум 4 символа';
        }

        if (empty($errors)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $userId = $this->userModel->setData([
                'name' => $username,
                'password' => $hashedPassword
            ])->save();

            if ($userId) {
                $_SESSION['user_id'] = $userId;
                $_SESSION['success'] = "Регистрация прошла успешно! Добро пожаловать, {$username}!";
                $this->redirect('/index.php?page=posts');
                return;
            } else {
                $errors[] = 'Пользователь с таким именем уже существует';
            }
        }
        $_SESSION['errors'] = $errors;
        $this->redirect('/index.php?page=register');
    }

    private function redirect(string $url): void
    {
        header("Location: {$url}");
        exit();
    }
}