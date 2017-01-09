<?php
// @codingStandardsIgnoreFile

namespace Jnjxp\Viewd;

use Aura\View\ViewFactory;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response;

class ViewdTest extends \PHPUnit_Framework_TestCase
{

    /**
     * SetUp
     *
     * @return mixed
     *
     * @access public
     */
    public function setUp()
    {
        $this->view = (new ViewFactory)->newInstance();
        $this->responder = new ViewResponder($this->view);
        $this->responder->setPrefix(null);

        $views = $this->view->getViewRegistry();
        $layouts = $this->view->getLayoutRegistry();

        $views->set(
            'foo',
            function () {
                echo 'foo content';
            }
        );

        $views->set(
            'error',
            function () {
                echo 'error content';
            }
        );

        $layouts->set(
            'default',
            function () {
                echo 'Layout: ';
                echo $this->getContent();
            }
        );

        $layouts->set(
            'other',
            function () {
                echo 'Other: ';
                echo $this->getContent();
            }
        );

    }

    /**
     * testIndex
     *
     * @return mixed
     *
     * @access public
     */
    public function testResponder()
    {
        $response  = new Response();
        $request   = ServerRequestFactory::fromGlobals()
                        ->withAttribute(ViewResponder::VIEW, 'foo');
        $responder = $this->responder;

        $responder->setDefaultLayout(null);

        $out = $responder($request, $response);

        $this->assertEquals('foo content', (string) $out->getBody());
        $this->assertEquals(200, $out->getStatusCode());
    }

    public function testNotFound()
    {
        $response  = new Response();
        $request   = ServerRequestFactory::fromGlobals()
                        ->withAttribute(ViewResponder::VIEW, 'bar');
        $responder = $this->responder;

        $responder->setDefaultLayout(null);
        $responder->setNotFound('error');

        $out = $responder($request, $response);

        $this->assertEquals('error content', (string) $out->getBody());
        $this->assertEquals(404, $out->getStatusCode());
    }

    public function testDefaultLayout()
    {
        $response  = new Response();
        $request   = ServerRequestFactory::fromGlobals()
                        ->withAttribute(ViewResponder::VIEW, 'foo');
        $responder = $this->responder;

        $out = $responder($request, $response);

        $this->assertEquals('Layout: foo content', (string) $out->getBody());
        $this->assertEquals(200, $out->getStatusCode());
    }

    public function testOtherLayout()
    {
        $response  = new Response();
        $request   = ServerRequestFactory::fromGlobals()
            ->withAttribute(ViewResponder::VIEW, 'foo')
            ->withAttribute(ViewResponder::LAYOUT, 'other');
        $responder = $this->responder;

        $out = $responder($request, $response);

        $this->assertEquals('Other: foo content', (string) $out->getBody());
        $this->assertEquals(200, $out->getStatusCode());
    }
}
