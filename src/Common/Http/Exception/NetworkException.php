<?php

namespace Omnipay\Common\Http\Exception;

use Omnipay\Common\Http\Exception;
use Psr\Http\Client\Exception\NetworkException as PsrNetworkException;

class NetworkException extends Exception implements PsrNetworkException
{
}
