<?php

namespace Bcchicr\Container;

use Bcchicr\Container\Exceptions\ContainerAutoWireException;
use Bcchicr\Container\Exceptions\ContainerNotFoundException;
use Bcchicr\Container\Exceptions\DefinitionException;

class AutoWire extends ContainerDecorator
{
    public function has(string $id): bool
    {
        return $this->container->has($id) || class_exists($id);
    }
    public function get(string $id): mixed
    {
        try {
            return $this->container->get($id);
        } catch (ContainerNotFoundException $e) {
            if (class_exists($id)) {
                return $this->autoWire($id);
            }
            throw $e;
        }
    }
    private function autoWire(string $id): mixed
    {
        try {
            $definition = new InstantiableDefinition($id);
            return $definition->resolve($this);
        } catch (DefinitionException $e) {
            throw new ContainerAutoWireException($e->getMessage());
        }
    }
}
