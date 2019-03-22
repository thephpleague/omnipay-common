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
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
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

    /**
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    /**
     * @var UriFactoryInterface
     */
    private $uriFactory;

    public function __construct(
        $httpClient = null,
        $requestFactory = null,
        $streamFactory = null,
        $uriFactory = null
    ) {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
        $this->uriFactory = $uriFactory;
    }

    /**
     * DRY factory getter
     * Loads a factory into memory only if requested
     *
     * @param $propertyName camelCased property name
     * @return factory set in constructor, otherwise the discovered by default
     */
    private function getFactory(string $propertyName)
    {
        if (empty($this->{$propertyName})) {
            $this->{$propertyName} = Psr17FactoryDiscovery::{'find' . ucfirst($propertyName)}();
        }

        return $this->{$propertyName};
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
        // TODO: After dropping MessageFactoryDiscovery support
        // replace contents of this function with the following line:
        // return $this->getFactory('requestFactory');

        if (empty($this->requestFactory)) {
            try {
                $this->requestFactory = Psr17FactoryDiscovery::findRequestFactory();
            } catch (DiscoveryNotFoundException $e) {
                $this->requestFactory = MessageFactoryDiscovery::find();
            }
        }
        return $this->requestFactory;
    }

    protected function getStreamFactory(): StreamFactoryInterface
    {
        return $this->getFactory('streamFactory');
    }

    protected function getUriFactory(): UriFactoryInterface
    {
        return $this->getFactory('uriFactory');
    }

    public function createRequest(string $method, $uri): RequestInterface
    {
        return $this->getRequestFactory()->createRequest($method, $uri);
    }

    public function createStream(string $content): StreamInterface
    {
        return $this->getStreamFactory()->createStream($content);
    }

    public function createUri(string $uri): UriInterface
    {
        return $this->getUriFactory()->createUri($method, $uri);
    }

    /**
     * @deprecated 4.0.0 use createRequest() directly instead
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
     * @deprecated 4.0.0 future versions will not use current exceptions
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
