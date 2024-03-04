<?php

namespace Bcchicr\Container;

use Bcchicr\Container\Exception\ContainerGetException;
use Bcchicr\Container\Exception\ContainerNotFoundException;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class ContainerTest extends TestCase
{
    private Container $container;
    public function setUp(): void
    {
        $this->container = new Container();
    }
    public function testBasicHas()
    {
        $this->assertFalse($this->container->has('unregisteredId'));
        $this->container->register('test', fn ($container) => 'register');
        $this->assertTrue($this->container->has('test'));
    }
    public function testBasicGet()
    {
        $this->container->register('test', function (
            ContainerInterface $container
        ) {
            return new NoConstructor();
        });
        $test = $this->container->get('test');
        $this->assertInstanceOf(NoConstructor::class, $test);
    }
    public function testCache()
    {
        $this->container->register('test', function (
            ContainerInterface $container
        ) {
            return new NoConstructor();
        });
        $test1 = $this->container->get('test');
        $test2 = $this->container->get('test');
        $this->assertSame($test1, $test2);
    }
    public function testRecursiveGet()
    {
        $this->container->register('test1', function (
            ContainerInterface $container
        ) {
            return new NoConstructor();
        });
        $this->container->register('test2', function (
            ContainerInterface $container
        ) {
            return $container->get('test1');
        });
        $test1 = $this->container->get('test1');
        $test2 = $this->container->get('test2');
        $this->assertSame($test1, $test2);
    }
    public function testAutoWireGet()
    {
        $test1 = $this->container->get(NoConstructor::class);
        $this->assertInstanceOf(NoConstructor::class, $test1);
        $test2 = $this->container->get(NoParamConstructor::class);
        $this->assertInstanceOf(NoParamConstructor::class, $test2);
        $test3 = $this->container->get(DependentFromObjects::class);
        $this->assertInstanceOf(DependentFromObjects::class, $test3);
    }
    public function testBasicNotFound()
    {
        $this->expectException(ContainerNotFoundException::class);
        $this->container->get('unregistered');
    }
    public function testAutoWireHas()
    {
        $this->assertTrue($this->container->has(NoConstructor::class));
        $this->assertFalse($this->container->has(NotInstantiable::class));
    }
    public function testNotInstantiableGet()
    {
        $this->expectException(ContainerNotFoundException::class);
        $this->container->get(NotInstantiable::class);
    }
    public function testAutoWireUnresolvable()
    {
        $this->expectException(ContainerGetException::class);
        $this->container->get(DependentFromNotInstantiable::class);
    }
    public function testGetUnresolvable()
    {
        $this->expectException(ContainerGetException::class);
        $this->container->register('test', function () {
            throw new Exception("TEST EXCEPTION");
        });
        $this->container->get('test');
    }
}
