<?php

spl_autoload_register(function ($className) {
    $appDir = __DIR__ . '/';
    $className = basename(str_replace('\\', '/', $className));
    $file = $appDir . $className . '.php';

    if (file_exists($file)) {
        require $file;
        return true;
    }
    $file = $appDir . lcfirst($className) . '.php';
    if (file_exists($file)) {
        require $file;
        return true;
    }

    return false;
});