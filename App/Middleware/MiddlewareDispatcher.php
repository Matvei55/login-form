<?php
namespace App\Middleware;

use App\Core\Request;
use App\Container\ContainerInterface;
use App\Middleware\GuestMiddleware;
use App\Middleware\AuthMiddleware;

class MiddlewareDispatcher
{
    

    public function __construct(private ContainerInterface $container,private array $middlewares = [])
    {}

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
            $middleware = $this->container->get($middlewareClass);

            $next = function ($request) use ($middleware, $next) {
                return $middleware->handle($request, $next);
            };
        }
        $next($request);
    }
}