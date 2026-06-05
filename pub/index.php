<?php
require_once __DIR__ . '/../App/Autoloader.php';
session_start();
use App\Router;
$router = new Router();

$router->get('/', 'LoginController', 'showLogin');
$router->get('/login', 'LoginController', 'showLogin');
$router->get('/register', 'RegisterController', 'showRegister');
$router->get('/posts', 'PostController', 'showPosts');
$router->get('/logout', 'LoginController', 'logout');

$router->post('/login', 'LoginController', 'login');
$router->post('/register', 'RegisterController', 'register');
$router->post('/create-post', 'PostController', 'createPost');

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);