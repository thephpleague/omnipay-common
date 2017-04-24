<?php

namespace Omnipay\Common\Http;

use Omnipay\Common\Exception\RuntimeException;
use Psr\Http\Message\ResponseInterface;

class ResponseParser
{
    /**
     * @param string|ResponseInterface $response
     * @return string
     */
    private static function toString($response)
    {
        if ($response instanceof ResponseInterface) {
            return $response->getBody()->__toString();
        }

        return (string) $response;
    }

    /**
     * Parse the JSON response body and return an array
     *
     * Copied from Response->json() in Guzzle3 (copyright @mtdowling)
     * @link https://github.com/guzzle/guzzle3/blob/v3.9.3/src/Guzzle/Http/Message/Response.php
     *
     * @param  string|ResponseInterface $response
     * @throws RuntimeException if the response body is not in JSON format
     * @return array|string|int|bool|float
     */
    public static function json($response)
    {
        $body = self::toString($response);

        $data = json_decode($body, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new RuntimeException('Unable to parse response body into JSON: ' . json_last_error());
        }

        return $data === null ? [] : $data;
    }

    /**
     * Parse the XML response body and return a \SimpleXMLElement.
     *
     * In order to prevent XXE attacks, this method disables loading external
     * entities. If you rely on external entities, then you must parse the
     * XML response manually by accessing the response body directly.
     *
     * Copied from Response->xml() in Guzzle3 (copyright @mtdowling)
     * @link https://github.com/guzzle/guzzle3/blob/v3.9.3/src/Guzzle/Http/Message/Response.php
     *
     * @param  string|ResponseInterface $response
     * @return \SimpleXMLElement
     * @throws RuntimeException if the response body is not in XML format
     * @link http://websec.io/2012/08/27/Preventing-XXE-in-PHP.html
     *
     */
    public static function xml($response)
    {
        $body = self::toString($response);

        $errorMessage = null;
        $internalErrors = libxml_use_internal_errors(true);
        $disableEntities = libxml_disable_entity_loader(true);
        libxml_clear_errors();

        try {
            $xml = new \SimpleXMLElement((string) $body ?: '<root />', LIBXML_NONET);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
        }

        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);
        libxml_disable_entity_loader($disableEntities);

        if ($errorMessage !== null) {
            throw new RuntimeException('Unable to parse response body into XML: ' . $errorMessage);
        }

        return $xml;
    }
}
