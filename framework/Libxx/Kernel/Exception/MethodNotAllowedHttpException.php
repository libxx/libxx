<?php

namespace Libxx\Kernel\Exception;

class MethodNotAllowedHttpException extends HttpException
{

    /**
     * MethodNotAllowedHttpException constructor.
     *
     * @param array $allow
     * @param string $message
     * @param array $headers
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct(array $allow, $message = "", array $headers = [], $code = 0, \Exception $previous = null)
    {
        $headers['Allow'] = strtoupper(implode(', ', $allow));
        parent::__construct(405, $message, $headers, $code, $previous);
    }
}
