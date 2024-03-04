<?php

namespace Bcchicr\Container\Definition;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use Psr\Container\ContainerInterface;
use Bcchicr\Container\Exception\ContainerException;
use Bcchicr\Container\Definition\Exception\DefinitionConstructException;
use Bcchicr\Container\Definition\Exception\DefinitionResolveException;

class AutoWireDefinition implements Definition
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
        throw new DefinitionResolveException("Cannot resolve non typed dependency '{$param->getName()}' in class '{$param->getDeclaringClass()->getName()}'");
    }
    private function resolveTypedParameter(
        ContainerInterface $container,
        ReflectionParameter $param
    ) {
        try {
            /**
             * @var ReflectionNamedType
             */
            $paramType = $param->getType();
            return $container->get($paramType->getName());
        } catch (ContainerException $e) {
            if ($param->isDefaultValueAvailable()) {
                return $param->getDefaultValue();
            }
            throw new DefinitionResolveException("Cannot resolve dependency '{$param->getName()}' in class '{$param->getDeclaringClass()->getName()}: {$e->getMessage()}'");
        }
    }
}
