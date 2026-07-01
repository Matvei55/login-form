<?php
namespace App\Core;

class Application
{
    private static ?Application $instance = null;
    private Router $router;
    private Request $request;

    private function __construct()
    {
        Config::load(__DIR__ . '/../../.env');
        $this->request = new Request();
        $this->router = new Router($this->request);
    }

    public static function getInstance(): self
    {
        if(self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function run(): void
    {
        $this->router->dispatch();
    }

    public function getRouter(): Router
    {
        return $this->router;
    }
    public function getRequest(): Request
    {
        return $this->request;
    }
}