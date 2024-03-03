<?php

namespace Bcchicr\Container;

use Bcchicr\Container\Exceptions\DefinitionBindingException;
use PDO;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class InstantiableDefinitionTest extends TestCase
{
    private ContainerInterface $container;
    public function setUp(): void
    {
        $this->container = new AutoWire(new Container());
    }
    public function testResolve(): void
    {
        $reflection = DependentFromObjects::class;
        $definition = new InstantiableDefinition($reflection);
        $this->assertInstanceOf(DependentFromObjects::class, $definition->resolve($this->container));
    }
    public function testBinding(): void
    {
        $this->expectException(DefinitionBindingException::class);
        $reflection = PDO::class;
        $definition = new InstantiableDefinition($reflection);
        $definition->resolve($this->container);
    }
}
