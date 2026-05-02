<?php
namespace App;

use PDO;
use App\Database;

class QueryBuilder
{
    private $pdo; //объект подключения к бд
    private $table; //имя таблицы
    private $fields = ['*']; //поля для выборки
    private $where = []; //условие where
    private $params = []; //параметры для подготовленных запросов
    private $orderBy = []; //сортировка
    private $limit = null; //лимит записей
    private $offset = null; //смещение пагинации

    private $joins = [];

    public function __construct($pdo = null)
    {
        if ($pdo !== null) {
            $this->pdo = $pdo;
        } else {
            $this->pdo = Database::getInstance()->getConnection();
        }
    }

    public function fetchOne(): ?array
    {
        [$sql, $params] = $this->getSelectSQL();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function fetchAll(): ?array
    {
        [$sql, $params] = $this->getSelectSQL();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert(array $data): int|bool
    {
        [$sql, $params] = $this->getInsertSQL($data);
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($params);
        return $result ? $this->pdo->lastInsertId() : false;
    }

    public function update(array $data): int|bool
    {
        [$sql, $params] = $this->getUpdateSQL($data);
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(): bool
    {
        [$sql, $params] = $this->getDeleteSQL();
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function count(): int
    {
        [$sql, $params] = $this->getCountSQL();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['count'] ?? 0);
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
    public  function table($table) //выбор таблицы
    {
        $this->table = $table;
        return $this;
    }

    public function select($fields = ['*']) //выбор полей
    {
        $this->fields = is_array($fields) ? $fields : func_get_args();
        return $this;
    }

    public function where($field, $operator, $value = null) //условие where
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $placeholder = ':' . str_replace('.', '_', $field) . '_' . count($this->params);
        $this->where[] = "{$field} {$operator} {$placeholder}";
        $this->params[$placeholder] = $value;

        return $this;
    }

    public function orderBy($field, $direction = 'ASC') //сортировка
    {
        $this->orderBy[] = "{$field} {$direction}";
        return $this;
    }

    public function limit($limit) //ограничение записей
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset($offset) //пагинация
    {
        $this->offset = $offset;
        return $this;
    }

    public function getSelectSQL(): array
    {
           $sql = $this->buildSelect();
    if (!empty($this->joins)) {
        $sql = str_replace(
            "FROM {$this->table}",
            "FROM {$this->table} " . implode(' ', $this->joins),
            $sql
        );
    }

    $params = $this->params;
    $this->clear();

    return [$sql, $params];
    }

    public function getInsertSQL(array $data): array
    {
    $fields = array_keys($data);
    $placeholders = array_map(fn($field) => ":{$field}", $fields);

    $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
            VALUES (" . implode(', ', $placeholders) . ")";

    return [$sql, $data];
    }

    public function getUpdateSQL(array $data): array
    {
        if (empty($this->where)) {
            throw new \Exception("UPDATE without WHERE is dangerous");
        }

        $sets = [];
        foreach (array_keys($data) as $field) {
            $sets[] = "{$field} = :{$field}";
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets);
        $sql .= " WHERE " . implode(' AND ', $this->where);

        $params = array_merge($data, $this->params);

        return [$sql, $params];
    }

    public function getDeleteSQL(): array
    {
    if (empty($this->where)) {
        throw new \Exception("DELETE without WHERE is dangerous");
    }

    $sql = "DELETE FROM {$this->table} WHERE " . implode(' AND ', $this->where);

    return [$sql, $this->params];
    }
    public function join(string $table, string $first, string $operator, string $second): self
    {
    $this->joins[] = "INNER JOIN {$table} ON {$first} {$operator} {$second}";
    return $this;
    }


    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
    $this->joins[] = "LEFT JOIN {$table} ON {$first} {$operator} {$second}";
    return $this;
    }


    public function rightJoin(string $table, string $first, string $operator, string $second): self
    {
    $this->joins[] = "RIGHT JOIN {$table} ON {$first} {$operator} {$second}";
    return $this;
    }
    private function buildSelect() //построение запроса
    {
        if (empty($this->table)) {
            throw new \Exception("Table not specified. Call table() first.");
        }

        $sql = "SELECT " . implode(', ', $this->fields) . " FROM {$this->table}";

        if (!empty($this->joins)) {
            $sql .= " " . implode(' ', $this->joins);
        }

        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }

        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY " . implode(', ', $this->orderBy);
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }

        return $sql;
    }
    public function getCountSQL() //подсччет записей
    {
         $oldFields = $this->fields;
        $this->fields = ['COUNT(*) as count'];

        $sql = $this->buildSelect();
        $params = $this->params;


        $this->fields = $oldFields;
        $this->clear();

        return [$sql, $params];
    }

    private function clear() //очистка состояния
    {
        $this->table = null;
        $this->fields = ['*'];
        $this->where = [];
        $this->params = [];
        $this->orderBy = [];
        $this->limit = null;
        $this->offset = null;
        $this->joins = [];
    }
}