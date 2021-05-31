<?php

namespace Omnipay\Common\Http;

use GuzzleHttp\Psr7\Request;
use Omnipay\Common\Http\Exception\NetworkException;
use Omnipay\Tests\TestCase;

class ExceptionTest extends TestCase
{
    public function testConstruct()
    {
        $request = new Request('GET', '/path');

        $previous = new \Exception('Whoops');
        $exception = new NetworkException('Something went wrong', $request, $previous);

        $this->assertSame($request, $exception->getRequest());
        $this->assertSame('Something went wrong', $exception->getMessage());
        $this->assertSame($previous, $exception->getPrevious());
    }
}