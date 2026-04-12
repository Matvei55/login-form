<?php
namespace app\Models;
use app\QueryBuilder;
use app\Models\Model;
use PDO;

class Posts implements Model
{
    private QueryBuilder $builder;
    private string $table = 'posts';

    public function __construct(QueryBuilder $builder)
    {
        $this->builder = $builder;
    }

    public function save(array $data, ?int $id = null)
    {
        $pdo = $this->builder->getPdo();

        if ($id !== null) {
            [$sql, $params] = $this->builder->table($this->table)->
                where('id', $id)->getUpdateSQL($data);
            $stmt = $pdo->prepare($sql);
            return $stmt->execute($params);
        }
        [$sql, $params] = $this->builder->table($this->table)->
            getInsertSQL($data);

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

    public function delete(int $id): bool
    {
        $pdo = $this->builder->getPdo();

        [$sql, $params] = $this->builder->table($this->table)->
            where('id', $id)->getDeleteSQL();

        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }
}