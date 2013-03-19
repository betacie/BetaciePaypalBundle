<?php

namespace Betacie\Bundle\PaypalBundle\Tests\Fixtures;

use Betacie\Bundle\OrderBundle\Entity\Item;
use Betacie\Bundle\OrderBundle\Entity\Order;
use Betacie\Bundle\PaymentBundle\Entity\Payment;
use Betacie\Bundle\PaymentBundle\Entity\Paypal;
use Betacie\Bundle\UserBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadData extends AbstractFixture
{

    public function load(ObjectManager $manager)
    {
        $customer = (new User())
            ->setUsername('customer')
            ->setPlainPassword('customer')
            ->setEmail('customer@email.tld')
            ->setEnabled(true)
        ;

        $seller = (new User())
            ->setUsername('seller')
            ->setPlainPassword('seller')
            ->setEmail('seller@email.tld')
            ->setEnabled(true)
        ;

        $item = (new Item())
            ->setQuantity(1)
            ->setUnitPrice(5)
            ->setTitle('foobar')
            ->setDescription('foobar')
            ->setType(Item::TYPE_SERVICE)
        ;
        $order = (new Order())
            ->addItem($item)
            ->setSeller($seller)
            ->setCustomer($customer)
        ;

        $payment = (new Payment())
            ->setType(Payment::TYPE_PAYPAL)
            ->addItem($item)
        ;

        $paypal = (new Paypal())
            ->setToken('TOKEN')
            ->setPayment($payment)
        ;

        $manager->persist($customer);
        $manager->persist($seller);
        $manager->persist($order);
        $manager->persist($item);
        $manager->persist($payment);
        $manager->persist($paypal);

        $manager->flush();
    }

}
