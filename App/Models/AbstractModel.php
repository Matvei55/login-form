<?php

namespace App\Models;

use App\QueryBuilder;

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
}