<?php
// @codingStandardsIgnoreFile

namespace Jnjxp\Viewd;

use Aura\View\ViewFactory;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response;

class SimpleResponderTest extends \PHPUnit_Framework_TestCase
{

    public function testPayload()
    {

        $req = ServerRequestFactory::fromGlobals()->withAttribute('view', 'view');
        $res = new Response();

        $view = $this->getMockBuilder('Aura\View\View')
            ->disableOriginalConstructor()
            ->getMock();

        $view->expects($this->exactly(2))
            ->method('addData')
            ->withConsecutive(
                [['request' => $req]],
                [[
                    'status' => 'status',
                    'output' => 'output',
                    'input' => 'input',
                    'messages' => 'messages',
                    'extras' => 'extras'
                ]]
            );

        $view->expects($this->once())
            ->method('setView')
            ->with('view');

        $view->expects($this->once())
            ->method('__invoke');

        $payload = new \Aura\Payload\Payload;
        $payload->setStatus('status')
            ->setOutput('output')
            ->setInput('input')
            ->setMessages('messages')
            ->setExtras('extras');


        $responder = new SimpleResponder($view);

        $responder($req, $res, $payload);

    }

    public function testViewless()
    {
        $this->setExpectedException('Exception');
        $req = ServerRequestFactory::fromGlobals();
        $res = new Response();

        $view = $this->getMockBuilder('Aura\View\View')
            ->disableOriginalConstructor()
            ->getMock();

        $responder = new SimpleResponder($view);
        $responder($req, $res);
    }

}
