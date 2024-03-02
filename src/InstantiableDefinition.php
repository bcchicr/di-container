<?php

namespace Bcchicr\StudentList\Container;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use Bcchicr\StudentList\Container\Exceptions\ContainerException;
use Bcchicr\StudentList\Container\Exceptions\ContainerNotFoundException;
use Bcchicr\StudentList\Container\Exceptions\DefinitionBindingException;

class InstantiableDefinition implements Definition
{
    public function __construct(
        private ReflectionClass $reflection
    ) {
    }
    public function resolve(Container $container): mixed
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
        $dependencies = array_map(
            function ($param) use ($container) {
                if ($this->isParamResolvable($param)) {
                    throw new DefinitionBindingException("Cannot resolve dependency '{$param->getName()}' in class '{$param->getDeclaringClass()->getName()}'");
                }
                try {
                    $instance = $container->get($param->getType()->getName());
                } catch (ContainerException $e) {
                    $instance = $param->getDefaultValue();
                }
                return $instance;
            },
            $constructParameters
        );
        return $reflector->newInstanceArgs($dependencies);
    }
    private function isParamResolvable(ReflectionParameter $param): bool
    {
        $paramType = $param->getType();
        return (!$param->isDefaultValueAvailable() &&
            (!$paramType instanceof ReflectionNamedType || $paramType->isBuiltin()));
    }
}
