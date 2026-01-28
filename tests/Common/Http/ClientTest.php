<?php

namespace Omnipay\Common\Http;

use GuzzleHttp\Psr7\Response;
use Http\Client\Exception\NetworkException;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Factory\Guzzle\StreamFactory;
use Mockery as m;
use GuzzleHttp\Psr7\Request;
use Http\Client\HttpClient;
use Http\Message\RequestFactory;
use Omnipay\Common\Http\Exception\RequestException;
use Omnipay\Tests\TestCase;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

class ClientTest extends TestCase
{

    public function testSend()
    {
        $mockClient = m::mock(HttpClient::class);
        $mockFactory = m::mock(RequestFactory::class);
        $client = new Client($mockClient, $mockFactory);

        $request = new Request('GET', '/path');
        $response = new Response();

        $mockFactory->shouldReceive('createRequest')->withArgs([
            'GET',
            '/path',
        ])->andReturn($request);

        $mockClient->shouldReceive('sendRequest')
            ->with($request)
            ->andReturn($response)
            ->once();

        $this->assertSame($response, $client->request('GET', '/path'));
    }

    public function testSendRequest()
    {
        $mockClient = m::mock(HttpClient::class);

        $client = new Client($mockClient, MessageFactoryDiscovery::find());

        $response = new Response();

        $mockClient->shouldReceive('sendRequest')
            ->withArgs(function (RequestInterface $request) {
                return $request->getMethod() === 'GET'
                    && $request->getUri()->getPath() === '/path'
                    && $request->getHeader('Content-Type')[0] === 'application/json'
                    && $request->getBody()->getContents() === '{"a":"b"}'
                   ;
            })
            ->andReturn($response)
            ->once();

        $this->assertSame($response, $client->request('GET', '/path', [
            'Content-Type' => 'application/json'
        ], json_encode(['a' => 'b'])));
    }

    public function testSendPsr()
    {
        $mockClient = m::mock(\Psr\Http\Client\ClientInterface::class);
        $mockFactory = m::mock(RequestFactoryInterface::class);
        $client = new Client($mockClient, $mockFactory);

        $request = new Request('GET', '/path');
        $response = new Response();

        $mockFactory->shouldReceive('createRequest')->withArgs([
            'GET',
            '/path',
        ])->andReturn($request);

        $mockClient->shouldReceive('sendRequest')
            ->andReturn($response)
            ->once();

        $this->assertSame($response, $client->request('GET', '/path'));
    }

    public function testSendRequestPsr()
    {
        $mockClient = m::mock(\Psr\Http\Client\ClientInterface::class);

        $client = new Client($mockClient, Psr17FactoryDiscovery::findRequestFactory());

        $response = new Response();

        $mockClient->shouldReceive('sendRequest')
            ->withArgs(function (RequestInterface $request) {
                return $request->getMethod() === 'GET'
                    && $request->getUri()->getPath() === '/path'
                    && $request->getHeader('Content-Type')[0] === 'application/json'
                    && $request->getBody()->getContents() === '{"a":"b"}'
                    ;
            })
            ->andReturn($response)
            ->once();

        $this->assertSame($response, $client->request('GET', '/path', [
            'Content-Type' => 'application/json'
        ], json_encode(['a' => 'b'])));
    }

    public function testSendRequestBody()
    {
        $mockClient = m::mock(\Psr\Http\Client\ClientInterface::class);
        $mockStream = m::mock(StreamFactoryInterface::class);

        $client = new Client($mockClient, Psr17FactoryDiscovery::findRequestFactory(), $mockStream);

        $request = new Request('GET', '/path');
        $response = new Response();

        $streamFactory = new StreamFactory();
        $stream = $streamFactory->createStream('{"a":"b"}');

        $mockStream->shouldReceive('createStream')
            ->withArgs(['{"a":"b"}'])
            ->andReturn($stream);

        $mockClient->shouldReceive('sendRequest')
            ->withArgs(function (RequestInterface $request) use ($stream) {
                return $request->getBody() === $stream;
            })
            ->andReturn($response)
            ->once();

        $this->assertSame($response, $client->request('GET', '/path', [
            'Content-Type' => 'application/json'
        ], json_encode(['a' => 'b'])));
    }

    public function testSendException()
    {
        $mockClient = m::mock(\Psr\Http\Client\ClientInterface::class);
        $mockFactory = m::mock(RequestFactoryInterface::class);
        $client = new Client($mockClient, $mockFactory);

        $request = new Request('GET', '/path');
        $response = new Response();

        $mockFactory->shouldReceive('createRequest')->withArgs([
            'GET',
            '/path',
        ])->andReturn($request);

        $mockClient->shouldReceive('sendRequest')
            ->with($request)
            ->andThrow(new \Exception('Something went wrong'));

        $this->expectException(\Omnipay\Common\Http\Exception\RequestException::class);
        $this->expectExceptionMessage('Something went wrong');

        $client->request('GET', '/path');
    }

    public function testSendNetworkException()
    {
        $mockClient = m::mock(HttpClient::class);
        $mockFactory = m::mock(RequestFactory::class);
        $client = new Client($mockClient, $mockFactory);

        $request = new Request('GET', '/path');
        $response = new Response();

        $mockFactory->shouldReceive('createRequest')->withArgs([
            'GET',
            '/path',
        ])->andReturn($request);

        $mockClient->shouldReceive('sendRequest')
            ->with($request)
            ->andThrow(new NetworkException('Something went wrong', $request));

        $this->expectException(\Omnipay\Common\Http\Exception\NetworkException::class);
        $this->expectExceptionMessage('Something went wrong');

        $client->request('GET', '/path');
    }

    public function testSendExceptionGetRequest()
    {
        $mockClient = m::mock(HttpClient::class);
        $mockFactory = m::mock(RequestFactory::class);
        $client = new Client($mockClient, $mockFactory);

        $request = new Request('GET', '/path');
        $response = new Response();

        $mockFactory->shouldReceive('createRequest')->withArgs([
            'GET',
            '/path',
        ])->andReturn($request);

        $exception = new \Exception('Something went wrong');

        $mockClient->shouldReceive('sendRequest')
            ->with($request)
            ->andThrow($exception);

        $this->expectException(\Omnipay\Common\Http\Exception\RequestException::class);
        $this->expectExceptionMessage('Something went wrong');


        try {
            $client->request('GET', '/path');
        } catch (RequestException $e) {
            $this->assertSame($request, $e->getRequest());
            $this->assertSame($exception, $e->getPrevious());

            throw $e;
        }
    }
}