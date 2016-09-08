<?php

namespace Libxx\Container;

use Interop\Container\Exception\NotFoundException as NotFoundExceptionInterface;

class NotFoundException extends ContainerException implements NotFoundExceptionInterface
{

}
