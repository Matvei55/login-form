<?php
include "handlerLogin.php";
include "handlerRegister.php";


class Registration
{
    private $filename = './workspace/form/src/creds.txt';

    public function saveUser($username, $password)
    {
        $data = trim($username) . ' ' . trim($password) . PHP_EOL;
        return file_put_contents($this->filename, $data, FILE_APPEND) !== false;
    }
}

