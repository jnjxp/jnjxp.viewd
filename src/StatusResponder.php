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

use Aura\Payload_Interface\PayloadInterface as Payload;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * StatusResponder
 *
 * @category Responder
 * @package  Jnjxp\Viewd
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  http://www.gnu.org/licenses/agpl-3.0.txt AGPL V3
 * @link     http://jakejohns.net
 */
class StatusResponder extends AbstractResponder
{

    /**
     * Prepare
     *
     * @return void
     *
     * @access protected
     */
    protected function prepare()
    {
        parent::prepare();

        $name = $this->getViewName();
        $this->view->setView($name);

        $method = $this->getMethodForPayload();
        $this->$method();
    }

    /**
     * GetMethodForPayload
     *
     * @return string
     *
     * @access protected
     */
    protected function getMethodForPayload()
    {
        if (! $this->payload) {
            return 'noContent';
        }

        $method = str_replace('_', '', strtolower($this->payload->getStatus()));
        return method_exists($this, $method) ? $method : 'unknown';
    }

    /**
     * GetViewName
     *
     * @return string
     *
     * @access protected
     */
    protected function getViewName()
    {
        if (! $this->payload) {
            return $this->prefix . 'no-content';
        }

        return $this->prefix
            . str_replace('-', '', strtolower($this->payload->getStatus()));
    }

    /**
     * Accepted
     *
     * @return void
     *
     * @access protected
     */
    protected function accepted()
    {
        $this->response = $this->response->withStatus(202);
        $this->view->addData($this->payload->getOutput());
    }

    /**
     * Created
     *
     * @return void
     *
     * @access protected
     */
    protected function created()
    {
        $this->response = $this->response->withStatus(201);
        $this->view->addData($this->payload->getOutput());
    }

    /**
     * Deleted
     *
     * @return void
     *
     * @access protected
     */
    protected function deleted()
    {
        $this->response = $this->response->withStatus(204);
        $this->view->addData($this->payload->getOutput());
    }

    /**
     * Error
     *
     * @return void
     *
     * @access protected
     */
    protected function error()
    {
        $this->response = $this->response->withStatus(500);
        $this->view->addData(
            [
                'input' => $this->payload->getInput(),
                'error' => $this->payload->getOutput(),
            ]
        );
    }

    /**
     * Failure
     *
     * @return void
     *
     * @access protected
     */
    protected function failure()
    {
        $this->response = $this->response->withStatus(400);
        $this->view->addData($this->payload->getInput());
    }

    /**
     * Found
     *
     * @return void
     *
     * @access protected
     */
    protected function found()
    {
        $this->response = $this->response->withStatus(200);
        $this->view->addData($this->payload->getOutput());
    }

    /**
     * NoContent
     *
     * @return void
     *
     * @access protected
     */
    protected function noContent()
    {
        $this->response = $this->response->withStatus(204);
    }

    /**
     * NotAuthenticated
     *
     * @return void
     *
     * @access protected
     */
    protected function notAuthenticated()
    {
        $this->response = $this->response->withStatus(401);
        $this->view->addData($this->payload->getInput());
    }

    /**
     * NotAuthorized
     *
     * @return void
     *
     * @access protected
     */
    protected function notAuthorized()
    {
        $this->response = $this->response->withStatus(403);
        $this->view->addData($this->payload->getInput());
    }

    /**
     * NotFound
     *
     * @return void
     *
     * @access protected
     */
    protected function notFound()
    {
        $this->response = $this->response->withStatus(404);
        $this->view->addData($this->payload->getInput());
    }

    /**
     * NotValid
     *
     * @return void
     *
     * @access protected
     */
    protected function notValid()
    {
        $this->response = $this->response->withStatus(422);
        $this->view->addData(
            [
            'input' => $this->payload->getInput(),
            'output' => $this->payload->getOutput(),
            'messages' => $this->payload->getMessages(),
            ]
        );
    }

    /**
     * Processing
     *
     * @return void
     *
     * @access protected
     */
    protected function processing()
    {
        $this->response = $this->response->withStatus(203);
        $this->view->addData($this->payload->getOutput());
    }

    /**
     * Success
     *
     * @return void
     *
     * @access protected
     */
    protected function success()
    {
        $this->response = $this->response->withStatus(200);
        $this->view->addData($this->payload->getOutput());
    }

    /**
     * Unknown
     *
     * @return void
     *
     * @access protected
     */
    protected function unknown()
    {
        $this->response = $this->response->withStatus(500);
        $this->view->addData(
            [
            'error' => 'Unknown domain payload status',
            'status' => $this->payload->getStatus(),
            ]
        );
    }

    /**
     * Updated
     *
     * @return void
     *
     * @access protected
     */
    protected function updated()
    {
        $this->response = $this->response->withStatus(303);
        $this->view->addData($this->payload->getOutput());
    }
}
