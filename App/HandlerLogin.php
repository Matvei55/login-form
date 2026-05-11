<?php
namespace App;

use App\Models\Users;

$userModel = new Users();
$username =trim($_POST['username'] ?? '');
$password= $_POST['password'] ?? '';
$errors = [];
$success = null;

if(empty($username)){
    $errors[] = "имя пользователя надо заполнить";
}elseif (mb_strlen($username) < 1){
    $errors[] = "имя пользователя должно содержать не меньше одного символа";
}elseif(mb_strlen($username) > 67){
    $errors[]= "имя пользователя не должно быть меньше 67 символов";
}

if(empty($password)){
    $errors[] = "поля пароля обязательно для заполнения";
}elseif (strlen($password) < 4){
    $errors[]="пароль должен быть не меньше 4 символов";
}

if(empty($errors)){
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $userId = $userModel->setData([
        'name' => $username,
        'password' => $hashedPassword,
    ])->save();

    if($userId){
        $success= "Пользователь '{$username}' успешно создан!";
    }else{
        $errors[] = 'не удалось создать пользователя';
    }
}

$_SESSION['userErrors'] = $errors;
$_SESSION['userSuccess'] = $success;
