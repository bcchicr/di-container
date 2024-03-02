<?php

namespace Bcchicr\StudentList\Container;

use Bcchicr\StudentList\Container\Exceptions\ContainerPrepareException;
use Bcchicr\StudentList\Container\Exceptions\ContainerResolveException;
use DateTime;
use PDO;
use PHPUnit\Framework\TestCase;
use Psr\Container\NotFoundExceptionInterface;

class ContainerTest extends TestCase
{
    private Container $container;

    public function setUp(): void
    {
        $this->container = new Container();
    }
    public function testRegisterAndHas(): void
    {
        $this->container->register('test1', fn () => 'test');
        $isRegistered =  $this->container->has('test1');

        $this->assertTrue($isRegistered);
    }
    public function testRegisterAndGet(): void
    {
        $this->container->register(DateTime::class, fn () => new DateTime());
        $dateTime1 = $this->container->get(DateTime::class);
        $dateTime2 = $this->container->get(DateTime::class);
        $this->assertInstanceOf(DateTime::class, $dateTime1);
        $this->assertSame($dateTime1, $dateTime2);

        $this->container->register(DateTime::class, fn () => new DateTime());
        $dateTime3 = $this->container->get(DateTime::class);
        $this->assertNotSame($dateTime2, $dateTime3);
    }
    public function testAutoWiring(): void
    {
        $dateTime1 = $this->container->get(DateTime::class);
        $dateTime2 = $this->container->get(DateTime::class);

        $this->assertInstanceOf(DateTime::class, $dateTime1);
        $this->assertSame($dateTime1, $dateTime2);

        $noConstructor = $this->container->get(NoConstructor::class);
        $this->assertInstanceOf(NoConstructor::class, $noConstructor);

        $noParamConstructor = $this->container->get(NoParamConstructor::class);
        $this->assertInstanceOf(NoParamConstructor::class, $noParamConstructor);

        $dependent = $this->container->get(DependentFromObjects::class);
        $this->assertInstanceOf(DependentFromObjects::class, $dependent);
    }
    public function testNotFound(): void
    {
        $this->expectException(NotFoundExceptionInterface::class);
        $this->container->get('IncorrectID');
    }
    public function testPrepare(): void
    {
        $this->expectException(ContainerPrepareException::class);
        $this->container->get(NotInstantiable::class);
    }
    public function testResolve(): void
    {
        $this->expectException(ContainerResolveException::class);
        $this->container->get(PDO::class);
    }
}
