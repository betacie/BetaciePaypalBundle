<?php

namespace Betacie\Bundle\PaypalBundle\Listener;

use Betacie\Bundle\PaypalBundle\Event\ResponseEvent;
use Betacie\Bundle\PaypalBundle\Model\PaypalManager;

class CheckoutListener
{

    private $manager;

    public function __construct(PaypalManager $manager)
    {
        $this->manager = $manager;
    }

    public function onSetCheckoutSuccess(ResponseEvent $event)
    {
        $response = $event->getResponse();

        $this->manager->printCheckout($response->getToken());
    }
}
