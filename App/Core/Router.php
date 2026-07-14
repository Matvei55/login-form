<?php
namespace App\Core;
use App\Middleware\GuestMiddleware;
use App\Middleware\MiddlewareDispatcher;
class Router
{

    public function __construct(private Request $request)
    {}

    public function dispatch(): void
    {
        $url = $this->request->getUri();
        $method = $this->request->getMethod();

        $url = strtok($url, '?');
        $parts = explode('/', trim($url, '/'));

        if(isset($parts[0]) && $parts[0] === 'logout') {
            $controllerName = 'LogoutController';
            $action = 'index';
            $params = [];
        }elseif (empty($parts[0]) || $parts[0] === '') {
            $controllerName = 'LoginController';
            $action = 'index';
            $params = [];
        } else {
            $controllerName = ucfirst($parts[0]) . 'Controller';
            $action = $parts[1] ?? 'index';
            $params = array_slice($parts, 2);
        }

        $controllerClass = '\\App\\Controllers\\' . $controllerName;

        if (!class_exists($controllerClass)) {
            $this->notFound();
            return;
        }

        $controller = Application::getInstance()
            ->getContainer()
            ->get($controllerClass);

        if (!method_exists($controller, $action)) {
            $this->notFound();
            return;
        }

        $middlewares = $this->getMiddlewareForRoute($controller, $action);

        $dispatcher = $container->make(MiddlewareDispatcher::class, ['middlewares' => $middlewares]);
        $dispatcher->handle($this->request, function ($request) use ($controller,$action,$params) {
            return $controller->$action($request, ...$params);
        });
    }

    private function getMiddlewareForRoute(Controller $controller, string $action):array
    {
        $controllerMiddlewares = $controller->getMiddlewareForAction($action);

        if(empty($controllerMiddlewares)){
            $controllerName = get_class($controller);
            $controllerName= basename(str_replace('\\', '/', $controllerName));

            if($controllerName === 'LogoutController'){
                return [];
            }

            $questRoutes = [
                'LoginController' => ['index', 'store'],
                'RegisterController' => ['index', 'store'],
            ];
            if(isset($questRoutes[controllerName]) && in_array($action,$questRoutes[$controllerName])){
                return [GuestMiddleware::class];
            }
            return [AuthMiddleware::class];
        }
        return $controllerMiddlewares;
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo "404 - Страница не найдена";
        exit();
    }
}
