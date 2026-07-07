<?php
namespace App\Events;

use App\Models\Users;
use App\Models\Posts;

class PostCreatedEvent extends Events
{
    public Posts $post
    public Users $user;

    public function __construct(Posts $post, Users $user)
    {
        $this->post = $post;
        $this->user - $user;
    }
}

