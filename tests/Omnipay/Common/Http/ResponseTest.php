<?php

namespace Omnipay\Common\Http;

use GuzzleHttp\Psr7\Response as HttpResponse;
use Omnipay\Common\Exception\RuntimeException;
use Omnipay\Tests\TestCase;

class ResponseTest extends TestCase
{
    public function testJsonResponse()
    {
        $response = new HttpResponse(200, [], '{"Baz":"Bar"}');

        $data = (new Response($response))->json();

        $this->assertEquals(array('Baz' => 'Bar'), $data);
    }


    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Unable to parse response body into JSON: 4
     */
    public function testJsonResponseException()
    {
        $response = new HttpResponse(200, [], 'FooBar');

        (new Response($response))->json();
    }

    public function testXmlResponse()
    {
        $response = new HttpResponse(200, [], '<Foo><Baz>Bar</Baz></Foo>');

        $data = (new Response($response))->xml();

        $this->assertInstanceOf('SimpleXMLElement', $data);
        $this->assertEquals('Bar', (string) $data->Baz);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Unable to parse response body into XML: String could not be parsed as XML
     */
    public function testXmlResponseException()
    {
        $response = new HttpResponse(200, [], '<abc');

        (new Response($response))->xml();
    }

    /**
     * Based on https://github.com/guzzle/guzzle3/blob/v3.9.3/tests/Guzzle/Tests/Http/Message/ResponseTest.php#L662-L676
     */
    public function testPreventsComplexExternalEntities()
    {
        $xml = '<?xml version="1.0"?><!DOCTYPE scan[<!ENTITY test SYSTEM "php://filter/read=convert.base64-encode/resource=ResponseTest.php">]><scan>&test;</scan>';
        $response = new HttpResponse(200, [], $xml);
        $oldCwd = getcwd();
        chdir(__DIR__);
        try {
            $xml = (new Response($response))->xml();
            chdir($oldCwd);
            $this->markTestIncomplete('Did not throw the expected exception! XML resolved as: ' . $xml->asXML());
        } catch (\Exception $e) {
            chdir($oldCwd);
        }
    }

}