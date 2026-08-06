<?php
namespace App\DTO;
class LoginUserDTO
{
    public function __construct(
        public string $username,
        public string $password,
    ) {
        $this->validate();
    }
    private function validate(): void
    {
        if(empty($this->username)) {
            throw new \InvalidArgumentException("имя пользователя обязательно");
        }
        if(empty($this->password)) {
            throw new \InvalidArgumentException('пароль обязателен');
        }
    }
}