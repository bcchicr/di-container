<?php

namespace Bcchicr\StudentList\Container;

use Bcchicr\StudentList\Container\Exceptions\DefinitionBindingException;
use PDO;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class InstantiableDefinitionTest extends TestCase
{
    private Container $container;
    public function setUp(): void
    {
        $this->container = new Container();
    }
    public function testResolve(): void
    {
        $reflection = new ReflectionClass(DependentFromObjects::class);
        $definition = new InstantiableDefinition($reflection);
        $this->assertInstanceOf(DependentFromObjects::class, $definition->resolve($this->container));
    }
    public function testBinding(): void
    {
        $this->expectException(DefinitionBindingException::class);
        $reflection = new ReflectionClass(PDO::class);
        $definition = new InstantiableDefinition($reflection);
        $definition->resolve($this->container);
    }
}
