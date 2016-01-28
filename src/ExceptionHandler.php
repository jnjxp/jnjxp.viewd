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
 * @category  Middleware
 * @package   Jnjxp\Viewd
 * @author    Jake Johns <jake@jakejohns.net>
 * @copyright 2016 Jake Johns
 * @license   http://www.gnu.org/licenses/agpl-3.0.txt AGPL V3
 * @link      http://jakejohns.net
 */


namespace Jnjxp\Viewd;

use Aura\View\View;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * ExceptionHandler
 *
 * @category Middleware
 * @package  Jnjxp\Viewd
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  http://www.gnu.org/licenses/agpl-3.0.txt AGPL V3
 * @link     http://jakejohns.net
 */
class ExceptionHandler
{

    /**
     * Name of view to render
     *
     * @var string
     *
     * @access protected
     */
    protected $viewName = 'exception';

    /**
     * View
     *
     * @var View
     *
     * @access protected
     */
    protected $view;

    /**
     * Exception Response
     *
     * @var Response
     *
     * @access protected
     */
    protected $exceptionResponse;

    /**
     * __construct
     *
     * @param View     $view     Aura\View Object
     * @param Response $response Response for Exception 
     *
     * @return mixed
     *
     * @access public
     */
    public function __construct(View $view, Response $response)
    {
        $this->view = $view;
        $this->response = $response;
    }

    /**
     * __invoke
     *
     * @param Request  $request  PSR7 Request
     * @param Response $response PSR7 Response
     * @param callable $next     Next middleware chain
     *
     * @return Response
     *
     * @access public
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        try {
            $response = $next($request, $response);
        } catch (Exception $e) {
            $response = $this->exceptionResponse->withStatus(500);
            $view = $this->view;
            $view->addData(
                [
                    'request' => $request,
                    'exception' => $e
                ]
            );
            $view->setView($this->viewName);
            $response->getBody()->write($view());
        }
        return $response;
    }
}
