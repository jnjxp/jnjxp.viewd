<?php
/**
* Jnjxp\Viewd
*
* PHP version 5
*
* This program is free software: you can redistribute it and/or modify it
* under the terms of the GNU Affero General Public License as published by
* the Free Software Foundation, either version 3 of the License, or (at your
* option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
*
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
* @category  Tests
* @package   Jnjxp\Viewd
* @author    Jake Johns <jake@jakejohns.net>
* @copyright 2015 Jake Johns
* @license   http://www.gnu.org/licenses/agpl-3.0.txt AGPL V3
* @link      http://jakejohns.net
 */

namespace Jnjxp\Viewd;

use Aura\View\ViewFactory;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response;

/**
 * ViewdTest
 *
 * @category CategoryName
 * @package  PackageName
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  http://www.gnu.org/licenses/agpl-3.0.txt AGPL V3
 * @link     http://jakejohns.net
 *
 */
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

    /**
     * testIndex
     *
     * @return mixed
     *
     * @access public
     */
    public function testIndex()
    {
        $response = new Response();
        $request = ServerRequestFactory::fromGlobals();
        $viewd = $this->viewd;

        $out = $viewd($request, $response);

        $this->assertEquals('index content', (string) $out->getBody());
        $this->assertEquals(200, $out->getStatusCode());
    }

    /**
     * testPath
     *
     * @return mixed
     *
     * @access public
     */
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

    /**
     * testAttr
     *
     * @return mixed
     *
     * @access public
     */
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

    /**
     * testError
     *
     * @return mixed
     *
     * @access public
     */
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

    /**
     * testNoError
     *
     * @return mixed
     *
     * @access public
     */
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


    /**
     * testNoError
     *
     * @return mixed
     *
     * @access public
     */
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
