<?php

namespace Libxx\Kernel\Exception;

class HttpException extends Exception
{

    /**
     * The HTTP status code.
     *
     * @var int
     */
    protected $statusCode;

    /**
     * The HTTP headers.
     *
     * @var array
     */
    protected $headers;

    public function __construct($statusCode, $message = "", array $headers = [], $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->statusCode = (int)$statusCode;
        $this->headers = $headers;
    }

    /**
     * Get the HTTP headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get the HTTP status code.
     *
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
