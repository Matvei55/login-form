<?php
namespace App\Models;
use App\Models\Model;
use App\QueryBuilder;
use http\Params;
use PDO;
use App\Models\AbstractModel;

class Users extends AbstractModel implements Model
{
    private string $table = 'users';//здесь имя таблицы

    public function save()
    {
        if($this->id !== null){
            $result = $this->builder
                ->table($this->table)
                ->where('id', $this->id)
                ->update($this->data);
            return $result;
        }
        $newId = $this->builder
            ->table($this->table)
            ->insert($this->data);

        if($newId){
            $this->id = $newId;
            $this->data['id'] = $newId;
            return $newId;
        }
        return false;
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
        if($this->id){
            return [];
        }
        return $this->builder
            ->table($this->table)
            ->where('id', $this->id)
            ->orderBy('created_at', 'DESC')
            ->fetchAll();
    }

    public function countPosts(): int
    {
        if(!$this->id){
            return 0;
        }
        return $this->builder
            ->table($this->table)
            ->where('id', $this->id)
            ->count();
    }

    public function getLastPosts(int $limit): array //последние посты
    {
        if(!$this->id){
            return [];
        }
        return $this->builder
            ->table($this->table)
            ->where('id', $this->id)
            ->orderBy('created_at', 'DESC')
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
            ->table($this->table)
            ->where('id', $this->id)
            ->delete();
    }
}



