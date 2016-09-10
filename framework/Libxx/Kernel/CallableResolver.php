<?php

namespace Libxx\Kernel;

use Libxx\Container\ContainerAwareInterface;
use Libxx\Container\ContainerAwareTrait;

class CallableResolver implements CallableResolverInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @inheritDoc
     */
    public function resolve($context)
    {
        if ($context instanceof \Closure) {
            $context->bindTo($this->container);
            return $context;
        }
        if (is_array($context) && isset($context[0], $context[1]) && is_string($context[1])) {
            $method = $context[1];
            if (is_string($context[0])) {
                $obj = $this->createCallableById($context[0]);
            } else {
                $obj = $context[0];
            }
            return [$obj, $method];
        }
        if (is_string($context)) {
            if (strpos($context, '::') === false) {
                $id = $context;
                $method = '__invoke';
            } else {
                list($id, $method) = explode('::', $context);
            }
            $obj = $this->createCallableById($id);
            return [$obj, $method];
        }
        if (is_object($context) && method_exists($context, '__invoke')) {
            return $context;
        }
        throw new \InvalidArgumentException(sprintf('Invalid callable context, only callable and string acceptable, %s given.', gettype($context)));
    }

    /**
     * Create callable object from class.
     *
     * @param string $id
     * @return mixed
     */
    private function createCallableById($id)
    {
        if ($this->container->has($id)) {
            return $this->container->get($id);
        }
        $callable = new $id;
        if ($callable instanceof ContainerAwareInterface) {
            $callable->setContainer($this->container);
        }
        return $callable;
    }
}
