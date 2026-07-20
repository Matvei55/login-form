<?php
namespace App\Models;
use App\Core\QueryBuilder;

class Users extends AbstractModel implements Model
{
    private string $table = 'users';//здесь имя таблицы

    public function __construct(QueryBuilder $builder)
    {
        parent::__construct($builder);
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
}