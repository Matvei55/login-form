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
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\LoggerMiddleware;

class Application
{
    private static ?Application $instance = null;
    private function __construct(
        private ContainerInterface $container,
        private Router $router,
        private Request $request,
        private EventDispatcher $dispatcher
    ) {
        Config::load(__DIR__ . '/../../.env');
        $this->container->singleton(ContainerInterface::class,function (){
            return $this->container;
        });
        $this->autoRegister();
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
            __DIR__ . '/../Core' => 'App\\Core',
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
                    error_log(" Зарегистрирован middleware: " . $fullClassName);
                }
            }
        }
    }

    private function registerEvents(): void
    {
        $this->dispatcher->addListener(PostCreatedEvent::class, LogPostCreatedListener::class);
        $this->dispatcher->addListener(UserRegisteredEvent::class, LogUserRegisteredListener::class);
    }

    public static function getInstance(): self
    {
    if (self::$instance === null) {
        $container = new Container();

        $get = new Get();
        $post = new Post();
        $request = new Request($get, $post);

        self::$instance = new self(
            $container,
            new Router($request),
            $request,
            new EventDispatcher($container)
        );
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