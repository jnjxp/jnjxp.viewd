<?php
//@codingStandardsIgnoreFile

namespace Jnjxp\Viewd\Fake;

use Jnjxp\Viewd\StatusResponder;

class Responder extends StatusResponder
{
    protected function foo()
    {
        $this->response->getBody()->write('foo');
    }
}
