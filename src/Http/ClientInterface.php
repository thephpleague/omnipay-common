<?php

namespace League\Omnipay\Common\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Http Interface
 *
 * This interface class defines the standard functions that any Omnipay http client
 * interface needs to be able to provide.
 *
 */
interface ClientInterface
{
    /**
     * @param  RequestInterface $request
     * @return ResponseInterface
     */
    public function sendRequest(RequestInterface $request);

    /**
     * Send a GET request.
     *
     * @param UriInterface|string $uri
     * @param array $headers
     * @return ResponseInterface
     */
    public function get($uri, $headers = []);

    /**
     * Send a POST request.
     *
     * @param UriInterface|string $uri
     * @param array $headers
     * @param string|null|resource|StreamInterface $body
     * @return ResponseInterface
     */
    public function post($uri, $headers = [], $body = null);

    /**
     * Send a PUT request.
     *
     * @param UriInterface|string $uri
     * @param array $headers
     * @param string|null|resource|StreamInterface $body
     * @return ResponseInterface
     */
    public function put($uri, $headers = [], $body = null);

    /**
     * Send a PATCH request.
     *
     * @param UriInterface|string $uri
     * @param array $headers
     * @param string|null|resource|StreamInterface $body
     * @return ResponseInterface
     */
    public function patch($uri, $headers = [], $body = null);

    /**
     * Send a DELETE request.
     *
     * @param UriInterface|string $uri
     * @param array $headers
     * @param string|null|resource|StreamInterface $body
     * @return ResponseInterface
     */
    public function delete($uri, $headers = [], $body = null);

    /**
     * Send a HEAD request.
     *
     * @param UriInterface|string $uri
     * @param array $headers
     * @return ResponseInterface
     */
    public function head($uri, $headers = []);

    /**
     * Send a OPTIONS request.
     *
     * @param UriInterface|string $uri
     * @return ResponseInterface
     */
    public function options($uri);
}
