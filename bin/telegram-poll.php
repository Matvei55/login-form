<?php
require_once __DIR__ . "/../vendor/autoload.php";
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();
use App\Core\Application;
use App\Services\TelegramService;
use App\Services\PostService;

echo "Telegram Long Polling\n";
echo "нажми Ctrl+C чтоб остановить\n";

try {
    $app = Application::getInstance();
    $container = $app->getContainer();

    $telegram = $container->get(TelegramService::class);
    $postService = $container->get(PostService::class);

    $offset = 0;

    while(true)
    {
        try {
            $updates = $telegram->getUpdates($offset);
            foreach ($updates['result'] ?? [] as $update) {
                $updateId = $update['update_id'];

                if(isset($update['callback_query'])) {
                    $callback = $update['callback_query'];
                    $data = $callback['data'];
                    $messageId = $callback['message']['message_id'];
                    $callbackId = $callback['id'];

                    error_log(" [Telegram] Получен callback: " . $data);
                    error_log(" [Telegram] Полный callback: " . json_encode($callback));

                    if (preg_match('/^(approve|reject)_post:(\d+)$/', $data, $matches)) {
                        $action = $matches[1];
                        $postId = (int)$matches[2];
                        error_log(" [Telegram] action: $action, postId: $postId");

                        try{
                            if($action === 'approve') {
                                $postService->approvedPost($postId);
                                $telegram->editMessageText($messageId, " Пост #{$postId} одобрен!");
                            }else{
                                $postService->rejectPost($postId);
                                $telegram->editMessageText($messageId, " Пост #{$postId} отклонен");
                            }

                            try {
                                $telegram->answerCallbackQuery($callbackId, "Готово!");
                            } catch (\Exception $e) {
                                if (strpos($e->getMessage(), 'query is too old') !== false) {
                                    error_log("[Telegram] Callback устарел (нажато слишком поздно), но пост обработан");
                                } else {
                                    throw $e;
                                }
                            }

                            echo date("Y-m-d H:i:s") . " Пост #{$postId} " . ($action === 'approve' ? 'одобрен' : 'отклонен') . "\n";

                        }catch (\Exception $e){
                            error_log("[Telegram] Ошибка: " . $e->getMessage());
                            echo "Error:" . $e->getMessage() . "\n";
                        }
                    }
                }
                $offset = $updateId + 1;
            }
        }catch (\Exception $e){
            echo "Error:" . $e->getMessage() . "\n";
            sleep(5);
        }
    }
}catch(\Exception $e){
    echo "fatal error:" . $e->getMessage() . "\n";
    exit(1);
}