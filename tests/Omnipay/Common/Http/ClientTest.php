<?php

namespace Omnipay\Common\Http;

use Mockery as m;
use GuzzleHttp\Psr7\Request;
use Http\Client\HttpClient;
use Http\Message\RequestFactory;
use Omnipay\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testEmptyConstruct()
    {
        $client = new Client();

        $this->assertAttributeInstanceOf(HttpClient::class, 'httpClient', $client);
        $this->assertAttributeInstanceOf(RequestFactory::class, 'requestFactory', $client);
    }

    public function testCreateRequest()
    {
         $client = $this->getHttpClient();

         $request = $client->createRequest('GET', '/path', ['foo' => 'bar']);

         $this->assertInstanceOf(Request::class, $request);
         $this->assertEquals('/path', $request->getUri());
         $this->assertEquals(['bar'], $request->getHeader('foo'));
    }


    public function testSend()
    {
        $mockClient = m::mock(HttpClient::class);
        $mockFactory = m::mock(RequestFactory::class);
        $client = new Client($mockClient, $mockFactory);

        $request = new Request('GET', '/path');

        $mockFactory->shouldReceive('createRequest')->withArgs([
            'GET',
            '/path',
            [],
            null,
            '1.1',
        ])->andReturn($request);

        $mockClient->shouldReceive('sendRequest')->with($request)->once();

        $client->request('GET', '/path');
    }
}