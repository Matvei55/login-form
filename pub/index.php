<?php
require_once __DIR__.'/../vendor/autoload.php';


use App\Core\Application;

session_start();
$app = Application::getInstance();
$app->run();