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
* @category  Viewd
* @package   Jnjxp\Viewd
* @author    Jake Johns <jake@jakejohns.net>
* @copyright 2015 Jake Johns
* @license   http://www.gnu.org/licenses/agpl-3.0.txt AGPL V3
* @link      http://jakejohns.net
 */

namespace Jnjxp\Viewd;

use Aura\View\View;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Viewd
 *
 * @category Viewd
 * @package  Jnjxp\Viewd
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  http://www.gnu.org/licenses/agpl-3.0.txt AGPL V3
 * @link     http://jakejohns.net
 */
class Viewd
{
    /**
     * View object
     *
     * @var View
     *
     * @access protected
     */
    protected $view;

    /**
     * Path prefix for viewd scripts
     *
     * @var string
     *
     * @access protected
     */
    protected $prefix;

    /**
     * Error view name
     *
     * @var string
     *
     * @access protected
     */
    protected $error;

    /**
     * __construct
     *
     * @param View   $view   view renderer
     * @param string $prefix path prefix for viewd scripts
     * @param string $error  name of error script
     *
     * @access public
     */
    public function __construct(View $view, $prefix = 'viewd', $error = 'error')
    {
        $this->view = $view;
        $this->prefix = $prefix;
        $this->error = $error;
    }

    /**
     * __invoke
     *
     * @param Request  $request  server request
     * @param Response $response response
     * @param callable $next     next
     *
     * @return Response
     *
     * @access public
     */
    public function __invoke(
        Request $request,
        Response $response,
        callable $next = null
    ) {
        $name = $this->getName($request);

        if ($this->has($name)) {
            return $this->render($name, $response);
        }

        $newResponse = $next ? $next($request, $response) : $response;

        if ($this->shouldRenderError($request, $newResponse)) {
            return $this->error($newResponse);
        }

        return $newResponse;
    }

    /**
     * ShouldRenderError
     *
     * @param Request  $request  original request
     * @param Response $response response
     *
     * @return mixed
     *
     * @access protected
     */
    protected function shouldRenderError(Request $request, Response $response)
    {
        if ($request->getAttribute('jnjxp/viewd:no-error')) {
            return false;
        }

        if ((string) $response->getBody()) {
            return false;
        }
        return true;
    }

    /**
     * GetName
     *
     * @param Request $request request
     *
     * @return string
     *
     * @access protected
     */
    protected function getName(Request $request)
    {

        if (! $path = $request->getAttribute('jnjxp/viewd:script')) {
            $path = parse_url($request->getRequestTarget(), PHP_URL_PATH);
            $path = trim($path, '/');
        }

        $name = $this->prefix
            . DIRECTORY_SEPARATOR
            . ($path ? $path : 'index');

        return $name;
    }

    /**
     * Has
     *
     * @param string $name name of script
     *
     * @return bool
     *
     * @access protected
     */
    protected function has($name)
    {
        return $this->view->getViewRegistry()->has($name);
    }

    /**
     * Render
     *
     * @param string   $name     name of script to render
     * @param Response $response response
     *
     * @return Response
     *
     * @access protected
     */
    protected function render($name, Response $response)
    {
        $view = $this->view;
        $view->setView($name);
        $response->getBody()->write($view());
        return $response;
    }

    /**
     * Error
     *
     * @param Response $response response
     *
     * @return Response
     *
     * @access protected
     */
    protected function error(Response $response)
    {
        return $this->render(
            $this->error,
            $response->withStatus(404)
        );
    }
}
