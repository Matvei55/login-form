<?php

require __DIR__ . '/../App/Autoloader.php';

use App\Database;
use App\Models\Users;
use App\Models\Tags;
use App\Models\Posts;
use App\QueryBuilder;
use App\Models\AbstractModel;

//юзер


//$user ->load(1);
//$data = $user->getData();
//echo $data['name'];

//$db = Database::getInstance();
//$pdo = $db->getConnection();
//$sql = "INSERT INTO users (name,password) VALUES ('Matvei', 'hash') ";
//$pdo->exec($sql);
//
//echo "zxzxzx";

//$pdo = new PDO("mysql:host=mysql_db;dbname=users;charset=utf8", "root", "myrootpassword");
//$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//
//$sql = "INSERT INTO users (name, password) VALUES (:name, :password)";
//$stmt = $pdo->prepare($sql);
//
//$stmt->execute([
//    ':name' => '1111',
//    ':password' => '3333'
//]);
//echo "ID: " . $pdo->lastInsertId();$tags
//теги
//$tags = new Tags();
//$tags->setData([
//    'title' => "php"
//]);
//$tags->save();
//
//$tags->load(1);
//$data = $tags->getData();
//echo $data['title'];

//$post->setData([
//    'title' => "php",
//    'content' => "php in program",
//    'user_id' => 1,
//]);
//$post->save();
//$post->load(1);
//$data = $post->getData();
//echo $data['title'];
$post = new Posts();
$user = new Users();
$user->setData([
    'name' => "4",
    'password' => password_hash("4", PASSWORD_DEFAULT)
]);
$user->save();


$post->setData([
    'title' => "111",
    'content' => "222"
]);
$post->setUser($user);
$post->save();
$data=$post->getData();
echo $data['user_id'];

//


$post = new Posts();
$post->load(10);
