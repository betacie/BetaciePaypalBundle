<?php

namespace Betacie\Bundle\PaypalBundle\Event;

use Betacie\Bundle\PaypalBundle\Response\Response;
use Symfony\Component\EventDispatcher\Event;

class ResponseEvent extends Event
{

    private $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }
}
