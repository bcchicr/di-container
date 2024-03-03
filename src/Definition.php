<?php

namespace Bcchicr\Container;

interface  Definition
{
    public function resolve(Container $container): mixed;
}
