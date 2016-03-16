<?php
// @codingStandardsIgnoreFile

namespace Jnjxp\Viewd;

use Aura\View\ViewFactory;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response;

class ViewdTest extends \PHPUnit_Framework_TestCase
{

    /**
     * setUp
     *
     * @return mixed
     *
     * @access public
     */
    public function setUp()
    {
        $this->view = (new ViewFactory)->newInstance();
        $this->viewd = new Viewd($this->view);

        $views = $this->view->getViewRegistry();

        $views->set(
            'viewd/index',
            function () {
                echo 'index content';
            }
        );

        $views->set(
            'viewd/foo',
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

    }

    public function testIndex()
    {
        $response = new Response();
        $request = ServerRequestFactory::fromGlobals();
        $viewd = $this->viewd;

        $out = $viewd($request, $response);

        $this->assertEquals('index content', (string) $out->getBody());
        $this->assertEquals(200, $out->getStatusCode());
    }

    public function testPath()
    {
        $response = new Response();
        $request = ServerRequestFactory::fromGlobals()
            ->withRequestTarget('/foo');

        $viewd = $this->viewd;

        $out = $viewd($request, $response);

        $this->assertEquals('foo content', (string) $out->getBody());
        $this->assertEquals(200, $out->getStatusCode());
    }

    public function testAttr()
    {
        $response = new Response();
        $request = ServerRequestFactory::fromGlobals()
            ->withAttribute('jnjxp/viewd:script', 'foo');

        $viewd = $this->viewd;

        $out = $viewd($request, $response);

        $this->assertEquals('foo content', (string) $out->getBody());
        $this->assertEquals(200, $out->getStatusCode());
    }

    public function testError()
    {
        $response = new Response();
        $request = ServerRequestFactory::fromGlobals()
            ->withRequestTarget('/foobarbaz');

        $viewd = $this->viewd;

        $out = $viewd($request, $response);

        $this->assertEquals('error content', (string) $out->getBody());
        $this->assertEquals(404, $out->getStatusCode());
    }

    public function testNoError()
    {
        $response = new Response();
        $response->getBody()->write('Other Body');
        $request = ServerRequestFactory::fromGlobals()
            ->withRequestTarget('/foobarbaz');

        $viewd = $this->viewd;

        $out = $viewd($request, $response);

        $this->assertEquals('Other Body', (string) $out->getBody());
        $this->assertEquals(200, $out->getStatusCode());
    }


    public function testStopError()
    {
        $response = new Response();
        $request = ServerRequestFactory::fromGlobals()
            ->withRequestTarget('/bingbamboom')
            ->withAttribute('jnjxp/viewd:no-error', true);

        $viewd = $this->viewd;

        $out = $viewd($request, $response);

        $this->assertEquals('', (string) $out->getBody());
        $this->assertEquals(200, $out->getStatusCode());
    }
}
