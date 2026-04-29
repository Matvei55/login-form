<?php

require __DIR__ . '/../App/Autoloader.php';

use App\Database;
use App\Models\Users;
use App\QueryBuilder;
use App\Models\AbstractModel;
//$user = new Users();
//$user->setData([
//    '??name' => "Matvey",
//    '??password' => password_hash("Matvei", PASSWORD_DEFAULT)
//]);
//$user->save();

//$db = Database::getInstance();
//$pdo = $db->getConnection();
//$sql = "INSERT INTO users (name,password) VALUES ('Matvei', 'hash') ";
//$pdo->exec($sql);
//
//echo "zxzxzx";

$pdo = new PDO("mysql:host=mysql_db;dbname=users;charset=utf8", "root", "myrootpassword");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "INSERT INTO users (name, password) VALUES (:name, :password)";
$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':name' => '1111',
    ':password' => '3333'
]);
echo "ID: " . $pdo->lastInsertId();