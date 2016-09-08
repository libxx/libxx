<?php

namespace Libxx\Container;

use Interop\Container\ContainerInterface;

interface ContainerAwareInterface
{

    /**
     * Set the container to the aware.
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container);
}
