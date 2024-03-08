<?php

namespace Bcchicr\Container;

use Psr\Container\ContainerInterface;
use Bcchicr\Container\Definition\Definition;
use Bcchicr\Container\Definition\AutoWireDefinition;
use Bcchicr\Container\Exception\ContainerGetException;
use Bcchicr\Container\Exception\ContainerNotFoundException;
use Bcchicr\Container\Definition\Exception\DefinitionException;
use Bcchicr\Container\Definition\FactoryDefinition;
use ReflectionClass;

class Container implements ContainerInterface
{
    /**
     * @var array<Definition>
     */
    private array $definitions = [];

    /**
     * @var mixed[]
     */
    private array $instances = [];

    public function __construct()
    {
        $this->instance($this::class, $this);
    }
    public function has(string $id): bool
    {
        return isset($this->instances[$id])
            || isset($this->definitions[$id])
            || $this->isClassAutoWireable($id);
    }
    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw new ContainerNotFoundException("Undefined dependency '{$id}'");
        }
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }
        $definition = $this->getDefinition($id);
        try {
            $instance = $definition->resolve($this);
        } catch (DefinitionException $e) {
            throw new ContainerGetException($e->getMessage());
        }
        return $this->instances[$id] = $instance;
    }
    public function register(
        string $id,
        callable $callback,
    ): void {
        $this->definitions[$id] = new FactoryDefinition($id, $callback);
    }
    public function instance(
        string $id,
        mixed $instance
    ): void {
        $this->instances[$id] = $instance;
    }
    private function isClassAutoWireable(string $class): bool
    {
        return class_exists($class) && (new ReflectionClass($class))->isInstantiable();
    }
    private function getDefinition(string $id): Definition
    {
        if (isset($this->definitions[$id])) {
            return $this->definitions[$id];
        }
        return new AutoWireDefinition($id);
    }
}
