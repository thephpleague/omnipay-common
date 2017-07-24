<?php

namespace Omnipay\Common\Http;

use Omnipay\Common\Exception\RuntimeException;
use Psr\Http\Message\ResponseInterface;

class Helper
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
     * @param $data
     * @return string
     */
    public static function formDataEncode($data)
    {
        return http_build_query($data, '', '&');
    }

    /**
     * @param  $value
     * @param  int $options
     * @param  int $depth
     * @return string
     */
    public static function jsonEncode($value, $options = 0, $depth = 512)
    {
        $body = json_encode($value, $options, $depth);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('json_decode error: ' . json_last_error_msg());
        }

        return $body;
    }

    /**
     * Decodes a JSON string,
     *
     * @param  string|ResponseInterface $response
     * @param  bool $assoc
     * @param  int $depth
     * @param  int $options
     * @return mixed
     */
    public static function jsonDecode($response, $assoc = false, $depth = 512, $options = 0)
    {
        $json = self::toString($response);

        $data = json_decode($json, $assoc, $depth, $options);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('json_decode error: ' . json_last_error());
        }

        return $data;
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
    public static function xmlDecode($response)
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
            throw new \InvalidArgumentException('SimpleXML error: ' . $errorMessage);
        }

        return $xml;
    }
}
