<?php
namespace App\Models;
use App\Models\Model;
use App\QueryBuilder;
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
        var_dump($params);
        var_dump($sql);
        $stmt = $pdo->prepare($sql); //подготовка скл к выполнению
        $result=  $stmt->execute($params);

        return $result ? $pdo->lastInsertId() : false; //если результат тру , то возвращает айд
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

    public function getPosts(?int $userID = null): array
    {
        $pdo = $this->builder->getPDO();
        $id = $userID ?? $this->id;

        if(!$id)
        {
            return [];
        }

        [$sql, $params] = $this->builder
            ->table('posts')
            ->where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->getSelectSQL();

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function countPosts(?int $userID = null): int
    {
        $pdo = $this->builder->getPDO();
        $id = $userID ?? $this->id;

        if(!$id){
            return 0;
        }
        $sql = "SELECT COUNT(*) as count FROM posts WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['user_id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return ((int)$result['count']??0);
    }

    public function getLastPosts(?int $userID = null, int $limit): array //последние посты
    {
        $pdo = $this->builder->getPDO();
        $id = $userID ?? $this->id;
        if(!$id){
            return [];
        }

        [$sql, $params] = $this->builder->table('posts')
            ->where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->getSelectSQL();

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserWithPosts(int $userId): ?array //пользователь и его посты
    {
        $user = $this->load($userId);

        if(!$user){
            return null;
        }

        $user['posts'] = $this->getPosts($userId);
        return $user;
    }

    public function hasPosts(?int $userID = null): bool //проверка на наличее постов
    {
        return $this->countPosts($userID) > 0;
    }

    public function deleteAllPosts(?int $userID = null): bool
    {
        $pdo = $this->builder->getPDO();
        $id = $userID ?? $this->id;

        if(!$id){
            return false;
        }

        $sql = "DELETE FROM posts WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute(['user_id' => $id]);
    }
}



