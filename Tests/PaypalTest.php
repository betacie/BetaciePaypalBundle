<?php

namespace Betacie\Bundle\PaypalBundle\Tests;

use Betacie\Bundle\OrderBundle\Entity\Item;
use Betacie\Bundle\PaymentBundle\Transaction\Transaction;
use Betacie\Bundle\PaypalBundle\Paypal;
use Betacie\Bundle\PaypalBundle\Request\ExpressCheckoutRequest;
use Betacie\Bundle\PaypalBundle\Response\Response;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class PaypalTests extends WebTestCase
{

    public function testExpressCheckoutFailure()
    {
        $paypal = $this->getContainer()->get('paypal');

        $failResponse = $paypal->request(new ExpressCheckoutRequest(array(
                'method' => 'SetExpressCheckout',
            )));

        $this->assertFalse($failResponse->isSuccess());
    }

    public function testExpressCheckout()
    {
        $paypal = $this->getContainer()->get('paypal');

        $item1 = (new Item())
            ->setTitle('foo')
            ->setDescription('bar')
            ->setUnitPrice(5)
            ->setQuantity(1)
        ;

        $item2 = (new Item())
            ->setTitle('foobar')
            ->setDescription('bar')
            ->setUnitPrice(8)
            ->setQuantity(2)
        ;

        $transaction = (new Transaction())
            ->addItem($item1)
            ->addItem($item2)
        ;

        $response = $paypal->setExpressCheckout($transaction);

        $this->assertTrue($response->isSuccess());

        $detailResponse = $paypal->getExpressCheckoutDetails($response->get('TOKEN'));

        $this->assertTrue($detailResponse->isSuccess());
        $this->assertEquals($detailResponse->get('AMT'), 21);
        $this->assertEquals($detailResponse->get('L_NAME0'), 'foo');
        $this->assertEquals($detailResponse->get('L_NAME1'), 'foobar');
        $this->assertEquals($detailResponse->get('L_AMT0'), 5);
        $this->assertEquals($detailResponse->get('L_AMT1'), 8);

        $doResponse = $paypal->doExpressCheckoutPayment($detailResponse);

        $this->assertEquals($doResponse->get('L_LONGMESSAGE1'), 'The PayerID value is invalid.');
    }

    public function testMasspay()
    {
        $paypal = $this->getContainer()->get('paypal');

        $response = $paypal->masspay('foobar@email.tld', 5);

        $this->assertTrue($response->isSuccess());
    }

    public function testRefund()
    {
        $paypal = $this->getContainer()->get('paypal');

        $response = $paypal->refund('REFUND');

        $this->assertEquals($response->get('L_LONGMESSAGE0'), 'The transaction id is not valid');
    }

    public function testUrlGetter()
    {
        $router = $this->getMock('Symfony\Component\Routing\RouterInterface');

        $paypal = new Paypal('foo', 'bar', 'baz', $router);

        $response = new Response(array('TOKEN' => 'foobar'));

        $reflectionMethod = new \ReflectionMethod($paypal, 'getEndPoint');
        $reflectionMethod->setAccessible(true);

        $this->assertEquals($reflectionMethod->invoke($paypal), 'https://api-3t.paypal.com/nvp');
        $this->assertEquals('https://www.paypal.com/webscr?cmd=_express-checkout&token=foobar', $paypal->getCheckoutUrl($response));
        $this->assertEquals('https://www.paypal.com/webscr?cmd=_account-authenticate-login&token=foobar', $paypal->getAccountLoginUrl($response));

        $paypal->setDebug(true);

        $this->assertEquals($reflectionMethod->invoke($paypal), 'https://api-3t.sandbox.paypal.com/nvp');
        $this->assertEquals('https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=foobar', $paypal->getCheckoutUrl($response));
        $this->assertEquals('https://www.sandbox.paypal.com/webscr?cmd=_account-authenticate-login&token=foobar', $paypal->getAccountLoginUrl($response));
    }

}
