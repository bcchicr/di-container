<?php

namespace Bcchicr\StudentList\Container;

use Closure;

class CallableDefinition implements Definition
{
    private Closure $callback;
    public function __construct(
        callable $callback
    ) {
        $this->callback = Closure::fromCallable($callback);
    }
    public function resolve(Container $container): mixed
    {
        return call_user_func($this->callback, $container);
    }
}
