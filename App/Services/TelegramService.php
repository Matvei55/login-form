<?php
namespace App\Services;
use App\Core\Config;
use App\Core\HttpClient;
class TelegramService
{
    private string $apiUrl; //юрл для всех запросов к тг
    private string $chatId; //id чата куда будут приходить уведомления

    public function __construct(
        private HttpClient $httpClient,
        private Config $config,
    ){
        $token = $this->config->get('telegram.token'); //читает юрл
        $this->apiUrl = "https://api.telegram.org/bot{$token}"; //формирует юрл
        $this->chatId = $this->config->get('telegram.chat_id'); //читает id
    }

    public function sendMessage(string $text,array $keyboard = null): array //отправить сообщение
    {
        $data = [
            'chat_id' => $this->chatId, //кому отправить
            'text' => $text, //текст сообщения
            'parse_mode' => 'HTML', //форматирование
        ];
        if ($keyboard) { //если есть клавиатура, добавляем ее в данные
            $data['reply_markup'] = json_encode($keyboard);
        }
        return $this->httpClient->post($this->apiUrl . '/sendMessage', $data);//отправляем запрос к тг апи и получаем ответ
    }
    public function sendPostForModeration(int $postId, string $title, string $content,string $author): array //отправить пост на модерацию
    {
        $text = "<b>новый пост на модерацию</b>\n\n";
        $text .= "<b>ID:</b> {$postId}\n";
        $text .= "<b>Автор:</b> {$author}\n";
        $text .="<b>Заголовок:</b> {$title}\n\n";
        $text .= "<b>Содержание:</b>\n . mb_substr($content, 0, 250) . (mb_strlen($content) > 200 ? '...' : '');";
        $keyboard = [
            'inline_keyboard' => [ //клавиатура с двумя кнопками
                [
                    [
                        'text' => 'Одобрить',
                        'callback_data' => "approve_post:{post_id}",
                    ],
                    [
                        'text' => 'Отклонить',
                        'callback_data' => "reject_post:{post_id}",
                    ]
                ]
            ]
        ];
        return $this->sendMessage($text, $keyboard);
    }
    public function editMessageText(int $messageId, string $text): array //обновить сообщение(меняет текст и убирает кнопки)
    {
        $data = [
            'chat_id' => $this->chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode(['inline_keyboard' => []])
        ];
        return $this->httpClient->post($this->apiUrl . '/editMessageText', $data);
    }
    public function answerCallbackQuery(array $callbackQueryId, string $text = ''): array //отвечает за нажатие кнопки
    {
        $data = [
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
        ];
        return $this->httpClient->post($this->apiUrl . '/answerCallbackQuery', $data);
    }
    public function getUpdates(int $offset = 0, int $timeout = 60): array
    {
        $data = [
            'offset' => $offset, //айд последнего обработанного события +1
            'timeout' => $timeout,//сколько секунд ждать новых событий
            'allowed_updates' => ['callback_query'],//получать только нажатия кнопок
        ];
        return $this->httpClient->post($this->apiUrl . '/getUpdates', $data);
    }
}