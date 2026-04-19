<?php

namespace App\Models;

use App\QueryBuilder;

abstract class AbstractModel
{

    public QueryBuilder $builder;

    public function __construct(QueryBuilder $builder)
    {
        $this->init();
    }
    public function init()
    {
        $this->builder = new QueryBuilder();
    }
}