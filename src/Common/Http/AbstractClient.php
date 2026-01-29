<?php

namespace Omnipay\Common\Http;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Http\Message\RequestFactory;
use Omnipay\Common\Http\Exception\NetworkException;
use Omnipay\Common\Http\Exception\RequestException;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

abstract class AbstractClient implements ClientInterface
{
    /** @var HttpClientInterface */
    private $httpClient;

    /** @var RequestFactoryInterface|RequestFactory */
    private $requestFactory;

    /** @var StreamFactoryInterface|null */
    private $streamFactory;

    public function __construct(
        $httpClient = null,
        RequestFactoryInterface|RequestFactory $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null
    ) {
        $this->httpClient = $httpClient ?: Psr18ClientDiscovery::find();
        $this->requestFactory = $requestFactory ?: Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory = $streamFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function request(
        $method,
        $uri,
        array $headers = [],
        $body = null,
        $protocolVersion = '1.1'
    ) {
        $request = $this->createRequest($method, $uri, $headers, $body, $protocolVersion);

        return $this->sendRequest($request);
    }

    protected function createRequest(
        $method,
        $uri,
        array $headers = [],
        $body = null,
        $protocolVersion = '1.1'
    ): RequestInterface {
        if ($this->requestFactory instanceof RequestFactory) {
            return $this->requestFactory->createRequest($method, $uri, $headers, $body, $protocolVersion);
        }

        $request = $this->requestFactory->createRequest($method, $uri)->withProtocolVersion($protocolVersion);

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        if ($body !== '' && $body !== null) {
            if (is_resource($body)) {
                $stream = $this->getStreamFactory()->createStreamFromResource($body);
            } elseif ($body instanceof StreamInterface) {
                $stream = $body;
            } elseif (is_scalar($body) || (is_object($body) && method_exists($body, '__toString'))) {
                $stream = $this->getStreamFactory()->createStream((string)$body);
            } else {
                throw new \InvalidArgumentException('Invalid body type: ' . gettype($body));
            }
            $request = $request->withBody($stream);
        }

        return $request;
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NetworkException|RequestException
     */
    private function sendRequest(RequestInterface $request)
    {
        try {
            return $this->httpClient->sendRequest($request);
        } catch (NetworkExceptionInterface $networkException) {
            throw new NetworkException($networkException->getMessage(), $request, $networkException);
        } catch (\Throwable $exception) {
            throw new RequestException($exception->getMessage(), $request, $exception);
        }
    }

    private function getStreamFactory(): StreamFactoryInterface
    {
        $this->streamFactory = $this->streamFactory ?: Psr17FactoryDiscovery::findStreamFactory();

        return $this->streamFactory;
    }
}
