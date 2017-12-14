<?php

namespace Omnipay\Common\Http;

use GuzzleHttp\Psr7\Response;
use Omnipay\Tests\TestCase;

class HelperTest extends TestCase
{
    public function testEncodesFormData()
    {
        $data = Helper::formDataEncode(array('Baz' => 'Bar', 'Foo' => 'Qux'));

        $this->assertEquals('Baz=Bar&Foo=Qux', $data);
    }

    public function testEncodesJsonArray()
    {
        $data = Helper::jsonEncode(array('Baz' => 'Bar'));

        $this->assertEquals('{"Baz":"Bar"}', $data);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage json_decode error: Malformed UTF-8 characters, possibly incorrectly encode
     */
    public function testEncodesJsonException()
    {
        $this->expectException(\InvalidArgumentException::class);

        Helper::jsonEncode("\xB1\x31");
    }

    public function testDecodesJsonString()
    {
        $data = Helper::jsonDecode('{"Baz":"Bar"}', true);

        $this->assertEquals(array('Baz' => 'Bar'), $data);
    }

    public function testDecodesJsonResponse()
    {
        $response = new Response(200, [], '{"Baz":"Bar"}');

        $data = Helper::jsonDecode($response);

        $this->assertEquals((object) array('Baz' => 'Bar'), $data);
    }

    public function testDecodesJsonResponseAssoc()
    {
        $response = new Response(200, [], '{"Baz":"Bar"}');

        $data = Helper::jsonDecode($response, true);

        $this->assertEquals(array('Baz' => 'Bar'), $data);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage json_decode error: 4
     */
    public function testDecodesJsonResponseException()
    {
        $this->expectException(\InvalidArgumentException::class);

        $response = new Response(200, [], 'FooBar');

        Helper::jsonDecode($response);
    }

    public function testDecodesXmlString()
    {
        $data = Helper::xmlDecode('<Foo><Baz>Bar</Baz></Foo>');

        $this->assertInstanceOf('SimpleXMLElement', $data);
        $this->assertEquals('Bar', (string) $data->Baz);
    }

    public function testDecodesXmlResponse()
    {
        $response = new Response(200, [], '<Foo><Baz>Bar</Baz></Foo>');

        $data = Helper::xmlDecode($response);

        $this->assertInstanceOf('SimpleXMLElement', $data);
        $this->assertEquals('Bar', (string) $data->Baz);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage SimpleXML error: String could not be parsed as XML
     */
    public function testDecodesXmlResponseException()
    {
        $response = new Response(200, [], '<abc');

        Helper::xmlDecode($response);
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
            $xml = Helper::xmlDecode($response);
            chdir($oldCwd);
            $this->markTestIncomplete('Did not throw the expected exception! XML resolved as: ' . $xml->asXML());
        } catch (\Exception $e) {
            chdir($oldCwd);
        }
    }

}