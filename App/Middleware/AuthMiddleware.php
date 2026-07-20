<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Session;
use App\Models\Tags;
use Closure;

class AuthMiddleware extends Middleware implements MiddlewareInterface
{

    public function __construct(private Session $session)
    {}
    public function handle(Request $request, callable $next)
    {
        if(!$this->session->has('user_id')){
            header('Location: /login');
            exit();
        }
        return $next($request);
    }
}