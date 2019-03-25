<?php

namespace Omnipay\Common\Http;

use GuzzleHttp\Psr7\Response;
use Http\Client\Exception\NetworkException;
use Mockery as m;
use GuzzleHttp\Psr7\Request;
use Omnipay\Common\Http\Exception\RequestException;
use Omnipay\Tests\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;

class ClientTest extends TestCase
{
    public function testSendGet()
    {
        $mockClient = m::mock(ClientInterface::class);
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
            ->andReturn($response)
            ->once();

        $this->assertSame($response, $client->request('GET', '/path'));
    }

    public function testSendPostJson()
    {
        $mockClient = m::mock(ClientInterface::class);
        $mockFactory = m::mock(RequestFactoryInterface::class);
        $client = new Client($mockClient, $mockFactory);

        $request = new Request('POST', '/path');
        $response = new Response();

        $mockFactory->shouldReceive('createRequest')->withArgs([
            'POST',
            '/path',
        ])->andReturn($request);

        $mockClient->shouldReceive('sendRequest')
            ->with(m::on(function (RequestInterface $request) {

                if ($request->getMethod() !== 'POST') {
                    return false;
                }

                if ($request->getHeader('Content-Type') !== ['application/json']) {
                    return false;
                }

                if ((string) $request->getBody() !== '{foo:bar}') {
                    return false;
                }

                return true;
            }))
            ->andReturn($response)
            ->once();

        $this->assertSame($response, $client->request(
            'POST',
            '/path',
            ['Content-Type' => 'application/json'],
            '{foo:bar}'
        ));
    }

    public function testSendException()
    {
        $mockClient = m::mock(ClientInterface::class);
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
        $mockClient = m::mock(ClientInterface::class);
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
            ->andThrow(new NetworkException('Something went wrong', $request));

        $this->expectException(\Omnipay\Common\Http\Exception\NetworkException::class);
        $this->expectExceptionMessage('Something went wrong');

        $client->request('GET', '/path');
    }

    public function testSendExceptionGetRequest()
    {
        $mockClient = m::mock(ClientInterface::class);
        $mockFactory = m::mock(RequestFactoryInterface::class);
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