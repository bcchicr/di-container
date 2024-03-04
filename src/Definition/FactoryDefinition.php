<?php

namespace Bcchicr\Container\Definition;

use Closure;
use Exception;
use Psr\Container\ContainerInterface;
use Bcchicr\Container\Definition\Exception\DefinitionResolveException;

class FactoryDefinition implements Definition
{
    private Closure $callback;
    public function __construct(
        private string $id,
        callable $callback
    ) {
        $this->callback = Closure::fromCallable($callback);
    }
    public function resolve(ContainerInterface $container): mixed
    {
        try {
            return call_user_func($this->callback, $container);
        } catch (Exception $e) {
            throw new DefinitionResolveException("Cannot resolve {$this->id}: {$e->getMessage()}'");
        }
    }
}
