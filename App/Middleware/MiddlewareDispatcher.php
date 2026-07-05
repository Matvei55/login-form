<?php
namespace App\Middleware;

use App\Core\Request;

class MiddlewareDispatcher
{
    private array $middlewares = [];

    public function __construct(array $middlewares = [])
    {
        $this->middlewares = $middlewares;
    }

    public function add(string $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }
    public function handle(Request $request, callable $controller):void
    {
        $next = function ($request) use ($controller) {
            return $controller($request);
        };
        foreach (array_reverse($this->middlewares) as $middlewareClass) {
            $middleware = new $middlewareClass();

            $next = function ($request) use ($middleware, $next) {
                return $middleware->handle($request, $next);
            };
        }
        $next($request);
    }
}