<?php
namespace App\Core;
class Router
{
    private Request $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function dispatch(): void
    {
        $url = $this->request->getUri();

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
        $controllerClass = '\\App\\Controllers\\' . $controllerName;

        if(!class_exists($controllerClass)) {
            $this->notFound();
            return;
        }
        $controller = Application::getInstance()
            ->getContainer()
            ->get($controllerClass);

        if(!method_exists($controller, $action)) {
            $this->notFound();
            return;
        }
        $controller->$action($this->request, ...$params);
    }
    private function notFound(): void
    {
        http_response_code(404);
        echo "me 404";
        exit();
    }
}