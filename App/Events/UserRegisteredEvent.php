<?php
namespace App\Events;
use App\Models\Users;

class UserRegisteredEvent extends Event
{
    public Users $user;

    public function __construct(Users $user)
    {
        $this->user = $user;
    }
}