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

class Client implements RequestFactory
{
    /**
     * The Http Client which implements `public function sendRequest(RequestInterface $request)`
     *
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var RequestFactory
     */
    private $requestFactory;

    public function __construct($httpClient = null, RequestFactory $requestFactory = null)
    {
        $this->httpClient = $httpClient ?: HttpClientDiscovery::find();
        $this->requestFactory = $requestFactory ?: MessageFactoryDiscovery::find();
    }

    /**
     * @param $method
     * @param $uri
     * @param array $headers
     * @param string|array|resource|StreamInterface|null $body
     * @param string $protocolVersion
     * @return ResponseInterface
     */
    public function send($method, $uri, array $headers = [], $body = null, $protocolVersion = '1.1')
    {
        if (!is_null($body) && !is_string($body)) {
            $body = Helper::formDataEncode($body);
        }

        $request = $this->createRequest($method, $uri, $headers, $body, $protocolVersion);

        return $this->sendRequest($request);
    }

    /**
     * Send data encoded as JSON and return the decoded response
     *
     * @param $method
     * @param $uri
     * @param array $headers
     * @param array $data
     * @param string $protocolVersion
     * @return array
     */
    public function json($method, $uri, $headers = [], array $data = null, $protocolVersion = '1.1')
    {
        $body = Helper::jsonEncode($data);

        $request = $this->createRequest($method, $uri, $headers, $body, $protocolVersion);

        // Add default Content-Type header when not set.
        if (! $request->hasHeader('Content-Type')) {
            $request = $request->withHeader('Content-Type', 'application/json');
        }

        // Accept JSON responses
        if (! $request->hasHeader('Accept')) {
            $request = $request->withHeader('Accept', 'application/json');
        }

        $response = $this->sendRequest($request);

        return Helper::jsonDecode($response, true) ?: [];
    }

    /**
     * Send data and
     *
     * @param $method
     * @param $uri
     * @param array $headers
     * @param null $body
     * @param string $protocolVersion
     * @return \SimpleXMLElement
     */
    public function xml($method, $uri, array $headers = [], $body = null, $protocolVersion = '1.1')
    {
        if (!is_null($body) && !is_string($body)) {
            $body = Helper::formDataEncode($body);
        }

        $request = $this->createRequest($method, $uri, $headers, $body, $protocolVersion);

        // Accept XML responses
        if (! $request->hasHeader('Accept')) {
            $request = $request->withHeader('Accept', 'application/xml');
        }

        $response = $this->sendRequest($request);

        return Helper::xmlDecode($response);
    }

    /**
     * @param $method
     * @param $uri
     * @param array $headers
     * @param string|resource|StreamInterface|null $body
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
        return $this->send('GET', $uri, $headers);
    }

    /**
     * Send a POST request.
     *
     * @param UriInterface|string $uri
     * @param array $headers
     * @param string|array|null|resource|StreamInterface $body
     * @return ResponseInterface
     */
    public function post($uri, array $headers = [], $body = null)
    {
        return $this->send('POST', $uri, $headers, $body);
    }
}
