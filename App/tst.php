<?php
namespace App;
require __DIR__ . '/Autoloader.php';
//
//use App\Models\Posts;
//use App\Models\Tags;
//
////$user = new Users();
////$user->setData([
////    'name' => "5",
////    'password' => password_hash("5", PASSWORD_DEFAULT)
////]);
////$user->save();
//$post = new Posts();
//$tag1 = new Tags();
//$tag2 = new Tags();
////$tag2->setData([
////    'title' => "10"
////]);
////$tag2->save();
////$tag1->setData([
////    'title' => "11"
////]);
////$tag1->save();
////$post->setData([
////    'title' => "....",
////    'content' => "bla"
////]);
////$post->setUser($user);
//$post->load(21);
////$post->addTag($tag1);
////$post->addTag($tag2);
////$post->save();
//$posts = $post->getTags();
//foreach ($posts as $tag) {
//    $data = $tag->getData();
//    echo $data['title'] . "\n";
//}



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
