<?php

namespace Libxx\Error;

use Interop\Container\ContainerInterface;
use Libxx\Container\ContainerAwareInterface;
use Libxx\Container\ContainerAwareTrait;
use Libxx\Kernel\BootstrapInterface;

class HandleExceptions implements ContainerAwareInterface, BootstrapInterface
{

    use ContainerAwareTrait;


    /**
     * @inheritDoc
     */
    public function boot(ContainerInterface $container)
    {
        error_reporting(-1);
        set_exception_handler([$this, 'handleUncaughtException']);
        set_error_handler([$this, 'handleUncaughtError']);
        register_shutdown_function([$this, 'handleShutdown']);
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
            $e = ErrorUtils::convertErrorToException($e);
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
            $exception = ErrorUtils::convertErrorToException($error);
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
     * Convert exception to response.
     *
     * @param \Exception $e
     */
    protected function convertExceptionToResponse(\Exception $e)
    {
        $handler = $this->container->get('exception_handler');
        $resp = $handler->handle($e, $this->container->get('request'), $this->container->get('response'));
        $this->container->get('response_emitter')->emit($resp);
    }
}
