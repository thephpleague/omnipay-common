<?php

namespace Omnipay\Common\Http;

use Psr\Http\Message\RequestInterface;
use Throwable;

abstract class Exception extends \RuntimeException
{
    /** @var RequestInterface  */
    protected $request;

    public function __construct(string $message, RequestInterface $request, Throwable $previous = null)
    {
        $this->request = $request;

        parent::__construct($message, 0, $previous);
    }

    /**
     * Returns the request.
     *
     * The request object MAY be a different object from the one passed to ClientInterface::sendRequest()
     *
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->getRequest();
    }
}
