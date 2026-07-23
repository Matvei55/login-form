<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Session;

class GuestMiddleware extends Middleware implements MiddlewareInterface
{
    public function __construct(private Session $session)
    {}
    public function handle(Request $request, callable $next)
    {
        if($this->session->has('user_id')){
            header('Location: /posts');
            exit();
        }
        return $next($request);
    }
}