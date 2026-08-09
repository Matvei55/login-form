<?php
require_once __DIR__ . "/../vendor/autoload.php";
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();
use App\Core\Application;
use App\Services\TelegramService;
use App\Services\PostService;

echo "Telegram Long Polling";
echo "нажми Ctrl+C чтоб остановить";
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
                    if (preg_match('/^(approve|reject)_post:(\d+)$/', $data, $matches)) {
                        $action = $matches[1];
                        $postId = (int)$matches[2];
                        try{
                            if($action === 'approve') {
                                $postService->approvedPost($postId);
                                $telegram->editMessageText($messageId, "Пост #{$postId} одобрен!!!!!!!");
                            }else{
                                $postService->rejectPost($postId);
                                $telegram->editMessageText($messageId, "Пост #{$postId} отклоне");
                            }
                            $telegram->answerCallbackQuery($callbackId, "Готово!");
                            echo date("Y-m-d H:i:s") . "Пост #{$postId}" . ($action === 'approved' ? 'одобрен' : 'отклонен') . "\n";
                        }catch (\Exception $e){
                            $telegram->answerCallbackQuery($callbackId, "ошибка:" . $e->getMessage());
                            echo "Error:" . $e->getMessage() . "\n";
                        }
                    }
                }
                $offset = $updateId+1;
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