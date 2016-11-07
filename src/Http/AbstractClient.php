<?php

namespace League\Omnipay\Common\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Abstract Http Client
 *
 */
abstract class AbstractClient implements ClientInterface
{
    /**
     * @param  RequestInterface $request
     * @return ResponseInterface
     */
    abstract public function sendRequest(RequestInterface $request);

     /**
      * Send a GET request.
      *
      * @param UriInterface|string $uri
      * @param array $headers
      * @return ResponseInterface
      */
    public function get($uri, $headers = [])
    {
        $request = Factory::createRequest('GET', $uri, $headers);

        return $this->sendRequest($request);
    }

     /**
      * Send a POST request.
      *
      * @param UriInterface|string $uri
      * @param array $headers
      * @param string|null|resource|StreamInterface $body
      * @return ResponseInterface
      */
    public function post($uri, $headers = [], $body = null)
    {
        $request = Factory::createRequest('POST', $uri, $headers, $body);

        return $this->sendRequest($request);
    }

     /**
      * Send a PUT request.
      *
      * @param UriInterface|string $uri
      * @param array $headers
      * @param string|null|resource|StreamInterface $body
      * @return ResponseInterface
      */
    public function put($uri, $headers = [], $body = null)
    {
        $request = Factory::createRequest('PUT', $uri, $headers, $body);

        return $this->sendRequest($request);
    }

     /**
      * Send a PATCH request.
      *
      * @param UriInterface|string $uri
      * @param array $headers
      * @param string|null|resource|StreamInterface $body
      * @return ResponseInterface
      */
    public function patch($uri, $headers = [], $body = null)
    {
        $request = Factory::createRequest('PATCH', $uri, $headers, $body);

        return $this->sendRequest($request);
    }

     /**
      * Send a DELETE request.
      *
      * @param UriInterface|string $uri
      * @param array $headers
      * @param string|null|resource|StreamInterface $body
      * @return ResponseInterface
      */
    public function delete($uri, $headers = [], $body = null)
    {
        $request = Factory::createRequest('DELETE', $uri, $headers, $body);

        return $this->sendRequest($request);
    }

     /**
      * Send a HEAD request.
      *
      * @param UriInterface|string $uri
      * @param array $headers
      * @return ResponseInterface
      */
    public function head($uri, $headers = [])
    {
        $request = Factory::createRequest('HEAD', $uri, $headers);

        return $this->sendRequest($request);
    }

     /**
      * Send a OPTIONS request.
      *
      * @param UriInterface|string $uri
      * @return ResponseInterface
      */
    public function options($uri)
    {
        $request = Factory::createRequest('OPTIONS', $uri);

        return $this->sendRequest($request);
    }
}
