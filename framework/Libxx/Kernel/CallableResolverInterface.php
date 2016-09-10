<?php

namespace Libxx\Kernel;

interface CallableResolverInterface
{

    /**
     * Resolve the context to a callable.
     *
     * @param mixed $context
     * @return callable
     */
    public function resolve($context);
}
