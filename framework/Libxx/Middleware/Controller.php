<?php

namespace Libxx\Middleware;

use Libxx\Container\ContainerAwareInterface;
use Libxx\Container\ContainerAwareTrait;

class Controller implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Retrieve a service from container.
     *
     * @param string $id
     * @return mixed
     */
    protected function get($id)
    {
        return $this->container->get($id);
    }
}
