<?php

namespace Bcchicr\StudentList\Container;

interface  Definition
{
    public function resolve(Container $container): mixed;
}
