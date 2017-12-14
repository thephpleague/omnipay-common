<?php

namespace Omnipay\Common\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Omnipay\Common\Exception\RuntimeException;

class Response implements ResponseInterface
{
    /** @var ResponseInterface  */
    private $response;

    /**
     * Decorate a PSR-7 Response object
     *
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Parse the JSON response body and return an array
     *
     * Copied from Response->json() in Guzzle3 (copyright @mtdowling)
     * @link https://github.com/guzzle/guzzle3/blob/v3.9.3/src/Guzzle/Http/Message/Response.php
     *
     * @throws RuntimeException if the response body is not in JSON format
     * @return array|string|int|bool|float
     */
    public function json()
    {
        $body = $this->getBody()->__toString();

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
     * @return \SimpleXMLElement
     * @throws RuntimeException if the response body is not in XML format
     * @link http://websec.io/2012/08/27/Preventing-XXE-in-PHP.html
     *
     */
    public function xml()
    {
        $body = $this->getBody()->__toString();

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


    /**
     * @inheritdoc
     */
    public function getProtocolVersion()
    {
        return $this->response->getProtocolVersion();
    }

    /**
     * @inheritdoc
     */
    public function withProtocolVersion($version)
    {
        $response = clone $this;

        $response->response = $this->response->withProtocolVersion($version);

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function getHeaders()
    {
        return $this->response->getHeaders();
    }

    /**
     * @inheritdoc
     */
    public function hasHeader($name)
    {
        return $this->response->hasHeader($name);
    }

    /**
     * @inheritdoc
     */
    public function getHeader($name)
    {
        return $this->response->getHeader($name);
    }

    /**
     * @inheritdoc
     */
    public function getHeaderLine($name)
    {
        return $this->getHeaderLine($name);
    }

    /**
     * @inheritdoc
     */
    public function withHeader($name, $value)
    {
        $response = clone $this;

        $response->response = $response->response->withHeader($name, $value);

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function withAddedHeader($name, $value)
    {
        $response = clone $this;

        $response->response = $response->response->withAddedHeader($name, $value);

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function withoutHeader($name)
    {
        $response = clone $this;

        $response->response = $response->response->withoutHeader($name);

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function getBody()
    {
        return $this->response->getBody();
    }

    /**
     * @inheritdoc
     */
    public function withBody(StreamInterface $body)
    {
        $response = clone $this;

        $response->response = $response->response->withBody($body);

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    /**
     * @inheritdoc
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $response = clone $this;

        $response->response = $response->response->withStatus($code, $reasonPhrase);

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function getReasonPhrase()
    {
        $this->response->getReasonPhrase();
    }
}
