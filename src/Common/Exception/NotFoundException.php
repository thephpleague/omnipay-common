<?php

namespace Omnipay\Common\Exception;

/**
 * Not Found Exception
 *
 * Thrown when the requested resource is not found on payment gateway
 */
class NotFoundException extends \Exception implements OmnipayException
{
    public function __construct($message = "Resource not found on payment gateway", $code = 404, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
