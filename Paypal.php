<?php

namespace Betacie\Bundle\PaypalBundle;

use Betacie\Bundle\PaypalBundle\Event\SetCheckoutSuccessEvent;
use Betacie\Bundle\PaypalBundle\Model\PaypalManager;
use Betacie\Bundle\PaypalBundle\Model\TransactionInterface;
use Betacie\Bundle\PaypalBundle\PaypalEvents;
use Betacie\Bundle\PaypalBundle\Request\ExpressCheckoutRequest;
use Betacie\Bundle\PaypalBundle\Request\Request;
use Betacie\Bundle\PaypalBundle\Request\RequestInterface;
use Betacie\Bundle\PaypalBundle\Response\Response;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\RouterInterface;

class Paypal
{

    const VERSION = '95.0';

    private $user;
    private $password;
    private $signature;
    private $manager;
    private $router;
    private $dispatcher;
    private $debug;

    public function __construct($user, $password, $signature, PaypalManager $manager, RouterInterface $router, EventDispatcherInterface $dispatcher, $debug = false)
    {
        $this->user = $user;
        $this->password = $password;
        $this->signature = $signature;
        $this->manager = $manager;
        $this->router = $router;
        $this->dispatcher = $dispatcher;
        $this->setDebug($debug);
    }

    /**
     * Call "SetExpressCheckout" from paypal API
     *
     * @param  \Betacie\Bundle\PaypalBundle\Model\TransactionInterface $transaction
     * @return \Betacie\Bundle\PaypalBundle\Response\Response
     */
    public function setExpressCheckout(TransactionInterface $transaction)
    {
        $request = new ExpressCheckoutRequest(array(
            'METHOD' => 'SetExpressCheckout',
            'RETURNURL' => $this->router->generate('betacie_paypal.checkout_return', array(), true),
            'CANCELURL' => $this->router->generate('betacie_paypal.checkout_cancel', array(), true),
        ));

        $request->addTransaction($transaction);

        $response = $this->request($request);

        if ($response->isSuccess()) {
            $checkout = $this->manager->printCheckout($response->getToken());
            $this->dispatcher->dispatch(PaypalEvents::SET_CHECKOUT_SUCCESS, new SetCheckoutSuccessEvent($checkout, $transaction));
        }

        return $response;
    }

    /**
     * Call "GetExpressCheckoutDetails" from paypal API
     *
     * @param  string                                         $token
     * @return \Betacie\Bundle\PaypalBundle\Response\Response
     */
    public function getExpressCheckoutDetails($token)
    {
        return $this->request(new Request(array(
                'METHOD' => 'GetExpressCheckoutDetails',
                'TOKEN' => $token,
        )));
    }

    /**
     * Call "DoExpressCheckoutPayment" from paypal API
     *
     * @param  \Betacie\Bundle\PaypalBundle\Response\Response $response
     * @return \Betacie\Bundle\PaypalBundle\Response\Response
     */
    public function doExpressCheckoutPayment(Response $response)
    {
        return $this->request(new Request(array(
                'METHOD' => 'DoExpressCheckoutPayment',
                'TOKEN' => $response->get('TOKEN'),
                'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
                'PAYERID' => $response->get('PAYERID'),
                'PAYMENTREQUEST_0_AMT' => $response->get('PAYMENTREQUEST_0_AMT'),
                'PAYMENTREQUEST_0_CURRENCYCODE' => $response->get('PAYMENTREQUEST_0_CURRENCYCODE'),
        )));
    }

    /**
     * Call "MassPay" paypal API
     *
     * @param  string                                         $receiverEmail
     * @param  mixed                                          $amount
     * @return \Betacie\Bundle\PaypalBundle\Response\Response
     */
    public function masspay($receiverEmail, $amount)
    {
        return $this->request(new Request(array(
                'METHOD' => 'MassPay',
                'RECEIVERTYPE' => 'EmailAddress',
                'L_EMAIL0' => $receiverEmail,
                'L_AMT0' => $amount,
                'CURRENCYCODE' => 'EUR',
        )));
    }

    /**
     * Call "RefundTransaction" paypal API
     *
     * @param  string                                         $transactionId
     * @return \Betacie\Bundle\PaypalBundle\Response\Response
     */
    public function refund($transactionId)
    {
        return $this->request(new Request(array(
                'METHOD' => 'RefundTransaction',
                'TRANSACTIONID' => $transactionId,
        )));
    }

    /**
     * Call paypal API
     *
     * @param  \Betacie\Bundle\PaypalBundle\Request\RequestInterface $parameters
     * @return \Betacie\Bundle\PaypalBundle\Response\Response
     */
    public function request(RequestInterface $request)
    {
        $request->add('VERSION', self::VERSION);
        $request->add('USER', $this->user);
        $request->add('PWD', $this->password);
        $request->add('SIGNATURE', $this->signature);

        $curlOptions = array(
            CURLOPT_URL => $this->getEndPoint(),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_POSTFIELDS => $request->toHttpQuery(),
        );

        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $this->_errors = curl_error($ch);
            curl_close($ch);

            return false;
        } else {
            curl_close($ch);
            $responseArray = array();
            parse_str($response, $responseArray);

            return new Response($responseArray);
        }
    }

    /**
     * Enable or disable debug mode, user for chose end point url
     *
     * @param boolean $debug
     */
    public function setDebug($debug)
    {
        $this->debug = (boolean) $debug;
    }

    /**
     * Get end point
     *
     * @return string
     */
    private function getEndPoint()
    {
        if ($this->debug) {
            return 'https://api-3t.sandbox.paypal.com/nvp';
        } else {
            return 'https://api-3t.paypal.com/nvp';
        }
    }

    /**
     * Get redirect url for an express checkout
     *
     * @param  \Betacie\Bundle\PaypalBundle\Response\Response $response
     * @return string
     */
    public function getCheckoutUrl(Response $response)
    {
        if ($this->debug) {
            return 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=' . $response->getToken();
        } else {
            return 'https://www.paypal.com/webscr?cmd=_express-checkout&token=' . $response->getToken();
        }
    }

    /**
     * Get url for redirect user on paypal authentication
     *
     * @param  \Betacie\Bundle\PaypalBundle\Response\Response $response
     * @return string
     */
    public function getAccountLoginUrl(Response $response)
    {
        if ($this->debug) {
            return 'https://www.sandbox.paypal.com/webscr?cmd=_account-authenticate-login&token=' . $response->getToken();
        } else {
            return 'https://www.paypal.com/webscr?cmd=_account-authenticate-login&token=' . $response->getToken();
        }
    }

}
