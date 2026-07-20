<?php
namespace App\Core;

use App\Container\ContainerInterface;

class EventDispatcher
{
    private array $listeners = []; //список слушателей для каждого события

    public function __construct(
        private ContainerInterface $container
    )
    {}
    public function addListener(string $eventClass, string $listenerClass): void //добавляю слушателя
    {
        if(!isset($this->listeners[$eventClass])){
            $this->listeners[$eventClass] = [];
        }
        $this->listeners[$eventClass][] = $listenerClass;
    }

    public function dispatch(object $event): void //отправляю событие
    {
        $eventClass = get_class($event);
        if(!isset($this->listeners[$eventClass])){
            return;
        }
        foreach($this->listeners[$eventClass] as $listenerClass){
            if(class_exists($listenerClass)){
                $listener = $this->container->get($listenerClass);
                $listener->handle($event);
            }
        }
    }
    public function getListeners(string $eventClass): array //получаю всех слушателей события
    {
        return $this->listeners[$eventClass] ?? [];
    }
}