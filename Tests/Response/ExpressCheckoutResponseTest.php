<?php

namespace Betacie\Bundle\PaypalBundle\Tests\Response;

use Betacie\Bundle\PaypalBundle\Response\Response;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    private $response;

    public function setUp()
    {
        $this->response = new Response(array(
            'ACK' => 'Success',
            'Foo' => 'Bar',
            'TOKEN' => 'foobarbaz'
        ));
    }

    public function tearDown()
    {
        $this->response = null;
    }

    public function testGetToken()
    {
        $this->assertEquals('foobarbaz', $this->response->getToken());
    }

    public function testGet()
    {
        $this->assertEquals('Bar', $this->response->get('Foo'));
        $this->assertNull($this->response->get('Baz'));
    }

    public function testIsSuccess()
    {
        $this->assertTrue($this->response->isSuccess());
    }
}
