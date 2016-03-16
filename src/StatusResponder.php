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
     * Error
     *
     * @return void
     *
     * @access protected
     */
    protected function error()
    {
        $this->response = $this->response->withStatus(500);
        $exception = $this->payload->getOutput();
        $class = get_class($exception);
        $message = $exception->getMessage();
        $this->response->getBody()->write(
            sprintf(
                'Error %s : %s',
                $class, $message
            )
        );
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
        $this->response->getBody()->write('No Content');
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
        $this->response->getBody()->write(
            sprintf(
                'Unknown Status: "%s"',
                $this->payload->getStatus()
            )
        );
    }

}
