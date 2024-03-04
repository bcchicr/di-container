<?php

namespace Bcchicr\Container;

class DependentFromNotInstantiable
{
    public function __construct(NotInstantiable $test)
    {
    }
}
