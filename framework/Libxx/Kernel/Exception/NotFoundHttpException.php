<?php

namespace Libxx\Kernel\Exception;

class NotFoundHttpException extends HttpException
{

    /**
     * The request pathinfo.
     *
     * @var string
     */
    protected $pathInfo;

    /**
     * NotFoundHttpException constructor.
     *
     * @param string $pathInfo
     * @param string $message
     * @param array $headers
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($pathInfo, $message = "", array $headers = [], $code = 0, \Exception $previous = null)
    {
        $this->pathInfo = $pathInfo;
        parent::__construct(404, $message, $headers, $code, $previous);
    }

    /**
     * Get the pathinfo.
     *
     * @return string
     */
    public function getPathInfo()
    {
        return $this->pathInfo;
    }
}
