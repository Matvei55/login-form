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
        $pdo = $this->builder->getPDO(); //пдо объект

        if ($this->id !== null) { //если айди передан то нужно обновить запись
            [$sql, $params] = $this->builder->
                table($this->table)->where('id', $this->id)->getUpdateSQL($this->data); //строит скл для обновления

            $stmt = $pdo->prepare($sql); //подготовка скл к выполнению
            return $stmt->execute($params);
        }
        [$sql, $params] = $this->builder->table($this->table)->getInsertSQL($this->data); //строит скл для создания записи
//        var_dump($params);
//        var_dump($sql);
        $stmt = $pdo->prepare($sql); //подготовка скл к выполнению
        $result=  $stmt->execute($params);

        if($result){
            $this->id = (int)$pdo->lastInsertId();
            $this->data['id'] = $this->id;
            return $this->id;
        }
        return false;
    }

    public function load(?int $id = null): self
    {
        if ($id !== null) {
            $pdo = $this->builder->getPDO();

            [$sql, $params] = $this->builder
                ->table($this->table)
                ->where('id', $id)
                ->getSelectSQL();
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->data = $result ?: [];

            if($this->data){
                $this->id = $this->data['id'] ?? null;
            }
        }
        return $this;
    }

    public function delete(): bool
    {
        $pdo = $this->builder->getPDO();

        [$sql, $params] = $this->builder->
        table($this->table)->where('id', $this->id)->getDeleteSQL(); //строим скл

        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function getPosts(): array
    {
        $pdo = $this->builder->getPDO();

        if(!$this->id)
        {
            return [];
        }

        [$sql, $params] = $this->builder
            ->table('posts')
            ->where('user_id', $this->id)
            ->orderBy('created_at', 'desc')
            ->getSelectSQL();

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function countPosts(): int
    {


        if(!$this->id){
            return 0;
        }
        $pdo = $this->builder->getPDO();
        [$sql, $params] = $this->builder
            ->table('posts')
            ->where('user_id', $this->id)
            ->getCountSQL();
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['user_id' => $this->id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)($result['count']??0);
    }

    public function getLastPosts(int $limit): array //последние посты
    {
        $pdo = $this->builder->getPDO();
        if(!$this->id){
            return [];
        }

        [$sql, $params] = $this->builder->table('posts')
            ->where('user_id', $this->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->getSelectSQL();

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserWithPosts(): ?array //пользователь и его посты
    {

        if(!$this->id){
            return null;
        }
        $user = $this->load($this->id);
        if(!$user->getData()) {
            return null;
        }
        $userData = $user->getData();
        $userData['posts'] = $this->getPosts();
        return $userData;
    }

    public function hasPosts(): bool //проверка на наличее постов
    {
        return $this->countPosts($this->id) > 0;
    }

    public function deleteAllPosts(): bool
    {

        if(!$this->id){
            return false;
        }
        $pdo= $this->builder->getPDO();
        [$sql,$params] = $this->builder
            ->table('posts')
            ->where('user_id', $this->id)
            ->getDeleteSQL();
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }
}



