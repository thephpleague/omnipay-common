<?php

namespace Omnipay\Common\Http;

use Http\Message\RequestFactory;

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
