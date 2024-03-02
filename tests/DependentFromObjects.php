<?php

namespace Bcchicr\StudentList\Container;

class DependentFromObjects
{
    public function __construct(NoConstructor $one, NoParamConstructor $two)
    {
    }
}
