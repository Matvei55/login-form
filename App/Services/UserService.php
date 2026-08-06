<?php
namespace App\Services;

use App\Models\Users;
use App\Models\Subscriptions;
use App\DTO\RegisterUserDTO;
use App\DTO\LoginUserDTO;

class UserService
{
    public function __construct(
        private Users $userModel,
        private Subscriptions $subscriptionModel,

    ) {}

    public function register(RegisterUserDTO $dto): ?array
    {
        $existing = $this->userModel->findByName($dto->username);
        if ($existing) {
            throw new \Exception("пользователь с таким именем уже существует");
        }
        $hashedPassword = password_hash($dto->password, PASSWORD_DEFAULT);
        $userId = $this->userModel->setData([
            'name' => $dto->username,
            'password' => $hashedPassword,
        ])->save();

        if($userId){
            return $this->userModel->load($userId)->getData();
        }
        return null;
    }
    public function login(LoginUserDTO $dto): ?array
    {
        $user = $this->userModel->findByName($dto->username);
        if ($user && password_verify($dto->password,  $user['password'])) {
            return $user;
        }
        return null;
    }
    public function getUser(int $userId): ?array
    {
        return $this->userModel->load($userId)->getData();
    }
    public function follow(int $followerId, int $authorId): bool
    {
        if($followerId === $authorId){
            return false;
        }
        return $this->subscriptionModel->subscribe($followerId,$authorId);
    }
    public function unfollow(int $followerId, int $authorId): bool
    {
        return $this->subscriptionModel->unsubscribe($followerId,$authorId);
    }
    public function isFollowing(int $followerId, int $authorId): bool
    {
        return $this->subscriptionModel->isSubscribed($followerId,$authorId);
    }
}