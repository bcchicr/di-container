<?php

namespace Bcchicr\StudentList\Container;

use PHPUnit\Framework\TestCase;

class CallableDefinitionTest extends TestCase
{
    public function testResolve(): void
    {
        $container = new Container();
        $definition = new CallableDefinition(fn ($container) => $container);
        $this->assertEquals($container, $definition->resolve($container));
    }
}
