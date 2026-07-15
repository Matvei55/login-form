<?php
namespace App\Container;

use App\Container\Exceptions\ContainerException;
use App\Container\Exceptions\NotFoundException;

class Container implements ContainerInterface
{
    private array $bindings = [];
    private array $instances = [];

    public function bind(string $abstract, $concrete = null ): void
    {
        if($concrete === null){
            $concrete = $abstract;
        }
        $this->bindings[$abstract] = $concrete;
    }

    public function singleton(string $abstract, $concrete = null):void
    {
        if ($concrete === null) {
            $concrete = $abstract;
        }
        $this->bindings[$abstract] = $concrete;
        $this->instances[$abstract] = null;
    }
    
    public function has(string $id): bool
    {
        return isset($this->bindings[$id]);
    }

    public function get(string $id)
    {
        if(isset($this->instances[$id]) && $this->instances[$id] !== null){
            return $this->instances[$id];
        }
        if(isset($this->bindings[$id])){
            $concrete = $this->bindings[$id];
            $object = $this->resolve($concrete);

            if (array_key_exists($id, $this->instances)) {
                $this->instances[$id] = $object;
            }
            return $object;
        }
        if(class_exists($id)){
            return $this->resolve($id);
        }

        throw new NotFoundException("зависимость не найдена : $id");
    }

    private function resolve($concrete)
    {
        if($concrete instanceof \Closure){
            return $concrete($this);
        }
        if(is_object($concrete)){
            return $concrete;
        }
        if(is_string($concrete)){
            return $this->resolveClass($concrete);
        }
        throw new ContainerException("не удалось разрешить зависимость");
    }

    private function resolveClass(string $className)
    {
        $reflection = new \ReflectionClass($className);

        if (!$reflection->isInstantiable()) {
            throw new ContainerException("Класс не может быть создан: $className");
        }

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return $reflection->newInstance();
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach($parameters as $parameter) {
            $type = $parameter->getType();

            if($type === null) {
                if($parameter->isDefaultValueAvailable()){
                    $dependencies[] = $parameter->getDefaultValue();
                    continue;
                }
                throw new ContainerException("не удалось разрешить параметр: {$parameter->getName()}");
            }
            $typeName= $type->getName();

            if($type->isBuiltin()) {
                if($parameter->isDefaultValueAvailable()){
                    $dependencies[] = $parameter->getDefaultValue();
                    continue;
                }
                throw new ContainerException("не удалось разрешить параметр: {$parameter->getName()}");
            }
            if($this->has($typeName) || class_exists($typeName)){
                $dependencies[] = $this->get($typeName);
            }elseif ($parameter->isDefaultValueAvailable()){
                $dependencies[] =$parameter->getDefaultValue();
            }else{
                throw new ContainerException("не удалось разрешить зависимость: $typeName");
            }
        }
        return $reflection->newInstanceArgs($dependencies);
    }    

    public function registerDirectory(string $directory, string $namespace = ''):void
    {
        if(!is_dir($directory)){
            return;
        }

        $files = glob($directory . '/*.php');
        foreach($files as $file) {
            $className= basename($file, '.php');
            $fullClassName = $namespace ? $namespace . '\\' . $className : $className;
        }
        if(class_exists($fullClassName) && !$this->has($fullClassName)) {
            $reflection = new \ReflectionClass($fullClassName);
            if($reflection->isInstantiable()) {
                $this->bind($fullClassName);
            }
        }
    }

    public function make(string $className, array $parameters = [])
    {
    if ($this->has($className)) {
        $concrete = $this->bindings[$className];
        return $this->resolveWithParameters($concrete, $parameters);
    }
    
    if (class_exists($className)) {
        return $this->resolveClassWithParameters($className, $parameters);
    }
    
    throw new NotFoundException("класс не найден: $className");
    }

    private function resolveWithParameters($concrete, array $parameters = [])
    {
        if ($concrete instanceof \Closure) {
            return $concrete($this);
        }
        if (is_object($concrete)) {
            return $concrete;
        }
        if (is_string($concrete)) {
            return $this->resolveClassWithParameters($concrete, $parameters);
        }
        throw new ContainerException("Не удалось разрешить зависимость");
    }

    private function resolveClassWithParameters(string $className, array $parameters = [])
    {
        $reflection = new \ReflectionClass($className);

        if (!$reflection->isInstantiable()) {
            throw new ContainerException("Класс не может быть создан: $className");
        }

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return $reflection->newInstance();
        }

        $params = $constructor->getParameters();
        $dependencies = [];

        foreach ($params as $param) {
            $paramName = $param->getName();

            if (array_key_exists($paramName, $parameters)) {
                $dependencies[] = $parameters[$paramName];
                continue;
            }

            $type = $param->getType();

            if ($type === null) {
                if ($param->isDefaultValueAvailable()) {
                    $dependencies[] = $param->getDefaultValue();
                    continue;
                }
                throw new ContainerException("Не удалось разрешить параметр: $paramName");
            }

            $typeName = $type->getName();

            if ($type->isBuiltin()) {
                if ($param->isDefaultValueAvailable()) {
                    $dependencies[] = $param->getDefaultValue();
                    continue;
                }
                throw new ContainerException("Не удалось разрешить параметр: $paramName");
            }
            if ($this->has($typeName) || class_exists($typeName)) {
                $dependencies[] = $this->get($typeName);
            } elseif ($param->isDefaultValueAvailable()) {
                $dependencies[] = $param->getDefaultValue();
            } else {
                throw new ContainerException("Не удалось разрешить зависимость: $typeName");
            }
        }

        return $reflection->newInstanceArgs($dependencies);
    }
}