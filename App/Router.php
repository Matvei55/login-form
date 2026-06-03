<?php
namespace App;

class Router
{
    private array $routes = [];

    public function get(string $path, string $controller , string $method): self
    {
        $this->routes['GET'][$path] =  [ //сохранение в массив для гет запросов
            'controller' => $controller,
            'method' => $method
        ];
        return $this;
    }
    public function post(string $path, string $controller , string $method): self
    {
        $this->routes['POST'][$path] =  [  //сохранение в массив для пост запросов
            'controller' => $controller,
            'method' => $method
        ];
        return $this;
    }
    public function dispatch(string $url, string $method): void
    {
        $url= parse_url($url, PHP_URL_PATH); //убираю юрл и индекс
        $url = str_replace('/index.php', '', $url);
        if($url ===''){
            $url = '/';
        }
        $route = $this->routes[$method][$url] ?? null; //ищу маршрут для данного метода и юрл

        if($route === null){
            $this->notFound();
            return;
        }
        $controllerClass = '\\App\\' . $route['controller'];  //создаю нужный контроллер и проверяю метод
        $controller = new $controllerClass();
        $methodName = $route['method'];
        if(!method_exists($controller, $methodName)){
            $this->notFound();
            return;
        }
        $controller->$methodName(); //вызов метода контроллера
    }

    private function notFound(): void //вызов 404
    {
        http_response_code(404);
        echo '404 - данной страницы не найдено,выкинь компьютер и иди работать в макдак';
        exit();
    }
}