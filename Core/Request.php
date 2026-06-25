<?php
namespace Core;

class Request
{
    private Get $get;
    private Post $post;
    private string $method;
    private string $uri;

    public function __construct()
    {
        $this->get = new Get();
        $this->post = new Post();
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri = $_SERVER['REQUEST_URI'] ?? '/';
    }

    public function get():Get
    {
        return $this->get;
    }

    public function post():Post
    {
        return $this->post;
    }

    public function getParam(string $key, $default = null):mixed //получаю значение гет параметра
    {
        return $this->get->get($key, $default);
    }

    public function postParam(string $key, $default = null):mixed //получаю значение пост параметра
    {
        return $this->post->get($key, $default);
    }

    public function input(string $key, $default = null):mixed //ищу параметр сначала в пост потом в гет
    {
        if($this->post->has($key)){
            return $this->post->get($key);
        }
        if($this->get->has($key)){
            return $this->get->get($key);
        }
        return $default;
    }

    public function isMethod(string $method):bool//совпадает ли метод запроса с переданным
    {
        return strtoupper($this->method) === strtoupper($method);
    }

    public function isGet():bool
    {
        return $this->isMethod('GET');
    }

    public function isPost():bool
    {
        return $this->isMethod('POST');
    }

    public function getMethod():string //возвращает метод запроса
    {
        return $this->method;
    }

    public function getUri(): string //ури без гет параметров
    {
        $uri = parse_url($this->uri, PHP_URL_PATH);
        if($uri===''){
            $uri = '/';
        }
        return $uri;
    }
    public function getFullUri():string //полный ури
    {
        return $this->uri;
    }
}