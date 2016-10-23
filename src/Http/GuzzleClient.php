<?php

namespace League\Omnipay\Common\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Default Http Client
 *
 * Implementation of the Http ClientInterface by using Guzzle.
 *
 */
class GuzzleClient extends AbstractClient implements ClientInterface
{
    /** @var  \GuzzleHttp\Client */
    protected $guzzle;

    public function __construct(Client $client = null)
    {
        $this->guzzle = $client ?: new Client(['a' => 'b']);
    }

    /**
     * @param  RequestInterface $request
     * @return ResponseInterface
     */
    public function sendRequest(RequestInterface $request)
    {
        return $this->guzzle->send($request);
    }
}
