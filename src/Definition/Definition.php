<?php

namespace Bcchicr\Container\Definition;

use Bcchicr\Container\Container;

interface  Definition
{
    public function resolve(Container $container): mixed;
}
