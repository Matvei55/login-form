<?php
namespace App\Listeners;

use App\Events\ModelSavedEvent;
use App\Models\Users;

class LogUserRegisteredListener extends Listener
{
    public function handle($event): void
    {
        if (!$event instanceof ModelSavedEvent) {
            return;
        }
        $model = $event->model;
        if (!$model instanceof Users) {
            return;
        }
        error_log("📝 пользователь  #{$model->getId()} сохранён");
    }
}