<?php

namespace App\Listeners;

use App\Events\PostPendingModerationEvent;
use App\Services\TelegramService;
class SendTelegramModerationRequest extends Listener
{
    public function __construct(
        private TelegramService $telegramService
    ){}

    public function handle($event): void
    {
        if(!$event instanceof PostPendingModerationEvent){
            return;
        }
        $post = $event->post;
        $author = $event->author;

        $this->telegramService->sendPostForModeration(
            $post->getId(),
            $post->getTitle(),
            $post->getContent(),
            $author->getName()
        );
    }
}