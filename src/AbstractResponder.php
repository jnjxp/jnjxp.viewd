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
 * @category  Responder
 * @package   Jnjxp\Viewd
 * @author    Jake Johns <jake@jakejohns.net>
 * @copyright 2016 Jake Johns
 * @license   http://www.gnu.org/licenses/agpl-3.0.txt AGPL V3
 * @link      http://jakejohns.net
 */

namespace Jnjxp\Viewd;

use Aura\View\View;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Aura\Payload_Interface\PayloadInterface as Payload;

/**
 * Abstract Responder
 *
 * @category Responder
 * @package  Jnjxp\Viewd
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  http://www.gnu.org/licenses/agpl-3.0.txt AGPL V3
 * @link     http://jakejohns.net
 */
abstract class AbstractResponder
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
     * Prefix
     *
     * @var null|string
     *
     * @access protected
     */
    protected $prefix;


    /**
     * __construct
     *
     * @param View $view Aura\View object
     *
     * @access public
     */
    public function __construct(View $view)
    {
        $this->view = $view;
    }

    /**
     * Set prefix
     *
     * @param mixed $prefix DESCRIPTION
     *
     * @return mixed
     *
     * @access public
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * __invoke
     *
     * @param Request  $request  PSR7 Request
     * @param Response $response PSR7 Response
     * @param Payload  $payload  Domain Payload
     *
     * @return Response
     *
     * @access public
     */
    public function __invoke(
        Request $request,
        Response $response,
        Payload $payload = null
    ) {
        $this->request  = $request;
        $this->response = $response;
        $this->payload  = $payload;

        $this->prepare();
        $this->render();

        return $this->response;
    }

    /**
     * Prepare
     *
     * @return mixed
     *
     * @access protected
     */
    protected function prepare()
    {
        $this->view->addData(['request' => $this->request]);
    }

    /**
     * Render
     *
     * @return mixed
     *
     * @access protected
     */
    protected function render()
    {
        $view = $this->view;
        $this->response->getBody()->write($view());
    }
}
