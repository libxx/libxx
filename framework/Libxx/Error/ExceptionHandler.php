<?php

namespace Libxx\Error;

use Libxx\Helper\HTTP;
use Libxx\Kernel\Exception\HttpException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;

class ExceptionHandler implements ExceptionHandlerInterface
{

    /**
     * Debug mode.
     *
     * @var bool
     */
    private $debug;

    /**
     * ExceptionHandler constructor.
     *
     * @param bool $debug
     */
    public function __construct($debug)
    {
        $this->debug = $debug;
    }

    /**
     * @inheritDoc
     */
    public function handle(\Exception $e, ServerRequestInterface $req, ResponseInterface $resp)
    {
        $resp = new Response();

        $contentType = HTTP::parseContentType($req);
        if ($contentType === 'application/json') {
            $content = $this->renderJson($e);
        } else {
            $contentType = 'text/html';
            $content = $this->renderHtml($e);
        }

        $resp = $resp->withAddedHeader('Content-Type', $contentType);
        $resp->getBody()->write($content);

        if ($e instanceof HttpException) {
            foreach ($e->getHeaders() as $key => $value) {
                $resp = $resp->withHeader($key, $value);
            }
            $resp = $resp->withStatus($e->getStatusCode());
        } else {
            $resp = $resp->withStatus(500);
        }

        return $resp;
    }

    /**
     * Render the exception into json format.
     *
     * @param \Exception $e
     * @return string
     */
    protected function renderJson(\Exception $e)
    {
        $data = [
            'message' => 'An error occurred while processing your request.'
        ];
        if ($this->debug) {
            $debug = [sprintf("Type: %s", get_class($e))];
            if (($message = $e->getMessage())) {
                $debug[] = sprintf('Message: %s', $message);
            }
            if (($file = $e->getFile())) {
                $debug[] = sprintf('File: %s', $file);
            }
            if (($line = $e->getLine())) {
                $debug[] = sprintf('Line: %s', $line);
            }
            if (($trace = explode("\n", $e->getTraceAsString()))) {
                $debug[] = $trace;
            }

            $data['debug'] = $debug;
        }
        return json_encode($data);
    }

    /**
     * Render the exception into html format.
     *
     * @param \Exception $e
     * @return string
     */
    protected function renderHtml(\Exception $e)
    {
        $title = 'Application Error';
        if ($e instanceof HttpException) {
            $html = '<p>' . $e->getMessage() . '</p>';
        } else {
            $html = '<p>A website error has occurred. Sorry for the temporary inconvenience.</p>';
        }
        if ($this->debug) {
            $html .= '<h2>Details</h2>';
            $html .= $this->renderHtmlException($e);
            while ($exception = $e->getPrevious()) {
                $html .= '<h2>Previous exception</h2>';
                $html .= $this->renderHtmlException($e);
            }
        }

        $output = sprintf(
            "<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'>" .
            "<title>%s</title><style>body{margin:0;padding:30px;font:12px/1.5 Helvetica,Arial,Verdana," .
            "sans-serif;}h1{margin:0;font-size:48px;font-weight:normal;line-height:48px;}strong{" .
            "display:inline-block;width:65px;}</style></head><body><h1>%s</h1>%s</body></html>",
            $title,
            $title,
            $html
        );

        return $output;
    }

    /**
     * Render exception as HTML.
     *
     * @param \Exception $exception
     *
     * @return string
     */
    protected function renderHtmlException(\Exception $exception)
    {
        $html = sprintf('<div><strong>Type:</strong> %s</div>', get_class($exception));
        if (($code = $exception->getCode())) {
            $html .= sprintf('<div><strong>Code:</strong> %s</div>', $code);
        }
        if (($message = $exception->getMessage())) {
            $html .= sprintf('<div><strong>Message:</strong> %s</div>', htmlentities($message));
        }
        if (($file = $exception->getFile())) {
            $html .= sprintf('<div><strong>File:</strong> %s</div>', $file);
        }
        if (($line = $exception->getLine())) {
            $html .= sprintf('<div><strong>Line:</strong> %s</div>', $line);
        }
        if (($trace = $exception->getTraceAsString())) {
            $html .= '<h2>Trace</h2>';
            $html .= sprintf('<pre>%s</pre>', htmlentities($trace));
        }
        return $html;
    }
}
