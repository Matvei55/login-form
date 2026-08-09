<?php
namespace App\Core;

use App\Container\Container;
use App\Container\ContainerInterface;
use App\Events\ModelSavedEvent;
use App\Listeners\LogPostCreatedListener;
use App\Listeners\LogUserRegisteredListener;
use App\Middleware\MiddlewareInterface;
use App\Events\PostPendingModerationEvent;
use App\Listeners\SendTelegramModerationRequest;
use App\Core\HttpClient;
use App\Services\PostService;
use App\Services\TelegramService;

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
        $this->registerServices();
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
            __DIR__ . '/../Services' => 'App\\Services',
            __DIR__ . '/../DTO' => 'App\\DTO',
        ];

        foreach ($directories as $path => $namespace) {
            if (is_dir($path)) {
             $this->container->registerDirectory($path, $namespace);
            }
        }
    }

    private function registerSingletons(): void //регистрирую синглетоны
    {
        $this->container->singleton(
            EventDispatcherInterface::class,
            function ($c) {
                return new EventDispatcher($c);
            }
        );
        $this->container->singleton(HttpClient::class);
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
    private function registerServices(): void
    {
        $this->container->bind(TelegramService::class, function ($c) {
            return new TelegramService(
                $c->get(HttpClient::class),
                $c->get(Config::class),
            );
        });
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
                        return new $fullClassName(
                            $c->get(Request::class),
                            $c->get(View::class),
                            $c->get(Session::class),
                            $c->get(EventDispatcherInterface::class),
                            $c->get(PostService::class),
                            $c
                        );
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

//    private function registerEvents(): void
//    {
//        $this->dispatcher->addListener(ModelSavedEvent::class, LogPostCreatedListener::class);
//        $this->dispatcher->addListener(ModelSavedEvent::class, LogUserRegisteredListener::class);
//        $this->dispatcher->addListener(PostPendingModerationEvent::class, SendTelegramModerationRequest::class);
//    }
    private function registerEvents(): void
{
    error_log("📝 [Application] Регистрируем события...");

    $this->dispatcher->addListener(ModelSavedEvent::class, LogPostCreatedListener::class);
    $this->dispatcher->addListener(ModelSavedEvent::class, LogUserRegisteredListener::class);

    error_log("📝 [Application] Регистрируем PostPendingModerationEvent -> SendTelegramModerationRequest");
    $this->dispatcher->addListener(PostPendingModerationEvent::class, SendTelegramModerationRequest::class);

    // Проверка, что слушатель зарегистрирован
    $listeners = $this->dispatcher->getListenersForEvent(PostPendingModerationEvent::class);
    error_log("📋 [Application] Слушатели для PostPendingModerationEvent: " . print_r($listeners, true));

    error_log("✅ [Application] События зарегистрированы");
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