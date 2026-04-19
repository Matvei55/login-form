<?php
namespace App\Models;
use App\QueryBuilder;
use App\Models\Model;
use PDO;
use App\Models\AbstractModel;

class Posts extends AbstractModel implements Model
{
    private string $table = 'posts';

    private array $data = [];
    private int $id;


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
}