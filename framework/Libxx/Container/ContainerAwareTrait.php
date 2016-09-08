<?php

namespace Libxx\Container;

use Interop\Container\ContainerInterface;

trait ContainerAwareTrait
{

    /**
     * The container instance.
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Set the container instance to the aware.
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
