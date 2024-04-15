<?php

namespace Omnipay\Common\Http;

use function GuzzleHttp\Psr7\str;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Omnipay\Common\Http\Exception\NetworkException;
use Omnipay\Common\Http\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Client implements ClientInterface
{
    /**
     * The Http Client which implements `public function sendRequest(RequestInterface $request)`
     * Note: Will be changed to PSR-18 when released
     *
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    public function __construct($httpClient = null, RequestFactoryInterface $requestFactory = null)
    {
        $this->httpClient = $httpClient ?: HttpClientDiscovery::find();
        $this->requestFactory = $requestFactory ?: MessageFactoryDiscovery::find();
    }

    /**
     * @param string $method
     * @param string|UriInterface $uri
     * @return ResponseInterface
     * @throws \Http\Client\Exception
     */
    public function request(
        $method, $uri) {
        $request = $this->requestFactory->createRequest($method, $uri);

        return $this->sendRequest($request);
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws \Http\Client\Exception
     */
    private function sendRequest(RequestInterface $request)
    {
        try {
            return $this->httpClient->sendRequest($request);
        } catch (\Http\Client\Exception\NetworkException $networkException) {
            throw new NetworkException($networkException->getMessage(), $request, $networkException);
        } catch (\Exception $exception) {
            throw new RequestException($exception->getMessage(), $request, $exception);
        }
    }
}
