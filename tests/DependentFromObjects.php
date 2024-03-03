<?php

namespace Bcchicr\Container;

class DependentFromObjects
{
    public function __construct(NoConstructor $one, NoParamConstructor $two)
    {
    }
}
