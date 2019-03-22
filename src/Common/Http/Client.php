<?php

namespace Omnipay\Common\Http;

use Omnipay\Common\Http\Exception\NetworkException;
use Omnipay\Common\Http\Exception\RequestException;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\Exception\NotFoundException as DiscoveryNotFoundException;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Http\Message\RequestFactory;
use Psr\Http\Message\RequestFactoryInterface as Psr17RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Client\ClientInterface as Psr18ClientInterface;

class Client implements ClientInterface
{
    /**
     * The Http Client which implements `public function sendRequest(RequestInterface $request)`
     *
     * @var Psr18ClientInterface|HttpClient
     */
    private $httpClient;

    /**
     * @var Psr17RequestFactoryInterface|RequestFactory
     */
    private $requestFactory;

    public function __construct($httpClient = null, $requestFactory = null)
    {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
    }

    protected function getHttpClient()
    {
        if (empty($this->httpClient)) {
            try {
                $this->httpClient = Psr18ClientDiscovery::find();
            } catch (DiscoveryNotFoundException $e) {
                $this->httpClient = HttpClientDiscovery::find();
            }
        }
        return $this->httpClient;
    }

    protected function getRequestFactory()
    {
        if (empty($this->requestFactory)) {
            try {
                $this->requestFactory = Psr17FactoryDiscovery::findRequestFactory();
            } catch (DiscoveryNotFoundException $e) {
                $this->requestFactory = MessageFactoryDiscovery::find();
            }
        }
        return $this->requestFactory;
    }

    /**
     * @param $method
     * @param $uri
     * @param array $headers
     * @param string|array|resource|StreamInterface|null $body
     * @param string $protocolVersion
     * @return ResponseInterface
     * @throws \Http\Client\Exception
     */
    public function request(
        $method,
        $uri,
        array $headers = [],
        $body = null,
        $protocolVersion = '1.1'
    ) {
        if ($this->getRequestFactory() instanceof Psr18ClientInterface) {
            $request = $this->requestFactory
                ->createRequest($method, $uri)
                ->withProtocolVersion($protocolVersion);
            if ($body) {
                $request = $request->withBody($body);
            }
            foreach ($headers as $name => $value) {
                $request = $request->withHeader($name, $value);
            }
        } else {
            $request = $this->getRequestFactory()
                ->createRequest($method, $uri, $headers, $body, $protocolVersion);
        }

        return $this->sendRequest($request);
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NetworkException|RequestException
     */
    public function sendRequest(RequestInterface $request)
    {
        try {
            return $this->getHttpClient()->sendRequest($request);
        } catch (\Http\Client\Exception\NetworkException $networkException) {
            throw new NetworkException($networkException->getMessage(), $request, $networkException);
        } catch (\Throwable $exception) {
            throw new RequestException($exception->getMessage(), $request, $exception);
        }
    }
}
