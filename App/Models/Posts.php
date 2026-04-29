<?php
namespace App\Models;
use App\QueryBuilder;
use App\Models\Model;
use Couchbase\User;
use PDO;
use App\Models\AbstractModel;

class Posts extends AbstractModel implements Model
{
    private string $table = 'posts';


    public function save()
    {
        $pdo = $this->builder->getPdo();

        if ($this->id !== null) {
            [$sql, $params] = $this->builder->table($this->table)->
                where('id', $this->id)->getUpdateSQL($this->data);
            $stmt = $pdo->prepare($sql);
            return $stmt->execute($params);
        }
        [$sql, $params] = $this->builder->table($this->table)->
            getInsertSQL($this->data);

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($params);

        return $result ? $pdo->lastInsertId() : false;
    }

    public function load(?int $id = null, bool $all = false): ?array
    {
        $pdo = $this->builder->getPdo();
        $builder = $this->builder->table($this->table);

        if ($id !== null) {
            $builder->where('id', $id);
            [$sql, $params] = $builder->getSelectSQL();
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        }
        [$sql, $params] = $builder->getSelectSQL();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result ? $result : ($result[0] ?? null);
    }

    public function delete(): bool
    {
        $pdo = $this->builder->getPdo();

        [$sql, $params] = $this->builder->table($this->table)->
            where('id', $this->id)->getDeleteSQL();

        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function getAuthor(int $postId): ?array  //получить автора поста
    {
        $pdo = $this->builder->getPdo();
        $sql = "SELECT users.* FROM users INNER JOIN posts ON users.id = posts.user_id WHERE posts.id = :postId";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['postId' => $postId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result : null;

    }

    public function getPostWithAuthor(int $postId): ?array //пост и атвор
    {
        $pdo = $this->builder->getPdo();
        $sql = "SELECT posts.*. user.name as author_name. FROM posts INNER JOIN users ON posts.user_id = users.id WHERE posts.id = :postId";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['postId' => $postId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}







