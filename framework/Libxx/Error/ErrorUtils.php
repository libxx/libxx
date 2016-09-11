<?php

namespace Libxx\Error;

class ErrorUtils
{

    /**
     * Convert an error context to an exception instance.
     *
     * @param mixed $error
     * @return \ErrorException
     */
    public static function convertErrorToException($error)
    {
        if ($error instanceof \Exception) {
            return $error;
        } elseif ($error instanceof \Error) {
            $exception = new \ErrorException($error->getMessage(), $error->getCode(), -1, $error->getFile(), $error->getLine());
            $trace = $error->getTrace();
        } else {
            $code = isset($error['code']) ? $error['code'] : 0;
            $exception = new \ErrorException($error['message'], $code, $error['type'], $error['file'], $error['line']);
            if (function_exists('xdebug_get_function_stack')) {
                $trace = array_reverse(array_slice(xdebug_get_function_stack(), 1, -2));
                $trace = array_map(function ($frame) {
                    if (!isset($frame['function'])) {
                        $frame['function'] = 'unknown';
                    }

                    if (!isset($frame['type']) || $frame['type'] === 'static') {
                        $frame['type'] = '::';
                    } elseif ($frame['type'] === 'dynamic') {
                        $frame['type'] = '->';
                    }

                    if (isset($frame['params']) && !isset($frame['args'])) {
                        $frame['args'] = $frame['params'];
                    }

                    return $frame;
                }, $trace);
            } else {
                $trace = [];
            }
        }

        $ref = new \ReflectionProperty('Exception', 'trace');
        $ref->setAccessible(true);
        $ref->setValue($exception, $trace);

        return $exception;
    }
}
