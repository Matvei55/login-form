<?php

namespace App\Events;

use App\Models\Posts;
use App\Models\Users;
class PostPendingModerationEvent
{
    public function __construct(
        public Posts $post,
        public Users $author,
    ){}
}