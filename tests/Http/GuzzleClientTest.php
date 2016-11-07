<?php

namespace League\Omnipay\Common\Http;

use Mockery as m;
use League\Omnipay\Tests\TestCase;
use GuzzleHttp\Client as Guzzle;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class GuzzleClientTest extends TestCase
{
    /** @var  GuzzleClient */
    protected $client;

    public function setUp()
    {
        $this->guzzle = m::mock(Guzzle::class)->makePartial();
        $this->client = new GuzzleClient($this->guzzle);
    }

    public function testEmptyConstruct()
    {
        $client = new GuzzleClientTest_MockGuzzleClient();
        $this->assertInstanceOf(ClientInterface::class, $client);
        $this->assertInstanceOf(Guzzle::class, $client->guzzle);
        $this->assertNotEquals($this->guzzle, $client->guzzle);
    }

    public function testGuzzleConstruct()
    {
        $client = new GuzzleClientTest_MockGuzzleClient($this->guzzle);
        $this->assertInstanceOf(ClientInterface::class, $client);
        $this->assertInstanceOf(Guzzle::class, $client->guzzle);
        $this->assertEquals($this->guzzle, $client->guzzle);
    }

    public function testSendRequest()
    {
        $request = m::mock(RequestInterface::class);
        $response = m::mock(ResponseInterface::class);

        $this->guzzle->shouldReceive('send')->once()->with($request)->andReturn($response);

        $this->assertSame($response, $this->client->sendRequest($request));
    }
}

class GuzzleClientTest_MockGuzzleClient extends GuzzleClient
{
    public $guzzle;
}