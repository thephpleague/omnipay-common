<?php

namespace Omnipay\Common\Http\Exception;

use Omnipay\Common\Http\Exception;
use Psr\Http\Client\Exception\RequestException as PsrRequestException;

class RequestException extends Exception implements PsrRequestException
{
}
