<?php

namespace Omnipay\Common\Exception;

use Omnipay\TestCase;

class InvalidRequestExceptionTest extends TestCase
{
    public function testConstruct()
    {
        $exception = new InvalidRequestException('Oops');
        $this->assertSame('Oops', $exception->getMessage());
    }
}
