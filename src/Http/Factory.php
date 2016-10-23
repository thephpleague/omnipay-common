<?php

namespace League\Omnipay\Common\Http;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Factory
{
    /**
     * Create a new request.
     *
     * @param string $method
     * @param UriInterface|string $uri
     * @param array $headers
     * @param string|null|resource|StreamInterface $body
     * @param string $version
     * @return RequestInterface
     */
    public static function createRequest($method, $uri, $headers = [], $body = null, $version = '1.1')
    {
        return new Request($method, $uri, $headers, $body, $version);
    }

    /**
     * Create a new response.
     *
     * @param int $statusCode
     * @param array $headers
     * @param null $body
     * @return ResponseInterface
     *
     */
    public static function createResponse($statusCode = 200, $headers = [], $body = null)
    {
        return new Response($statusCode, $headers, $body);
    }

    /**
     * Create a new server request from PHP globals.
     *
     * @return ServerRequestInterface
     */
    public static function createServerRequestFromGlobals()
    {
        return ServerRequest::fromGlobals();
    }

    /**
     * Create a new URI.
     *
     * @param string|UriInterface $uri
     *
     * @return UriInterface
     *
     * @throws \InvalidArgumentException
     *  If the given URI cannot be parsed.
     */
    public static function createUri($uri = '')
    {
        return \GuzzleHttp\Psr7\uri_for($uri);
    }

    /**
     * Create a new stream from a resource.
     *
     * @param resource|string|null|int|float|bool|StreamInterface|callable $resource Entity body data
     *
     * @return StreamInterface
     */
    public static function createStream($resource = '')
    {
        return \GuzzleHttp\Psr7\stream_for($resource);
    }
}
