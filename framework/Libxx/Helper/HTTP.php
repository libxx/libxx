<?php

namespace Libxx\Helper;

use Psr\Http\Message\MessageInterface;

class HTTP
{

    /**
     * Parse content type from an HTTP message.
     *
     * @param MessageInterface $message
     * @return null|string
     */
    public static function parseContentType(MessageInterface $message)
    {
        $ret = $message->getHeader('Content-Type');
        if ($ret) {
            $contentType = $ret[0];
            $contentTypeParts = preg_split('/\s*[;,]\s*/', $contentType);
            return strtolower($contentTypeParts[0]);
        }
        return null;
    }
}
