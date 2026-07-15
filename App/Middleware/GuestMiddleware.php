<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Session;

class GuestMiddleware extends Middleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next)
    {
        $session = new Session();

        if($session->has('user_id')){
            header('Location: /posts');
            exit();
        }
        return $next($request);
    }
}