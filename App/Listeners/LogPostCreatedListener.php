<?php
namespace App\Listeners;

use App\Models\Posts;
use App\Events\ModelSavedEvent;

class LogPostCreatedListener extends Listener
{
    public function handle($event): void
    {
        if(!$event instanceof ModelSavedEvent) {
            return;
        }
        $model = $event->model;
        if(!$model instanceof Posts) {
            return;
        }
        error_log("Пост {$model->getId()} сохранен");
        error_log("название:" . ($model->getTitle()));
    }
}