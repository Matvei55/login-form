<?php

namespace App\Models;

use App\Core\QueryBuilder;

abstract class AbstractModel
{
    protected array $data = [];
    protected ?int $id = null;
    public QueryBuilder $builder;

    public function __construct()
    {
        $this->init();
    }
    public function init()
    {
        $this->builder = new QueryBuilder();
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    public function getId():?int
    {
        if ($this->id === null) {
            return $this->id;
        }
        return $this->data['id'] ?? null;
    }

    public function setId(int $id): self
    {
    $this->id = $id;
    $this->data['id'] = $id;
    return $this;
    }

    protected function saveAfter():void
    {

    }

    protected function saveBefore():void
    {

    }

    public function save()
    {
        $this->saveBefore();
        if($this->id !== null) {
            $result = $this->builder
                ->table($this->getTable())
                ->where('id', $this->id)
                ->update($this->data);
        }else{
            $newId = $this->builder
                ->table($this->getTable())
                ->insert($this->data);
            if($newId){
                $this->id = $newId;
                $this->data['id'] = $newId;
                $result = $newId;
            }else{
                $result = false;
            }
        }
        return $result;
    }

    abstract protected function getTable(): string;
}