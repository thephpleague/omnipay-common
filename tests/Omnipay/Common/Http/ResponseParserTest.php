<?php

namespace Omnipay\Common\Http;

use GuzzleHttp\Psr7\Response;
use Omnipay\Common\Exception\RuntimeException;
use Omnipay\Tests\TestCase;

class ResponseParserTest extends TestCase
{
    public function testParsesXmlString()
    {
        $data = ResponseParser::xml('<Foo><Baz>Bar</Baz></Foo>');

        $this->assertInstanceOf('SimpleXMLElement', $data);
        $this->assertEquals('Bar', (string) $data->Baz);
    }

    public function testParsesXmlResponse()
    {
        $response = new Response(200, [], '<Foo><Baz>Bar</Baz></Foo>');

        $data = ResponseParser::xml($response);

        $this->assertInstanceOf('SimpleXMLElement', $data);
        $this->assertEquals('Bar', (string) $data->Baz);
    }

    public function testParsesXmlResponseException()
    {
        $this->expectException(RuntimeException::class);

        $response = new Response(200, [], 'FooBar');

        ResponseParser::xml($response);
    }

    public function testParsesJsonString()
    {
        $data = ResponseParser::json('{"Baz":"Bar"}');

        $this->assertEquals(array('Baz' => 'Bar'), $data);
    }

    public function testParsesJsonResponse()
    {
        $response = new Response(200, [], '{"Baz":"Bar"}');

        $data = ResponseParser::json($response);

        $this->assertEquals(array('Baz' => 'Bar'), $data);
    }

    public function testParsesJsonResponseException()
    {
        $this->expectException(RuntimeException::class);

        $response = new Response(200, [], 'FooBar');

        ResponseParser::json($response);
    }

}