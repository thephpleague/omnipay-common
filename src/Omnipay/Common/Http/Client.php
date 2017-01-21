<?php

namespace Omnipay\Common\Http;

use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\RequestFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Client implements HttpClient, RequestFactory
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var RequestFactory
     */
    private $requestFactory;

    public function __construct(HttpClient $httpClient = null, RequestFactory $requestFactory = null)
    {
        $this->httpClient = $httpClient ?: HttpClientDiscovery::find();
        $this->requestFactory = $requestFactory ?: MessageFactoryDiscovery::find();
    }

    /**
     * @param $method
     * @param $uri
     * @param array $headers
     * @param null $body
     * @param string $protocolVersion
     * @return RequestInterface
     */
    public function createRequest($method, $uri, array $headers = [], $body = null, $protocolVersion = '1.1')
    {
        return $this->requestFactory->createRequest($method, $uri, $headers, $body, $protocolVersion);
    }

    /**
     * @param  RequestInterface $request
     * @return ResponseInterface
     */
    public function sendRequest(RequestInterface $request)
    {
        return $this->httpClient->sendRequest($request);
    }

    /**
     * Send a GET request.
     *
     * @param UriInterface|string $uri
     * @param array $headers
     * @return ResponseInterface
     */
    public function get($uri, array $headers = [])
    {
        $request = $this->createRequest(
            'GET',
            $uri,
            $headers
        );

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
    public function post($uri, array $headers = [], $body = null)
    {
        $request = $this->createRequest(
            'POST',
            $uri,
            $headers,
            $body
        );

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
    public function put($uri, array $headers = [], $body = null)
    {
        $request = $this->createRequest(
            'PUT',
            $uri,
            $headers,
            $body
        );

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
    public function patch($uri, array $headers = [], $body = null)
    {
        $request = $this->createRequest(
            'PATCH',
            $uri,
            $headers,
            $body
        );

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
    public function delete($uri, array $headers = [], $body = null)
    {
        $request = $this->createRequest(
            'DELETE',
            $uri,
            $headers,
            $body
        );

        return $this->sendRequest($request);
    }

    /**
     * Send a HEAD request.
     *
     * @param UriInterface|string $uri
     * @param array $headers
     * @return ResponseInterface
     */
    public function head($uri, array $headers = [])
    {
        $request = $this->createRequest(
            'HEAD',
            $uri,
            $headers
        );

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
        $request = $this->createRequest(
            'OPTIONS',
            $uri
        );

        return $this->sendRequest($request);
    }

}