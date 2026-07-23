<?php

namespace App\Core;

interface EventDispatcherInterface
{
    public function dispatch(object $event): void;
    public function addListener(string $eventClass, string $listenerClass): void;
}