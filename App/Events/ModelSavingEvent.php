<?php
namespace App\Events;
use App\Models\AbstractModel;

class ModelSavingEvent extends Event
{
    public function __construct(
        public AbstractModel $model
    )
    {}
}