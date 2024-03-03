<?php

namespace Bcchicr\Container;

use Psr\Container\ContainerInterface;
use Bcchicr\Container\CallableDefinition;
use Bcchicr\Container\Exceptions\DefinitionException;
use Bcchicr\Container\Exceptions\ContainerResolveException;
use Bcchicr\Container\Exceptions\ContainerNotFoundException;


class Container implements ContainerInterface
{
    /**
     * @var array<Definition>
     */
    private array $definitions = [];

    public function __construct()
    {
    }
    public function has(string $id): bool
    {
        return isset($this->definitions[$id]);
    }
    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw new ContainerNotFoundException("Undefined dependency '{$id}'");
        }
        $definition = $this->definitions[$id];
        try {
            return $definition->resolve($this);
        } catch (DefinitionException $e) {
            throw new ContainerResolveException($e->getMessage());
        }
    }
    public function register(
        string $id,
        callable $value,
    ): void {
        $this->definitions[$id] = new CallableDefinition($value);
    }
}
