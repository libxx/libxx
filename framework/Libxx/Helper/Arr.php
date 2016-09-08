<?php

namespace Libxx\Helper;

class Arr
{
    /**
     * Merge array.
     *
     * @param array $a
     * @param array $b
     * @return array
     */
    public static function merge(array $a, array $b)
    {
        foreach ($b as $k => $v) {
            if (is_int($k)) {
                $a[] = $v;
            } elseif (!is_array($v) || !array_key_exists($k, $a) || !is_array($a[$k])) {
                $a[$k] = $v;
            } else {
                $a[$k] = self::merge($a[$k], $v);
            }
        }
        return $a;
    }

    /**
     * Map array.
     *
     * @param array $array
     * @param $callable
     * @return array
     */
    public static function map(array $array, $callable)
    {
        $mapped = [];
        $args = array_slice(func_get_args(), 2);
        foreach ($array as $key => $value) {
            $currentArgs = array_merge([$value, $key], $args);
            $mapped[$key] = call_user_func_array($callable, $currentArgs);
        }
        return $mapped;
    }

    /**
     * Get a value by key from an array.
     *
     * @param array $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(array $array, $key, $default = null)
    {
        if (isset($array[$key])) {
            return $array[$key];
        }
        if (strpos($key, '.') !== false) {
            $pathSegments = explode('.', $key);
            $parameters = $array;
            foreach ($pathSegments as $pathSegment) {
                if (!isset($parameters[$pathSegment])) {
                    return $default;
                }
                $parameters = $parameters[$pathSegment];
            }
            return $parameters;
        }
        return $default;
    }
}
