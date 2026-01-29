<?php

namespace Omnipay\Common\Http;

use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class PsrClient extends AbstractClient
{
    public function __construct(
        ?HttpClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null
    ) {
        parent::__construct($httpClient, $requestFactory, $streamFactory);
    }
}
