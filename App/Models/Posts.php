<?php
namespace App\Models;
use App\Models\Users;
use App\QueryBuilder;
use App\Models\Model;
use App\Models\Tags;
use PDO;
use App\Models\AbstractModel;

class Posts extends AbstractModel implements Model
{
    private string $table = 'posts';
    private ?Users  $user = null;


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

            if ($this->data) {
                $this->id = $this->data['id'] ?? null;
            }
        }
        return $this;
    }

    public function delete(): bool
    {
        $pdo = $this->builder->getPdo();

        [$sql, $params] = $this->builder->table($this->table)->
        where('id', $this->id)->getDeleteSQL();

        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }


//
    public function getPostWithAuthor(): ?array //пост и атвор
    {


    }

    public function setUser(Users $user): self
    {
        if ($user->getId() === null) {
            throw new \RuntimeException("пользователь не создан");
        }
        $this->user = $user;
        $this->data['user_id'] = $user->getId();
        return $this;
    }

    public function getUser(): Users
    {
        if($this->user === null) {
            $user = new Users();
            $user->load($this->data['user_id']);
            $this->user = $user;
        }
       return $this->user;
    }
}






