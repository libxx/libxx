<?php

namespace Libxx\Container;

use Interop\Container\ContainerInterface;
use Pimple\Container as PimpleContainer;

class Container extends PimpleContainer implements ContainerInterface
{
    /**
     * @inheritDoc
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException("Service named \"{$id}\" is not defined.");
        }
        return $this->offsetGet($id);
    }

    /**
     * @inheritDoc
     */
    public function has($id)
    {
        return $this->offsetExists($id);
    }
}
