<?php

namespace App\Models;

use App\QueryBuilder;

abstract class AbstractModel
{
    protected array $data = [];
    protected int $id;
    public QueryBuilder $builder;

    public function __construct(QueryBuilder $builder)
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
        $this->data = $data;
        return $this;
    }
}