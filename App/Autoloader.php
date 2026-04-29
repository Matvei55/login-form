<?php

//spl_autoload_register(function ($className) {
//    $appDir = __DIR__ . '/';
//    $className = basename(str_replace('\\', '/', $className));
//    $file = $appDir . $className . '.php';
//
//    if (file_exists($file)) {
//        require $file;
//        return true;
//    }
//    $file = $appDir . lcfirst($className) . '.php';
//    if (file_exists($file)) {
//        require $file;
//        return true;
//    }
//
//    return false;
//});

spl_autoload_register(function ($className) {
    $baseDir = __DIR__ . '/';
    if(strpos($className, 'App\\') === 0){
        $className = substr($className , 4);
    }
    $filePath = $baseDir . str_replace('\\', '/', $className) . '.php';

    if(file_exists($filePath)){
        require $filePath;
        return true;
    }
    return false;
});