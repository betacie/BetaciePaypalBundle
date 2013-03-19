<?php

namespace Betacie\Bundle\PaypalBundle\Tests\Request;

use Betacie\Bundle\OrderBundle\Entity\Item;
use Betacie\Bundle\OrderBundle\Entity\Order;
use Betacie\Bundle\PaymentBundle\Transaction\Transaction;
use Betacie\Bundle\PaypalBundle\Request\ExpressCheckoutRequest;
use Betacie\Bundle\ServiceBundle\Entity\Service;
use Betacie\Bundle\UserBundle\Entity\User;

class ExpressCheckoutRequestTest extends \PHPUnit_Framework_TestCase
{

    public function testAddTransaction()
    {
        $service = new Service();
        $service->setTitle('foobar');

        $item1 = (new Item())
            ->setDescription('foo')
            ->setTitle('bar')
            ->setQuantity(1)
            ->setUnitPrice(5)
        ;

        $item2 = (new Item())
            ->setDescription('foobar')
            ->setTitle('barbaz')
            ->setQuantity(2)
            ->setUnitPrice(7)
        ;

        $order = new Order();
        $order
            ->setService($service)
            ->setCustomer(new User())
            ->setSeller(new User())
            ->addItem($item1)
            ->addItem($item2)
        ;

        $transaction = Transaction::createFromOrder($order);

        $request = new ExpressCheckoutRequest();

        $expected = array(
            'PAYMENTREQUEST_0_AMT' => 19,
            'PAYMENTREQUEST_0_ITEMAMT' => 19,
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR',
            'L_PAYMENTREQUEST_0_NAME0' => 'bar',
            'L_PAYMENTREQUEST_0_DESC0' => 'foo',
            'L_PAYMENTREQUEST_0_AMT0' => 5,
            'L_PAYMENTREQUEST_0_QTY0' => 1,
            'L_PAYMENTREQUEST_0_NAME1' => 'barbaz',
            'L_PAYMENTREQUEST_0_DESC1' => 'foobar',
            'L_PAYMENTREQUEST_0_AMT1' => 7,
            'L_PAYMENTREQUEST_0_QTY1' => 2,
        );

        $this->assertInstanceOf('Betacie\Bundle\PaypalBundle\Request\ExpressCheckoutRequest', $request->addTransaction($transaction));
        $this->assertEquals(http_build_query($expected), $request->toHttpQuery());
    }

}
