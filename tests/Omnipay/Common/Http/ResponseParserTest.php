<?php

namespace Omnipay\Common\Http;

use GuzzleHttp\Psr7\Response;
use Omnipay\Common\Exception\RuntimeException;
use Omnipay\Tests\TestCase;

class ResponseParserTest extends TestCase
{
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

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Unable to parse response body into JSON: 4
     */
    public function testParsesJsonResponseException()
    {
        $this->expectException(RuntimeException::class);

        $response = new Response(200, [], 'FooBar');

        ResponseParser::json($response);
    }

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

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Unable to parse response body into XML: String could not be parsed as XML
     */
    public function testParsesXmlResponseException()
    {
        $response = new Response(200, [], '<abc');

        ResponseParser::xml($response);
    }

    /**
     * Based on https://github.com/guzzle/guzzle3/blob/v3.9.3/tests/Guzzle/Tests/Http/Message/ResponseTest.php#L662-L676
     */
    public function testPreventsComplexExternalEntities()
    {
        $xml = '<?xml version="1.0"?><!DOCTYPE scan[<!ENTITY test SYSTEM "php://filter/read=convert.base64-encode/resource=ResponseTest.php">]><scan>&test;</scan>';
        $response = new Response(200, [], $xml);
        $oldCwd = getcwd();
        chdir(__DIR__);
        try {
            $xml = ResponseParser::xml($response);
            chdir($oldCwd);
            $this->markTestIncomplete('Did not throw the expected exception! XML resolved as: ' . $xml->asXML());
        } catch (\Exception $e) {
            chdir($oldCwd);
        }
    }

}