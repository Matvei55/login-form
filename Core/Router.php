<?php
namespace Core;

class Router
{
    public function dispatch(Request $request): void
    {
        $url = $request->getUri();

        $parts = explode('/', trim($url, '/'));

        if(empty($parts[0])) {
            $controllerName = 'LoginController';
            $action = 'index';
            $params = [];
        }else{
            $controllerName = ucfirst($parts[0]) . 'Controller';
            $action = $parts[1] ?? 'index';
            $params = array_slice($parts, 2);
        }
        $controllerClass = '\\App\\' . $controllerName;

        if(!class_exists($controllerClass)) {
            $this->notFound();
            return;
        }
        $controller = new $controllerClass();
        if(!method_exists($controller, $action)) {
            $this->notFound();
            return;
        }
        $controller->$action($request, ...$params);
    }
    private function notFound(): void
    {
        http_response_code(404);
        echo "me 404";
        exit();
    }
}