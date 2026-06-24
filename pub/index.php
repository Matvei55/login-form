<?php
require_once __DIR__.'/../vendor/autoload.php';
session_start();
use App\Router;
use App\Request;
$router = new Router();
$request = new Request();
$router->dispatch($request);