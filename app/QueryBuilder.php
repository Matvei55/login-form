<?php
namespace Ren;

use PDO;
use Ren\Database;

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

    public function __construct($pdo = null)
    {
        if ($pdo !== null) {
            $this->pdo = $pdo;
        } else {
            $this->pdo = Database::getInstance()->getConnection();
        }
    }

    public function create($data) : int {
        if(empty($this->table)){
            throw new \Exception("Имя таблицы не выбрано");
        }
        $fields= array_keys($data); // здесь я получаю название полей
        $placeholders= [];
        foreach ($fields as $field) {
            $placeholders[] = ':{$field}';
        }
        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") 
        VALUES (" . implode(',', $placeholders) . ")";

        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($data);

        $lastId = $result ? $this->pdo->lastInsertId() : false;

        $this->reset();

        return $lastId;

    }

//    public  function table($table)
//    {
//        $this->table = $table;
//        return $this;
//    }
//
//    public function select($fields = ['*'])
//    {
//        $this->fields = is_array($fields) ? $fields : func_get_args();
//        return $this;
//    }
//
//    public function where($field, $operator, $value = null)
//    {
//        if ($value === null) {
//            $value = $operator;
//            $operator = '=';
//        }
//
//        $placeholder = ':' . str_replace('.', '_', $field) . '_' . count($this->params);
//        $this->where[] = "{$field} {$operator} {$placeholder}";
//        $this->params[$placeholder] = $value;
//
//        return $this;
//    }
//
//    public function orderBy($field, $direction = 'ASC')
//    {
//        $this->orderBy[] = "{$field} {$direction}";
//        return $this;
//    }
//
//    public function limit($limit)
//    {
//        $this->limit = $limit;
//        return $this;
//    }
//
//    public function offset($offset)
//    {
//        $this->offset = $offset;
//        return $this;
//    }
//
//    public function first()
//    {
//        $this->limit(1);
//        $results = $this->get();
//        $this->reset();
//        return !empty($results) ? $results[0] : null;
//    }
//
//    public function get()
//    {
//        $sql = $this->buildSelect();
//        $stmt = $this->pdo->prepare($sql);
//        $stmt->execute($this->params);
//        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
//        $this->reset();
//        return $results;
//    }
//
//    private function buildSelect()
//    {
//        if (empty($this->table)) {
//            throw new \Exception("Table not specified. Call table() first.");
//        }
//
//        $sql = "SELECT " . implode(', ', $this->fields) . " FROM {$this->table}";
//
//        if (!empty($this->where)) {
//            $sql .= " WHERE " . implode(' AND ', $this->where);
//        }
//
//        if (!empty($this->orderBy)) {
//            $sql .= " ORDER BY " . implode(', ', $this->orderBy);
//        }
//
//        if ($this->limit !== null) {
//            $sql .= " LIMIT {$this->limit}";
//        }
//
//        if ($this->offset !== null) {
//            $sql .= " OFFSET {$this->offset}";
//        }
//
//        return $sql;
//    }
//
//    public function insert($data)
//    {
//        if (empty($this->table)) {
//            throw new \Exception("Table not specified. Call table() first.");
//        }
//
//        $fields = array_keys($data);
//        $placeholders = [];
//
//        foreach ($fields as $field) {
//            $placeholders[] = ":{$field}";
//        }
//
//        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ")
//                VALUES (" . implode(', ', $placeholders) . ")";
//
//        $stmt = $this->pdo->prepare($sql);
//        $result = $stmt->execute($data);
//        $this->reset();
//        return $result;
//    }
//
//    public function update($data)
//    {
//        if (empty($this->table)) {
//            throw new \Exception("Table not specified. Call table() first.");
//        }
//
//        $sets = [];
//        foreach (array_keys($data) as $field) {
//            $sets[] = "{$field} = :{$field}";
//        }
//
//        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets);
//
//        if (!empty($this->where)) {
//            $sql .= " WHERE " . implode(' AND ', $this->where);
//        } else {
//            throw new \Exception("UPDATE without WHERE is dangerous. Use where() first.");
//        }
//
//        $stmt = $this->pdo->prepare($sql);
//        $result = $stmt->execute(array_merge($data, $this->params));
//        $this->reset();
//        return $result;
//    }
//
//    public function delete()
//    {
//        if (empty($this->table)) {
//            throw new \Exception("Table not specified. Call table() first.");
//        }
//
//        $sql = "DELETE FROM {$this->table}";
//
//        if (!empty($this->where)) {
//            $sql .= " WHERE " . implode(' AND ', $this->where);
//        } else {
//            throw new \Exception("DELETE without WHERE is dangerous. Use where() first.");
//        }
//
//        $stmt = $this->pdo->prepare($sql);
//        $result = $stmt->execute($this->params);
//        $this->reset();
//        return $result;
//    }
//
//    public function count()
//    {
//        $fields = $this->fields;
//        $this->fields = ['COUNT(*) as count'];
//        $result = $this->first();
//        $this->fields = $fields;
//        return $result ? (int)$result['count'] : 0;
//    }
//
//    private function reset()
//    {
//        $this->table = null;
//        $this->fields = ['*'];
//        $this->where = [];
//        $this->params = [];
//        $this->orderBy = [];
//        $this->limit = null;
//        $this->offset = null;
//    }
}