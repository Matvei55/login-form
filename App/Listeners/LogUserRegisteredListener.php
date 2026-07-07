<?php
namespace App\Listeners;

use App\Events\UserRegisteredEvent;

class LogUserRegisteredListener extends Listener
{
    public function handle($event): void
    {
        if(!$event instanceof UserRegisteredEvent){
            return;
        }
        $userData = $event->user->getData();
        $username = $userData['name'] ?? 'неизвестно';
        $userId = $event->user->getId() ?? 'новый';

        error_log("[СОБЫТИЕ] Зарегистрирован новый пользователь: '$username'");
    }
}