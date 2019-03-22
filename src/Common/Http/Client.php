<?php

namespace Omnipay\Common\Http;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Omnipay\Common\Http\Exception\NetworkException;
use Omnipay\Common\Http\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

class Client implements ClientInterface
{
    /**
     * The Http Client which implements `public function sendRequest(RequestInterface $request)`
     *
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    public function __construct($httpClient = null, $requestFactory = null, $streamFactory = null)
    {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
    }

    private function getHttpClient() : HttpClientInterface
    {
        if (!$this->httpClient) {
            $this->httpClient = Psr18ClientDiscovery::find();
        }

        return $this->httpClient;
    }

    private function getRequestFactory() : RequestFactoryInterface
    {
        if (!$this->requestFactory) {
            $this->requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        }

        return $this->requestFactory;
    }

    private function getStreamFactory() : StreamFactoryInterface
    {
        if (!$this->streamFactory) {
            $this->streamFactory = Psr17FactoryDiscovery::findStreamFactory();
        }

        return $this->streamFactory;
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
        $request = $this->getRequestFactory()
            ->createRequest($method, $uri)
            ->withProtocolVersion($protocolVersion);

        if ($body) {
            $stream = $this->getStreamFactory()->createStream($body);
            $request = $request->withBody($stream);
        }

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        return $this->sendRequest($request);
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NetworkException|RequestException
     */
    private function sendRequest(RequestInterface $request)
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
