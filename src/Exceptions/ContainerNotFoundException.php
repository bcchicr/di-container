<?php

namespace Bcchicr\Container\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

class ContainerNotFoundException extends ContainerException implements NotFoundExceptionInterface
{
}
