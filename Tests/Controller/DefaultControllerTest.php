<?php

namespace Betacie\Bundle\PaypalBundle\Tests\Controller;

use Betacie\Bundle\PaypalBundle\Response\Response;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * @group controller
 */
class DefaultControllerTest extends WebTestCase
{

    public function setUp()
    {
        $this->loadFixtures(array(
            'Betacie\Bundle\PaypalBundle\Tests\Fixtures\LoadData',
        ));
    }

    public function testSuccess()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getUrl('betacie_paypal.default_success'), array(), array(), array(
            'PHP_AUTH_USER' => 'customer',
            'PHP_AUTH_PW' => 'customer',
            ));

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Votre paiement a été effectué avec succés")')->count());
    }

    public function testCancel()
    {
        $mockPaypal = $this->getServiceMockBuilder('paypal')->getMock();
        $mockPaypal->expects($this->any())
            ->method('getExpressCheckoutDetails')
            ->will($this->returnValue(new Response(array(
                        'ACK' => 'Success',
                        'PAYERID' => 'PAYERID',
                        'CHECKOUTSTATUS' => 'PaymentActionFailed'
                    ))));
        $mockPaypal->expects($this->any())
            ->method('doExpressCheckoutPayment')
            ->will($this->returnValue(new Response(array(
                        'ACK' => 'Success',
                        'PAYMENTINFO_0_TRANSACTIONID' => 'TRANSACTIONID',
                        'PAYMENTINFO_0_TRANSACTIONTYPE' => 'ExpressCheckout',
                        'PAYMENTINFO_0_PAYMENTSTATUS' => 'Failed',
                    ))));

        $client = static::createClient();
        $client->getContainer()->set('paypal', $mockPaypal);

        $uri = $this->getUrl('betacie_paypal.default_cancel', array('token' => 'TOKEN'));

        $client->request('GET', $uri, array(), array(), array(
            'PHP_AUTH_USER' => 'customer',
            'PHP_AUTH_PW' => 'customer',
        ));

        $paypal = $this->getContainer()->get('doctrine')
            ->getEntityManager()
            ->getRepository('BetaciePaymentBundle:Paypal')
            ->findOneBy(array(
            'token' => 'TOKEN',
            ));

        $this->assertEquals($paypal->getPayerId(), 'PAYERID');
        $this->assertFalse($paypal->getPayment()->isPaid());
    }

    public function testWithoutToken()
    {
        $uri = $this->getUrl('betacie_paypal.default_cancel');

        $client = static::createClient();
        $crawler = $client->request('GET', $uri, array(), array(), array(
            'PHP_AUTH_USER' => 'customer',
            'PHP_AUTH_PW' => 'customer',
        ));

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Un problème est survenue")')->count());
    }

}
