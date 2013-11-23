<?php

namespace OAuthTest\Unit\Common\Http\Client;

use OAuth\Common\Http\Client\GuzzleClient;

class GuzzleClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testConstructCorrectInstance()
    {
        $client = new GuzzleClient();

        $this->assertInstanceOf('\\OAuth\\Common\\Http\\Client\\AbstractClient', $client);
    }

    /**
     * @covers OAuth\Common\Http\Client\GuzzleClient::retrieveResponse
     */
    public function testRetrieveResponseThrowsExceptionOnGetRequestWithBody()
    {
        $this->setExpectedException('\\InvalidArgumentException');

        $client = new GuzzleClient();

        $client->retrieveResponse(
            $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface'),
            'foo',
            array(),
            'GET'
        );
    }

    /**
     * @covers OAuth\Common\Http\Client\GuzzleClient::retrieveResponse
     */
    public function testRetrieveResponseThrowsExceptionOnGetRequestWithBodyMethodConvertedToUpper()
    {
        $this->setExpectedException('\\InvalidArgumentException');

        $client = new GuzzleClient();

        $client->retrieveResponse(
            $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface'),
            'foo',
            array(),
            'get'
        );
    }

    /**
     * @covers OAuth\Common\Http\Client\GuzzleClient::retrieveResponse
     * @covers OAuth\Common\Http\Client\GuzzleClient::generateStreamContext
     */
    public function testRetrieveResponseDefaultUserAgent()
    {
        $endPoint = $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('http://httpbin.org/get'));

        $client = new GuzzleClient();

        $response = $client->retrieveResponse(
            $endPoint,
            '',
            array(),
            'get'
        );

        $response = json_decode($response, true);

        $this->assertSame('PHPoAuthLib', $response['headers']['User-Agent']);
    }

    /**
     * @covers OAuth\Common\Http\Client\GuzzleClient::retrieveResponse
     * @covers OAuth\Common\Http\Client\GuzzleClient::generateStreamContext
     */
    public function testRetrieveResponseCustomUserAgent()
    {
        $endPoint = $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('http://httpbin.org/get'));

        $client = new GuzzleClient('My Super Awesome Http Client');

        $response = $client->retrieveResponse(
            $endPoint,
            '',
            array(),
            'get'
        );

        $response = json_decode($response, true);

        $this->assertSame('My Super Awesome Http Client', $response['headers']['User-Agent']);
    }

    /**
     * @covers OAuth\Common\Http\Client\GuzzleClient::retrieveResponse
     * @covers OAuth\Common\Http\Client\GuzzleClient::generateStreamContext
     */
    public function testRetrieveResponseWithCustomContentType()
    {
        $endPoint = $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('http://httpbin.org/get'));

        $client = new GuzzleClient();

        $response = $client->retrieveResponse(
            $endPoint,
            '',
            array('Content-type' => 'foo/bar'),
            'get'
        );

        $response = json_decode($response, true);

        $this->assertSame('foo/bar', $response['headers']['Content-Type']);
    }

    /**
     * @covers OAuth\Common\Http\Client\GuzzleClient::retrieveResponse
     * @covers OAuth\Common\Http\Client\GuzzleClient::generateStreamContext
     */
    public function testRetrieveResponseWithFormUrlEncodedContentType()
    {
        $endPoint = $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('http://httpbin.org/post'));

        $client = new GuzzleClient();

        $response = $client->retrieveResponse(
            $endPoint,
            array('foo' => 'bar', 'baz' => 'fab'),
            array(),
            'POST'
        );

        $response = json_decode($response, true);

        $this->assertSame('application/x-www-form-urlencoded; charset=utf-8', $response['headers']['Content-Type']);
        $this->assertEquals(array('foo' => 'bar', 'baz' => 'fab'), $response['form']);
    }

    /**
     * @covers OAuth\Common\Http\Client\GuzzleClient::retrieveResponse
     * @covers OAuth\Common\Http\Client\GuzzleClient::generateStreamContext
     */
    public function testRetrieveResponseHost()
    {
        $endPoint = $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('http://httpbin.org/post'));

        $client = new GuzzleClient();

        $response = $client->retrieveResponse(
            $endPoint,
            array('foo' => 'bar', 'baz' => 'fab'),
            array(),
            'POST'
        );

        $response = json_decode($response, true);

        $this->assertSame('httpbin.org', $response['headers']['Host']);
    }

    /**
     * @covers OAuth\Common\Http\Client\GuzzleClient::retrieveResponse
     * @covers OAuth\Common\Http\Client\GuzzleClient::generateStreamContext
     */
    public function testRetrieveResponsePostRequestWithRequestBodyAsString()
    {
        $endPoint = $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('http://httpbin.org/post'));

        $client = new GuzzleClient();

        $response = $client->retrieveResponse(
            $endPoint,
            'foo',
            array(),
            'POST'
        );

        $response = json_decode($response, true);

        $this->assertSame('foo', $response['data']);
    }

    /**
     * @covers OAuth\Common\Http\Client\GuzzleClient::retrieveResponse
     * @covers OAuth\Common\Http\Client\GuzzleClient::generateStreamContext
     */
    public function testRetrieveResponsePutRequestWithRequestBodyAsString()
    {
        $endPoint = $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('http://httpbin.org/put'));

        $client = new GuzzleClient();

        $response = $client->retrieveResponse(
            $endPoint,
            'foo',
            array(),
            'PUT'
        );

        $response = json_decode($response, true);

        $this->assertSame('foo', $response['data']);
    }

    /**
     * @covers OAuth\Common\Http\Client\GuzzleClient::retrieveResponse
     * @covers OAuth\Common\Http\Client\GuzzleClient::generateStreamContext
     */
    public function testRetrieveResponseThrowsExceptionOnInvalidRequest()
    {
        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $endPoint = $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('dskjhfckjhekrsfhkehfkreljfrekljfkre'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('dskjhfckjhekrsfhkehfkreljfrekljfkre'));

        $client = new GuzzleClient();

        $response = $client->retrieveResponse(
            $endPoint,
            '',
            array('Content-type' => 'foo/bar'),
            'get'
        );

        $response = json_decode($response, true);

        $this->assertSame('foo/bar', $response['headers']['Content-Type']);
    }
}
