<?php

namespace Libxx\Error;

use Interop\Container\ContainerInterface;
use Libxx\Container\ContainerAwareInterface;
use Libxx\Container\ContainerAwareTrait;
use Libxx\Kernel\BootstrapInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\EmitterInterface;

class HandleExceptions implements ContainerAwareInterface, BootstrapInterface
{

    use ContainerAwareTrait;

    /**
     * Whether the debug mode is enabled.
     *
     * @var bool
     */
    private $debug;

    /**
     * The output charset.
     *
     * @var string
     */
    private $charset;

    /**
     * HandleExceptions constructor.
     *
     * @param bool $debug
     * @param string $charset
     */
    public function __construct($debug = false, $charset = 'utf-8')
    {
        $this->debug = $debug;
        $this->charset = $charset;
    }

    /**
     * @inheritDoc
     */
    public function boot(ContainerInterface $container)
    {
        ini_set('display_errors', false);
        error_reporting(-1);
        $this->register();
    }

    /**
     * Register the error handler.
     */
    public function register()
    {
        set_exception_handler([$this, 'handleUncaughtException']);
        set_error_handler([$this, 'handleUncaughtError']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    /**
     * Unregister the error handler.
     */
    public function unregister()
    {
        restore_error_handler();
        restore_exception_handler();
    }

    /**
     * Convert a PHP error to an ErrorException.
     *
     * @param  int $level
     * @param  string $message
     * @param  string $file
     * @param  int $line
     * @param  array $context
     * @return void
     *
     * @throws \ErrorException
     */
    public function handleUncaughtError($level, $message, $file = '', $line = 0, $context = [])
    {
        if (error_reporting() & $level) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Handle the uncaught exception.
     *
     * @param \Exception|\Error|\Throwable $e
     */
    public function handleUncaughtException($e)
    {
        if (!$e instanceof \Exception) {
            $e = $this->convertErrorToException($e);
        }

        $this->convertExceptionToResponse($e);
    }

    /**
     * Handle shutdown errors.
     */
    public function handleShutdown()
    {
        $error = error_get_last();
        if (!is_null($error) && $this->isFatal($error['type'])) {
            $this->cleanRawErrorOutput();
            $exception = $this->convertErrorToException($error);
            $this->convertExceptionToResponse($exception);
        }
    }

    /**
     * Clear the output buffer.
     */
    protected function cleanRawErrorOutput()
    {
        for ($level = ob_get_level(); $level > 0; --$level) {
            if (!@ob_end_clean()) {
                ob_clean();
            }
        }
    }

    /**
     * Check if the error type is fatal error.
     *
     * @param int $type
     * @return bool
     */
    protected function isFatal($type)
    {
        return in_array($type, [E_ERROR, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING, E_PARSE]);
    }

    /**
     * Convert an error context to an exception instance.
     *
     * @param mixed $error
     * @return \ErrorException
     */
    protected function convertErrorToException($error)
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

    /**
     * Convert exception to response.
     *
     * @param \Exception $e
     */
    protected function convertExceptionToResponse(\Exception $e)
    {
        $this->unregister();
        try {
            $handler = $this->container->get(ExceptionHandlerInterface::class);
            $resp = $handler->handle($e, $this->container->get(ServerRequestInterface::class), $this->container->get(ResponseInterface::class));
            $this->container->get(EmitterInterface::class)->emit($resp);
        } catch (\Exception $exception) {
            $msg = "An Error occurred while handling another error:\n";
            $msg .= (string) $exception;
            $msg .= "\nPrevious exception:\n";
            $msg .= (string) $e;
            if ($this->debug) {
                if (PHP_SAPI === 'cli') {
                    echo $msg . "\n";
                } else {
                    echo '<pre>' . htmlspecialchars($msg, ENT_QUOTES, $this->charset) . '</pre>';
                }
            } else {
                echo 'An internal server error occurred.';
            }
            $msg .= "\n\$_SERVER = " . json_encode($_SERVER);
            error_log($msg);
            exit(1);
        }
    }
}
