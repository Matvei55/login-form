<?php

use App\Models\Users;

class UserRegisteredEvent extends Events
{
    public Users $user;

    public function __construct(Users $user)
    {
        $this->user = $user;
    }
}