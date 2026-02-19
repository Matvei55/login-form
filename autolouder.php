<?php
spl_autoload_register(function ($className) {
    $baseDir = __DIR__ . '/src/';
    $file = $baseDir . str_replace('\\', '/', $className) . '.php';
    if (file_exists($file)) {
    require $file;
    return true;
    }

    $simpleFile = $baseDir . $className . '.php';
    if (file_exists($simpleFile)) {
        require $simpleFile;
        return true;
    }

    return false;
});