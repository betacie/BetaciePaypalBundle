<?php

namespace Betacie\Bundle\PaypalBundle\Model;

use Betacie\Bundle\PaypalBundle\Entity\Checkout;
use Doctrine\ORM\EntityManager;

class PaypalManager
{

    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function findCheckout($token)
    {
        return $this->em->getRepository('BetaciePaypalBundle:Checkout')->findOneBy(array(
                'token' => $token,
        ));
    }

    public function printCheckout($token)
    {
        $checkout = new Checkout();
        $checkout->setToken($token);

        $this->em->persist($checkout);
        $this->em->flush();
    }
}
