<?php
namespace App\Events;
use App\Models\AbstractModel;

class ModelSavedEvent extends Event
{
    public function __construct(
        public AbstractModel $model
    )
    {}
}