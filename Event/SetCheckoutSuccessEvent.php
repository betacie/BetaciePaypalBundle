<?php

namespace Betacie\Bundle\PaypalBundle\Event;

use Betacie\Bundle\PaypalBundle\Entity\Checkout;
use Betacie\Bundle\PaypalBundle\Model\TransactionInterface;
use Symfony\Component\EventDispatcher\Event;

class SetCheckoutSuccessEvent extends Event
{
    private $checkout;
    private $transaction;
    
    function __construct(Checkout $checkout, TransactionInterface $transaction)
    {
        $this->checkout = $checkout;
        $this->transaction = $transaction;
    }

    public function getCheckout()
    {
        return $this->checkout;
    }
    
    public function getTransaction()
    {
        return $this->transaction;
    }
}