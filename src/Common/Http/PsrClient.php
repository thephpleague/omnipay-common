<?php

namespace Omnipay\Common\Http;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Omnipay\Common\Http\Exception\NetworkException;
use Omnipay\Common\Http\Exception\RequestException;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

class PsrClient implements ClientInterface
{
    private ?HttpClientInterface $httpClient;

    private ?RequestFactoryInterface $requestFactory;

    private ?StreamFactoryInterface $streamFactory;

    public function __construct(
        ?HttpClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null
    ) {
        $this->httpClient = $httpClient ?: Psr18ClientDiscovery::find();
        $this->requestFactory = $requestFactory ?: Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory = $streamFactory ?: Psr17FactoryDiscovery::findStreamFactory();
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
        $request = $this->requestFactory->createRequest($method, $uri)->withProtocolVersion($protocolVersion);

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        if ($body !== '' && $body !== null) {
            if (is_resource($body)) {
                $stream = $this->streamFactory->createStreamFromResource($body);
            } elseif ($body instanceof StreamInterface) {
                $stream = $body;
            } elseif (is_scalar($body) || (is_object($body) && method_exists($body, '__toString'))) {
                $stream = $this->streamFactory->createStream((string) $body);
            } else {
                throw new \InvalidArgumentException('Invalid body type: ' . gettype($body));
            }
            $request = $request->withBody($stream);
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
            return $this->httpClient->sendRequest($request);
        } catch (NetworkExceptionInterface $networkException) {
            throw new NetworkException($networkException->getMessage(), $request, $networkException);
        } catch (\Exception $exception) {
            throw new RequestException($exception->getMessage(), $request, $exception);
        }
    }
}
