<?php

namespace Omnipay\Common\Exception;

/**
 * Resource Not Found Exception
 *
 * Thrown when the requested resource is not found on payment gateway
 */
class ResourceNotFoundException extends \Exception implements OmnipayException
{
    public function __construct($message = "Resource not found on payment gateway", $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
