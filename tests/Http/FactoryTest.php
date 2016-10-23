<?php

namespace League\Omnipay\Common\Http;

use Mockery as m;
use League\Omnipay\Tests\TestCase;
use GuzzleHttp\Client as Guzzle;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class FactoryTest extends TestCase
{
    public function testCreateRequest()
    {
        $request = Factory::createRequest('POST', 'https://thephpleague.com/', ['key' => 'value'], 'my-body');

        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('https://thephpleague.com/', $request->getUri());
        $this->assertEquals('value', $request->getHeaderLine('key'));
        $this->assertEquals('my-body', $request->getBody());
    }

    public function testCreateUri()
    {
        $uri = Factory::createUri('https://thephpleague.com/');

        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertEquals('https://thephpleague.com/', (string) $uri);
    }

    public function testCreateStream()
    {
        $stream = Factory::createStream('my-body');

        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertEquals('my-body', (string) $stream);
    }
}
