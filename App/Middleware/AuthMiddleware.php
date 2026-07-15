<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Session;
use Closure;

class AuthMiddleware extends Middleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next)
    {
        $session = new Session();
        if(!$session->has('user_id')){
            header('Location: /login');
            exit();
        }
        return $next($request);
    }
}