<?php
// @codingStandardsIgnoreFile

namespace Jnjxp\Viewd;

use Aura\View\ViewFactory;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response;

class StatusResponderTest extends \PHPUnit_Framework_TestCase
{

    public function testError()
    {

        $req = ServerRequestFactory::fromGlobals();
        $res = new Response();

        $view = $this->getMockBuilder('Aura\View\View')
            ->disableOriginalConstructor()
            ->getMock();

        $exception = new \Exception('MESSAGE');
        $payload = new \Aura\Payload\Payload;
        $payload->setStatus('ERROR')
            ->setOutput($exception);


        $responder = new StatusResponder($view);

        $result = $responder($req, $res, $payload);

        $this->assertEquals(
            500,
            $result->getStatusCode()
        );

        $this->assertEquals(
            'Error Exception : MESSAGE',
            (string) $result->getBody()
        );

    }

    public function testNoContent()
    {

        $req = ServerRequestFactory::fromGlobals();
        $res = new Response();

        $view = $this->getMockBuilder('Aura\View\View')
            ->disableOriginalConstructor()
            ->getMock();


        $responder = new StatusResponder($view);

        $result = $responder($req, $res);

        $this->assertEquals(
            204,
            $result->getStatusCode()
        );

        $this->assertEquals(
            'No Content',
            (string) $result->getBody()
        );

    }

    public function testKnown()
    {

        $req = ServerRequestFactory::fromGlobals();
        $res = new Response();

        $view = $this->getMockBuilder('Aura\View\View')
            ->disableOriginalConstructor()
            ->getMock();

        $payload = new \Aura\Payload\Payload;
        $payload->setStatus('foo');


        $responder = new Fake\Responder($view);

        $result = $responder($req, $res, $payload);

        $this->assertEquals(
            200,
            $result->getStatusCode()
        );

        $this->assertEquals(
            'foo',
            (string) $result->getBody()
        );
    }

    public function testUnknown()
    {

        $req = ServerRequestFactory::fromGlobals();
        $res = new Response();

        $view = $this->getMockBuilder('Aura\View\View')
            ->disableOriginalConstructor()
            ->getMock();

        $payload = new \Aura\Payload\Payload;
        $payload->setStatus('bar');


        $responder = new Fake\Responder($view);

        $result = $responder($req, $res, $payload);

        $this->assertEquals(
            500,
            $result->getStatusCode()
        );

        $this->assertEquals(
            'Unknown Status: "bar"',
            (string) $result->getBody()
        );
    }
}
