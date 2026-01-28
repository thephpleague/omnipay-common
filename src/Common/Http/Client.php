<?php

namespace Omnipay\Common\Http;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Http\Message\RequestFactory;
use Omnipay\Common\Http\Exception\NetworkException;
use Omnipay\Common\Http\Exception\RequestException;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

class Client implements ClientInterface
{
    /**
     * The Http Client which implements `public function sendRequest(RequestInterface $request)`
     *
     * @var \Psr\Http\Client\ClientInterface|\Http\Client\HttpClient
     */
    private $httpClient;

    /**
     * @var RequestFactoryInterface|RequestFactory
     */
    private $requestFactory;

    private $streamFactory;

    /**
     * @param \Psr\Http\Client\ClientInterface|\Http\Client\HttpClient|null $httpClient
     */
    public function __construct(
        $httpClient = null,
        null|RequestFactoryInterface|RequestFactory $requestFactory = null,
        null|StreamFactoryInterface $streamFactory = null
    ) {
        $this->httpClient = $httpClient ?: Psr18ClientDiscovery::find();
        $this->requestFactory = $requestFactory ?: Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory = $streamFactory ?: Psr17FactoryDiscovery::findStreamFactory();
    }

    /**
     * @param $method
     * @param $uri
     * @param array $headers
     * @param string|resource|StreamInterface|null $body
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
        $request = $this->requestFactory->createRequest($method, $uri)->withProtocolVersion($protocolVersion);

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        if (null !== $body) {
            if (is_resource($body)) {
                $stream = $this->streamFactory->createStreamFromResource($body);
            } elseif (is_string($body)) {
                $stream = $this->streamFactory->createStream($body);
            } elseif ($body instanceof StreamInterface) {
                $stream = $body;
            } else {
                throw new \InvalidArgumentException('Invalid body type.');
            }
            $request = $request->withBody($stream);
        }

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
