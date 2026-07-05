<?php
namespace App\Core;

use App\Container\Container;
use App\Container\ContainerInterface;

class Application
{
    private static ?Application $instance = null;
    private ContainerInterface $container;
    private Router $router;
    private Request $request;

    private function __construct() //закрытый конструктор,читает енв и создает контейнер и регестрирует классы
    {
        Config::load(__DIR__ . '/../../.env');
        $this->container = new Container();
        $this->autoRegister();

        $this->request = $this->container->get(Request::class);
        $this->router = $this->container->get(Router::class);
    }

    private function autoRegister(): void
    {
        $this->registerContainerInterface();
        $this->registerAllClasses();
        $this->registerSingletons();
        $this->registerControllers();
    }

    private function registerContainerInterface(): void
    {
        $this->container->singleton(
            ContainerInterface::class,
            function () {
                return $this->container;
            }
        );
    }
    private function registerAllClasses(): void //сканирую папки и регистрирую классы
    {
        $directories = [
            __DIR__ . '/../Models' => 'App\\Models',
            __DIR__ . '/../Controllers' => 'App\\Controllers',
        ];

        foreach ($directories as $path => $namespace) {
            if (is_dir($path)) {
             $this->container->registerDirectory($path, $namespace);
            }
        }
    }

    private function registerSingletons(): void //регистрирую синглетоны
    {
        $singletons = [
            Request::class,
            Session::class,
            View::class,
            Database::class,
            Router::class,
        ];

        foreach ($singletons as $class) { //бд использует свою логику создания через замыкание
            if (class_exists($class)) {
                if($class === Database::class) {
                    $this->container->singleton($class, function () {
                        return Database::getInstance();
                    });
                }else{
                    $this->container->singleton($class);
                }
            }
        }
    }

    private function registerControllers(): void //нахожу контроллеры и регистрирую их
    {
        $path = __DIR__ . '/../Controllers';

        if(!is_dir($path)) {
            return;
        }
        $files = glob($path . '/*.php');

        foreach($files as $file) {
            //получаю имя класса из имени файла
            $className= basename($file, '.php');
            $fullClassName = 'App\\Controllers\\' . $className;

            if(class_exists($fullClassName)) {
                $reflection = new \ReflectionClass($fullClassName); //рефлектион показывает структуру класса
                if($reflection->isSubclassOf(Controller::class)) {
                    $this->container->bind($fullClassName, function ($c) use ($fullClassName) {
                        return new $fullClassName($c);
                    });
                }
            }
        }
    }

    public static function getInstance(): self //создаю синглетон(1)
    {
        if(self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function run(): void //запуск приложения
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
    public function getContainer(): ContainerInterface
    {
    return $this->container;
    }

}