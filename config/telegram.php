<?php
return [
    'token' => getenv('TELEGRAM_TOKEN') ?: '',
    'chat_id' => getenv('TELEGRAM_CHAT_ID') ?: '',
];