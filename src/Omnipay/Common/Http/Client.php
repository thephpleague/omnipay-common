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
     * @return ResponseInterface
     */
    public function send($method, $uri, array $headers = null, $body = null, $protocolVersion = '1.1')
    {
        $request = $this->createRequest($method, $uri, $headers, $body, $protocolVersion);

        return $this->sendRequest($request);
    }

    /**
     * @param $method
     * @param $uri
     * @param array $headers
     * @param null $body
     * @param string $protocolVersion
     * @return RequestInterface
     */
    public function createRequest($method, $uri, array $headers = null, $body = null, $protocolVersion = '1.1')
    {
        if (is_null($headers)) {
            $headers = [];
        }

        if (is_array($body)) {
            $body = http_build_query($body, '', '&');
        }

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
    public function get($uri, array $headers = null)
    {
        return $this->send(
            'GET',
            $uri,
            $headers
        );
    }

    /**
     * Send a POST request.
     *
     * @param UriInterface|string $uri
     * @param array $headers
     * @param string|null|resource|StreamInterface $body
     * @return ResponseInterface
     */
    public function post($uri, array $headers = null, $body = null)
    {
        return $this->send(
            'POST',
            $uri,
            $headers,
            $body
        );
    }

    /**
     * Send a PUT request.
     *
     * @param UriInterface|string $uri
     * @param array $headers
     * @param string|null|resource|StreamInterface $body
     * @return ResponseInterface
     */
    public function put($uri, array $headers = null, $body = null)
    {
        return $this->send(
            'PUT',
            $uri,
            $headers,
            $body
        );
    }

    /**
     * Send a PATCH request.
     *
     * @param UriInterface|string $uri
     * @param array $headers
     * @param string|null|resource|StreamInterface $body
     * @return ResponseInterface
     */
    public function patch($uri, array $headers = null, $body = null)
    {
        return $this->send(
            'PATCH',
            $uri,
            $headers,
            $body
        );
    }

    /**
     * Send a DELETE request.
     *
     * @param UriInterface|string $uri
     * @param array $headers
     * @param string|null|resource|StreamInterface $body
     * @return ResponseInterface
     */
    public function delete($uri, array $headers = null, $body = null)
    {
        return $this->send(
            'DELETE',
            $uri,
            $headers,
            $body
        );
    }

    /**
     * Send a HEAD request.
     *
     * @param UriInterface|string $uri
     * @param array $headers
     * @return ResponseInterface
     */
    public function head($uri, array $headers = null)
    {
        return $this->send(
            'HEAD',
            $uri,
            $headers
        );
    }

    /**
     * Send a OPTIONS request.
     *
     * @param UriInterface|string $uri
     * @return ResponseInterface
     */
    public function options($uri)
    {
        return $this->send(
            'OPTIONS',
            $uri
        );
    }
}
