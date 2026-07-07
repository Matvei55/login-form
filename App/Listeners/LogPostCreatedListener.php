<?php
namespace App\Listeners;

use App\Events\PostCreatedEvent;

class LogPostCreatedListener extends Listener
{
    public function handle($event): void
    {
        if(!$event instanceof PostCreatedEvent) {
            return;
        }
        error_log("[событие] пост №{$event->post->getId()} создан пользователем {$event->user->getName()}");
        error_log("название:" . $event->post->getTitle());
    }
}