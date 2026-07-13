<?php
namespace App\Events;
use App\Models\Users;

class UserRegisteredEvent extends Event
{

    public function __construct(public Users $user)
    {}
}