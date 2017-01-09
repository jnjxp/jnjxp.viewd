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

/**
 * ViewResponder
 *
 * @category Responder
 * @package  Jnjxp\Viewd
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  http://jnj.mit-license.org/ MIT License
 * @link     http://jakejohns.net
 */
class ViewResponder
{

    const VIEW   = 'view';
    const LAYOUT = 'layout';

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
     * @var string
     *
     * @access protected
     */
    protected $prefix = 'viewd/';

    /**
     * Not_found
     *
     * @var string
     *
     * @access protected
     */
    protected $not_found = 'error/404';

    /**
     * Default_layout
     *
     * @var string
     *
     * @access protected
     */
    protected $default_layout = 'default';

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
     * SetPrefix
     *
     * @param mixed $prefix DESCRIPTION
     *
     * @return null
     *
     * @access public
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * SetNotFound
     *
     * @param mixed $script DESCRIPTION
     *
     * @return mixed
     *
     * @access public
     */
    public function setNotFound($script)
    {
        $this->not_found = $script;
    }

    /**
     * SetDefaultLayout
     *
     * @param mixed $layout DESCRIPTION
     *
     * @return mixed
     *
     * @access public
     */
    public function setDefaultLayout($layout)
    {
        $this->default_layout = $layout;
    }

    /**
     * __invoke
     *
     * @param Request  $request  PSR7 Request
     * @param Response $response PSR7 Response
     *
     * @return Response
     *
     * @access public
     */
    public function __invoke(
        Request $request,
        Response $response
    ) {
        $this->request  = $request;
        $this->response = $response;

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
        $this->setScript();
        $this->setLayout();
        $this->setData();
    }

    /**
     * SetScript
     *
     * @return mixed
     *
     * @access protected
     */
    protected function setScript()
    {
        $name = $this->getViewName();
        if (! $this->hasView($name)) {
            return $this->viewNotFound();
        }
        $this->view->setView($name);
    }

    /**
     * HasView
     *
     * @param mixed $name DESCRIPTION
     *
     * @return mixed
     *
     * @access protected
     */
    protected function hasView($name)
    {
        $views = $this->view->getViewRegistry();
        return $views->has($name);
    }

    /**
     * GetViewName
     *
     * @return mixed
     *
     * @access protected
     */
    protected function getViewName()
    {
        $name = $this->request->getAttribute(self::VIEW);
        return $this->prefix . $name;
    }

    /**
     * NotFound
     *
     * @return mixed
     *
     * @access protected
     */
    protected function viewNotFound()
    {
        $this->response = $this->response->withStatus(404);
        $this->view->setView($this->not_found);
    }

    /**
     * SetLayout
     *
     * @return mixed
     *
     * @access protected
     */
    protected function setLayout()
    {
        $layout = $this->request->getAttribute(self::LAYOUT, $this->default_layout);
        if ($layout) {
            $this->view->setLayout($layout);
        }
    }

    /**
     * SetData
     *
     * @return mixed
     *
     * @access protected
     */
    protected function setData()
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
        $body = $this->view->__invoke();
        $this->response->getBody()->write($body);
    }
}
