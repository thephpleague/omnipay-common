<?php

namespace League\Omnipay\Common\Http;

use Mockery as m;
use League\Omnipay\Tests\TestCase;
use GuzzleHttp\Client as Guzzle;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class AbstractClientTest extends TestCase
{
    public function setUp()
    {
        $this->client = m::mock(AbstractClient::class)->makePartial();
    }

    public function testGet()
    {
        $response = m::mock(ResponseInterface::class);

        $this->client->shouldReceive('sendRequest')->once()->andReturn($response);

        $this->assertSame($response, $this->client->get('https://thephpleague.com/'));
    }

    public function testPost()
    {
        $response = m::mock(ResponseInterface::class);

        $this->client->shouldReceive('sendRequest')->once()->andReturn($response);

        $this->assertSame($response, $this->client->post('https://thephpleague.com/', [], 'my-body'));
    }

    public function testPut()
    {
        $response = m::mock(ResponseInterface::class);

        $this->client->shouldReceive('sendRequest')->once()->andReturn($response);

        $this->assertSame($response, $this->client->put('https://thephpleague.com/', [], 'my-body'));
    }

    public function testPatch()
    {
        $response = m::mock(ResponseInterface::class);

        $this->client->shouldReceive('sendRequest')->once()->andReturn($response);

        $this->assertSame($response, $this->client->patch('https://thephpleague.com/', [], 'my-body'));
    }

    public function testDelete()
    {
        $response = m::mock(ResponseInterface::class);

        $this->client->shouldReceive('sendRequest')->once()->andReturn($response);

        $this->assertSame($response, $this->client->patch('https://thephpleague.com/', [], 'my-body'));
    }

    public function testHead()
    {
        $response = m::mock(ResponseInterface::class);

        $this->client->shouldReceive('sendRequest')->once()->andReturn($response);

        $this->assertSame($response, $this->client->head('https://thephpleague.com/', []));
    }

    public function testOptions()
    {
        $response = m::mock(ResponseInterface::class);

        $this->client->shouldReceive('sendRequest')->once()->andReturn($response);

        $this->assertSame($response, $this->client->options('https://thephpleague.com/'));
    }
}
