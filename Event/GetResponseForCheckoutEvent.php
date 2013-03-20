<?php

namespace Betacie\Bundle\PaypalBundle\Event;

use Betacie\Bundle\PaypalBundle\Entity\Checkout;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;

class GetResponseForCheckoutEvent extends Event
{
    private $checkout;

    private $response;

    public function __construct(Checkout $checkout)
    {
        $this->checkout = $checkout;
    }

    public function getCheckout()
    {
        return $this->checkout;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }
    
    public function hasResponse()
    {
        return null !== $this->response;
    }
}
