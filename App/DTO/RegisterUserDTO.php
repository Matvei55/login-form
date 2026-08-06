<?php
namespace App\DTO;

class RegisterUserDTO
{
    public function __construct(
        public string $username,
        public string $password,
    ) {
        $this->validate();
    }
    private function validate(): void
    {
        if(strlen($this->username) < 3) {
            throw new \InvalidArgumentException('имя пользователя должно быть минимум 3 символа');
        }
        if(strlen($this->password) <6) {
            throw new \InvalidArgumentException('пароль должен быть минимум 3 символа');
        }
    }
}