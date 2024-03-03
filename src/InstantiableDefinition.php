<?php

namespace Bcchicr\Container;

use ReflectionClass;
use Psr\Container\ContainerInterface;
use Bcchicr\Container\Exceptions\ContainerException;
use Bcchicr\Container\Exceptions\DefinitionBindingException;
use Bcchicr\Container\Exceptions\DefinitionConstructException;
use ReflectionNamedType;
use ReflectionParameter;

class InstantiableDefinition implements Definition
{
    private ReflectionClass $reflection;
    
    public function __construct(string $id)
    {
        if (!class_exists($id)) {
            throw new DefinitionConstructException("Unknown class '{$id}'");
        }
        $reflection = new ReflectionClass($id);
        if (!$reflection->isInstantiable()) {
            throw new DefinitionConstructException("Class '{$id}' is not instantiable");
        }
        $this->reflection = $reflection;
    }
    public function resolve(ContainerInterface $container): mixed
    {
        $reflector = $this->reflection;
        $constructor = $reflector->getConstructor();
        if (empty($constructor)) {
            return $reflector->newInstance();
        }
        $constructParameters = $constructor->getParameters();
        if (empty($constructParameters)) {
            return $reflector->newInstance();
        }
        $dependencies = $this->resolveParameters($container, $constructParameters);
        return $reflector->newInstanceArgs($dependencies);
    }

    private function resolveParameters(
        ContainerInterface $container,
        array $parameters
    ): array {
        return array_map(function (
            ReflectionParameter $param
        ) use ($container) {
            if (!$param->getType() instanceof ReflectionNamedType) {
                return $this->resolveNonTypedParameter($param);
            }
            return $this->resolveTypedParameter($container, $param);
        }, $parameters);
    }
    private function resolveNonTypedParameter(
        ReflectionParameter $param
    ): object {
        if ($param->isDefaultValueAvailable()) {
            return $param->getDefaultValue();
        }
        throw new DefinitionBindingException("Cannot resolve non typed dependency '{$param->getName()}' in class '{$param->getDeclaringClass()->getName()}'");
    }
    private function resolveTypedParameter(
        ContainerInterface $container,
        ReflectionParameter $param
    ) {
        try {
            return $container->get($param->getType()->getName());
        } catch (ContainerException $e) {
            if ($param->isDefaultValueAvailable()) {
                return $param->getDefaultValue();
            }
            throw new DefinitionBindingException("Cannot resolve dependency '{$param->getName()}' in class '{$param->getDeclaringClass()->getName()}: {$e->getMessage()}'");
        }
    }
}
