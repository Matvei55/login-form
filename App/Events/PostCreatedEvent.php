<?php
namespace App\Events;

use App\Models\Posts;
use App\Models\Users;

class PostCreatedEvent extends Event
{

    public function __construct(public Posts $post, public Users $user)
    {}
}