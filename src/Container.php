<?php

namespace Bcchicr\Container;

use ReflectionClass;
use Psr\Container\ContainerInterface;
use Bcchicr\Container\CallableDefinition;
use Bcchicr\Container\InstantiableDefinition;
use Bcchicr\Container\Exceptions\DefinitionException;
use Bcchicr\Container\Exceptions\ContainerPrepareException;
use Bcchicr\Container\Exceptions\ContainerResolveException;
use Bcchicr\Container\Exceptions\ContainerNotFoundException;


class Container implements ContainerInterface
{
    /**
     * @var array<Definition>
     */
    private array $definitions = [];
    /**
     * @var array<object>
     */
    private array $instances = [];

    public function __construct()
    {
    }

    public function get(string $id): mixed
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }
        $definition =
            $this->has($id)
            ? $this->definitions[$id]
            : $this->prepareDefinition($id);

        try {
            $instance = $definition->resolve($this);
        } catch (DefinitionException $e) {
            throw new ContainerResolveException($e->getMessage());
        }
        $this->instances[$id] = $instance;
        return $instance;
    }

    public function has(string $id): bool
    {
        return isset($this->definitions[$id]);
    }

    public function register(
        string $id,
        callable $value,
    ): void {
        if ($this->has($id)) {
            unset($this->instances[$id]);
        }
        $this->definitions[$id] = new CallableDefinition($value);
    }

    private function prepareDefinition(string $id): Definition
    {
        if (!class_exists($id)) {
            throw new ContainerNotFoundException("Undefined dependency '{$id}'");
        }
        $reflection = new ReflectionClass($id);
        if (!$reflection->isInstantiable()) {
            throw new ContainerPrepareException("Class '{$id}' is not instantiable");
        }
        return new InstantiableDefinition($reflection);
    }
}
