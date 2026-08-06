<?php
namespace App\Models;
use App\Core\Application;
use App\Core\QueryBuilder;
use App\Core\EventDispatcherInterface;

class Users extends AbstractModel implements Model
{
    private string $table = 'users';//здесь имя таблицы

    public function __construct(
        QueryBuilder $builder,
        EventDispatcherInterface $dispatcher,
        private Subscriptions $subscriptionModel,
    ){
        parent::__construct($builder, $dispatcher);
    }
    protected function saveAfter():void
    {
        error_log("пользователь {$this->id} сохранен");
    }

    protected function getTable(): string
    {
     return $this->table;
    }
    public function load(?int $id = null): self
    {
        if($id !== null){
            $result = $this->builder
                ->table($this->table)
                ->where('id', $id)
                ->fetchOne();

            $this->data = $result ?: [];

            if($this->data){
               $this->id = $this->data['id'] ?? null;
            }
        }
        return $this;
    }

    public function delete(): bool
    {
        $result = $this->builder
            ->table($this->table)
            ->where('id', $this->id)
            ->delete();

        if($result){
            $this->data = [];
            $this->id = null;
        }
        return $result;
    }

    public function getPosts(): array
    {
        if(!$this->id){
            return [];
        }
        return $this->builder
            ->table('posts')
            ->where('user_id', $this->id)
            ->fetchAll();
    }

    public function countPosts(): int
    {
        if(!$this->id){
            return 0;
        }
        return $this->builder
            ->table('posts')
            ->where('user_id', $this->id)
            ->count();
    }

    public function getLastPosts(int $limit): array //последние посты
    {
        if(!$this->id){
            return [];
        }
        return $this->builder
            ->table('posts')
            ->where('user_id', $this->id)
            ->limit($limit)
            ->fetchAll();
    }

    public function getUserWithPosts(): ?array //пользователь и его посты
    {
        if(!$this->id){
            return null;
        }
        $this->load($this->id);
        if(!$this->getData()){
            return null;
        }
        $userData = $this->getData();
        $userData['posts'] = $this->getPosts();
        return $userData;
    }

    public function hasPosts(): bool //проверка на наличее постов
    {
        return $this->countPosts() > 0;
    }

    public function deleteAllPosts(): bool
    {
        if(!$this->id){
            return false;
        }
        return $this->builder
            ->table('posts')
            ->where('user_id', $this->id)
            ->delete();
    }

    public function findByName(string $name): ?array
    {
        $result = $this->builder
            ->table($this->table)
            ->where('name', $name)
            ->fetchOne();
        return $result ;
    }
    public function getName(): string
    {
        return $this->data['name'] ?? '';
    }

    public function getFollowers(): array
    {
        if (!$this->id) {
            return [];
        }

        $result = $this->subscriptionModel->getFollowers($this->id);
        $followers = [];
        foreach ($result as $row) {
            $user = new static(
                $this->builder,
                $this->dispatcher,
                $this->subscriptionModel,
            );
            $user->load($row['follower_id']);
            $followers[] = $user;
        }
        return $followers;
    }

    public function getFollowing(): array
    {
        if (!$this->id) {
            return [];
        }
        $result = $this->subscriptionModel->getFollowing($this->id);
        $following = [];
        foreach ($result as $row) {
            $user = new static(
                $this->builder,
                $this->dispatcher,
                $this->subscriptionModel,
            );
            $user->load($row['author_id']);
            $following[] = $user;
        }
        return $following;
    }
    public function isFollowing(int $authorId): bool
    {
        if (!$this->id) {
            return false;
        }
        return $this->subscriptionModel->isSubscribed($this->id, $authorId);
    }
    public function follow(int $authorId): bool
    {
        if(!$this->id || $this->id === $authorId){
            return false;
        }
        return $this->subscriptionModel->subscribe($this->id, $authorId);
    }
    public function unfollow(int $authorId): bool
    {
        if(!$this->id){
            return false;
        }
        return $this->subscriptionModel->unsubscribe($this->id, $authorId);
    }
    public function getFollowersCount(): int
    {
        if (!$this->id) {
            return 0;
        }
        return $this->builder
            ->table('subscriptions')
            ->where('author_id', $this->id)
            ->count();
    }
    public function getFollowingCount(): int
    {
        if (!$this->id) {
            return 0;
        }
        return $this->builder
            ->table('subscriptions')
            ->where('follower_id', $this->id)
            ->count();
    }

    public function getComments():array
    {
        if (!$this->id) {
            return [];
        }
        return $this->builder
            ->table('comments')
            ->where('user_id', $this->id)
            ->orderBy('created_at', 'DESC')
            ->fetchAll();
    }
    public function getCommentsCount(): int
    {
        if (!$this->id) {
            return 0;
        }
        return $this->builder
            ->table('comments')
            ->where('user_id', $this->id)
            ->count();
    }

    public function getRole():string
    {
        return $this->data['role'] ?? 'user';
    }
    public function isAdmin(): bool
    {
        return $this->getRole() === 'admin';
    }
    public function isModerator(): bool
    {
        return $this->getRole() === 'moderator';
    }
    public function isUser(): bool
    {
        return $this->getRole() === 'user';
    }
}
//1