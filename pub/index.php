<?php
require_once __DIR__.'/../vendor/autoload.php';
session_start();

use App\Core\Request;
use App\Core\Router;

$router = new Router();
$request = new Request();
$router->dispatch($request);