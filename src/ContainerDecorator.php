<?php

namespace Bcchicr\Container;

use Psr\Container\ContainerInterface;

abstract class ContainerDecorator implements ContainerInterface
{
    public function __construct(
        protected ContainerInterface $container
    ) {
    }
}
