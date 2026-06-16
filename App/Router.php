<?php
namespace App;

class Router
{
//    private array $routes = [];
//
//    public function get(string $path, string $controller , string $method): self
//    {
//        $this->routes['GET'][$path] =  [ //сохранение в массив для гет запросов
//            'controller' => $controller,
//            'method' => $method
//        ];
//        return $this;
//    }
//    public function post(string $path, string $controller , string $method): self
//    {
//        $this->routes['POST'][$path] =  [  //сохранение в массив для пост запросов
//            'controller' => $controller,
//            'method' => $method
//        ];
//        return $this;
//    }
//    public function dispatch(Request $request): void
//    {
//        $url = $request->getUri();
//        $method = $request->getMethod();
//         $route = $this->routes[$method][$url] ?? null;
//         if ($route === null) {
//             $this->notFound();
//             return;
//         }
//         $controllerClass = '\\App\\' . $route['controller'];
//         if(!class_exists($controllerClass)){
//             $this->notFound();
//             return;
//         }
//
//         $controller = new $controllerClass();
//         $methodName = $route['method'];
//
//         if(!method_exists($controller, $methodName)){
//             $this->notFound();
//             return;
//         }
//         $controller->$methodName($request);
//    }
//
//    private function notFound(): void //вызов 404
//    {
//        http_response_code(404);
//        echo '404 - данной страницы не найдено,выкинь компьютер и иди работать в макдак';
//        exit();
//    }
    public function dispatch(Request $request): void
    {
        $url = $request->getUri();
        $method = $request->getMethod();

        $parts = explode('/', trim($url, '/'));

        if(empty($parts[0])) {
            $controllerName = 'LoginController';
            $action = 'showLogin';
            $params = [];
        }else{
            $controllerName = ucfirst($parts[0]) . 'Controller';
            $action = $parts[1] ?? 'index';
            $params = array_slice($parts, 2);
        }
        if($method == 'POST') {
            if($action === 'index') {
                $action = 'store';
            }
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