<?php

namespace Libxx\Kernel;

use Interop\Container\ContainerInterface;

interface BootstrapInterface
{

    /**
     * Fire the bootstrap.
     *
     * @param ContainerInterface $container
     */
    public function boot(ContainerInterface $container);
}
