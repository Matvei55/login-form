<?php
namespace App\Middleware;

use App\Core\Request;

abstract class Middleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next)
    {
        return $next($request);
    }
}