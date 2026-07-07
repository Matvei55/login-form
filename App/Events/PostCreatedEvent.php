<?php
namespace App\Events;

use App\Models\Posts;
use App\Models\Users;

class PostCreatedEvent extends Event
{
    public Posts $post;
    public Users $user;

    public function __construct(Posts $post, Users $user)
    {
        $this->post = $post;
        $this->user = $user;
    }
}