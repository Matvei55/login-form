<?php
namespace App\Core;

use App\Container\Container;
use App\Container\ContainerInterface;
use App\Events\PostCreatedEvent;
use App\Events\UserRegisteredEvent;
use App\Listeners\LogPostCreatedListener;
use App\Listeners\LogUserRegisteredListener;
use App\Middleware\Middleware;
use App\Middleware\MiddlewareInterface;

class Application
{
    private static ?Application $instance = null;
    private ContainerInterface $container;
    private Router $router;
    private Request $request;
    private EventDispatcher $dispatcher;

    private function __construct() //закрытый конструктор,читает енв и создает контейнер и регестрирует классы
    {
        Config::load(__DIR__ . '/../../.env');
        $this->container = new Container();
        $this->container->singleton(ContainerInterface::class,function (){
            return $this->container;
        }
        );
        $this->autoRegister();

        $this->request = $this->container->get(Request::class);
        $this->router = $this->container->get(Router::class);
        $this->dispatcher = new EventDispatcher();

        $this->registerEvents();
    }

    private function autoRegister(): void
    {
        $this->registerAllClasses();
        $this->registerSingletons();
        $this->registerControllers();
        $this->registerMiddleware();
    }
    private function registerAllClasses(): void //сканирую папки и регистрирую классы
    {
        $directories = [
            __DIR__ . '/../Models' => 'App\\Models',
            __DIR__ . '/../Controllers' => 'App\\Controllers',
            __DIR__ . '/../Middleware' => 'App\\Middleware',
            __DIR__ . '/../Events' => 'App\\Events',
            __DIR__ . '/../Listeners' => 'App\\Listeners',
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

    private function registerMiddleware(): void
    {
        $path = __DIR__ . '/../Middleware';
        if(!is_dir($path)) {
            return;
        }

        $files = glob($path . '/*.php');
        foreach($files as $file) {
            $className= basename($file, '.php');
            $fullClassName = 'App\\Middleware\\' . $className;

            if(class_exists($fullClassName)) {
                $reflection = new \ReflectionClass($fullClassName);

                if($reflection->implementsInterface(MiddlewareInterface::class)) {
                    $this->container->bind($fullClassName);
                }
            }
        }
    }

    private function registerEvents(): void
    {
        $this->dispatcher->addListener(PostCreatedEvent::class, LogPostCreatedListener::class);
        $this->dispatcher->addListener(UserRegisteredEvent::class, LogUserRegisteredListener::class);
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

    public function getDispatcher(): EventDispatcher
    {
        return $this->dispatcher;
    }

}