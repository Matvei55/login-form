<?php
namespace app\Models;
use app\Models\Model;
use app\QueryBuilder;
use PDO;

class Users implements Model
{
    private QueryBuilder $builder; //тут стро. sql
    private string $table = 'users';//здесь имя таблицы


    public function __construct(QueryBuilder $builder)
    {
        $this->builder = $builder;
    }

    public function save(array $data, ?int $id = null)
    {
        $pdo = $this->builder->getPDO(); //пдо объект

        if ($id !== null) { //если айди передан то нужно обновить запись
            [$sql, $params] = $this->builder->
                table($this->table)->where('id', $id)->getUpdateSQL($data); //строит скл для обновления

            $stmt = $pdo->prepare($sql); //подготовка скл к выполнению
            return $stmt->execute($params);
        }
        [$sql, $params] = $this->builder->table($this->table)->getInsertSQL($data); //строит скл для создания записи

        $stmt = $pdo->prepare($sql); //подготовка скл к выполнению
        $result=  $stmt->execute($params);

        return $result ? $pdo->lastInsertId() : false; //если результат тру , то возвращает айд
    }

    public function load(?int $id = null, bool $all = false): ?array //айд записи и все записи
    {
        $pdo = $this->builder->getPDO(); //получаем PDO
        $builder = $this->builder->table($this->table); //передаем название таблицы
        if ($id !== null) {  //выводим строку по id и условию where
            $builder->where('id', $id);
            [$sql, $params] = $builder->getSelectSQL();

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?:null; //вернет массив данных
        }

        [$sql, $params] = $builder->getSelectSQL(); //загрузка всех пользователей
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $result = $stmt->fetchALL(PDO::FETCH_ASSOC);

        return $all ? $result : ($result[0]??null); //Если $result[0] существует и не равен null → верни $result[0] Если $result[0] не существует или равен null → верни null

    }

    public function delete(int $id): bool
    {
        $pdo = $this->builder->getPDO();

        [$sql, $params] = $this->builder->
        table($this->table)->where('id', $id)->getDeleteSQL(); //строим скл

        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }
}



