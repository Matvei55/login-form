<?php
//
//namespace App\Listeners;
//
//use App\Events\PostPendingModerationEvent;
//use App\Services\TelegramService;
//class SendTelegramModerationRequest extends Listener
//{
//    public function __construct(
//        private TelegramService $telegramService
//    ){}
//
//    public function handle($event): void
//    {
//        if(!$event instanceof PostPendingModerationEvent){
//            return;
//        }
//        $post = $event->post;
//        $author = $event->author;
//
//        $this->telegramService->sendPostForModeration(
//            $post->getId(),
//            $post->getTitle(),
//            $post->getContent(),
//            $author->getName()
//        );
//    }
//}
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

        error_log(" [Listener] Это PostPendingModerationEvent!");

        $post = $event->post;
        $author = $event->author;

        error_log(" [Listener] Пост #" . $post->getId() . " от " . $author->getName());

        try {
            $result = $this->telegramService->sendPostForModeration(
                $post->getId(),
                $post->getTitle(),
                $post->getContent(),
                $author->getName()
            );
            error_log("✅ [Listener] Сообщение отправлено в Telegram! Ответ: " . json_encode($result));
        } catch (\Exception $e) {
            error_log(" [Listener] Ошибка при отправке в Telegram: " . $e->getMessage());
        }
    }
}