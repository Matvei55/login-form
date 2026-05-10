<?php

require __DIR__ . '/../App/Autoloader.php';

use App\Models\Posts;
use App\Models\Tags;

//$user = new Users();
//$user->setData([
//    'name' => "5",
//    'password' => password_hash("5", PASSWORD_DEFAULT)
//]);
//$user->save();
$post = new Posts();
$tag1 = new Tags();
$tag2 = new Tags();
//$tag2->setData([
//    'title' => "10"
//]);
//$tag2->save();
//$tag1->setData([
//    'title' => "11"
//]);
//$tag1->save();
//$post->setData([
//    'title' => "....",
//    'content' => "bla"
//]);
//$post->setUser($user);
$post->load(21);
//$post->addTag($tag1);
//$post->addTag($tag2);
//$post->save();
$posts = $post->getTags();
foreach ($posts as $tag) {
    $data = $tag->getData();
    echo $data['title'] . "\n";
}

