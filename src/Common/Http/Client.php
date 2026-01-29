<?php

namespace Omnipay\Common\Http;

use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\RequestFactory;
use Omnipay\Common\Http\Exception\NetworkException;
use Omnipay\Common\Http\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @deprecated use Psr18Client instead
 */
class Client extends AbstractClient
{
    public function __construct($httpClient = null, ?RequestFactory $requestFactory = null)
    {
        parent::__construct($httpClient, $requestFactory);
    }
}
