<?php

namespace Libxx\Helper;

class ParameterBag implements \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * The parameters.
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * ParameterBag constructor.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters = [])
    {
        $this->replace($parameters);
    }

    /**
     * Set a parameter value by key.
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set($key, $value)
    {
        $this->parameters[$key] = $value;
        return $this;
    }

    /**
     * Replace parameters.
     *
     * @param array $parameters
     * @return $this
     */
    public function replace(array $parameters)
    {
        $this->parameters = array_merge($this->parameters, $parameters);
        return $this;
    }

    /**
     * Get a parameter.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->parameters, $key, $default);
    }

    /**
     * Check if a parameter exist.
     *
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this->parameters[$key]) && !empty($this->parameters[$key]);
    }

    /**
     * Remove the parameter and return the removed value.
     *
     * @param string $key
     * @return mixed
     */
    public function remove($key)
    {
        $parameter = null;
        if (isset($this->parameters[$key])) {
            $parameter = $this->parameters[$key];
            unset($this->parameters[$key]);
        }
        return $parameter;
    }

    /**
     * Retrieve all the parameters.
     *
     * @return array
     */
    public function all()
    {
        return $this->parameters;
    }

    /**
     * Retrieve all the parameter keys.
     *
     * @return array
     */
    public function keys()
    {
        return array_keys($this->parameters);
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->parameters);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        return $this->remove($offset);
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->parameters);
    }
}
