<?php

namespace Betacie\Bundle\PaypalBundle\Controller;

use Betacie\Bundle\PaypalBundle\Event\CheckoutEvent;
use Betacie\Bundle\PaypalBundle\PaypalEvents;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CheckoutController implements ContainerAwareInterface
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function returnAction()
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $request = $this->container->get('request');
        $paypal = $this->container->get('betacie.paypal');
        $token = $request->get('token');

        // Check if we have a checkout with this token
        if (!$checkout = $this->container->get('betacie.paypal_manager')->findCheckout($token)) {
            return new RedirectResponse($this->container->get('router')->generate('betacie_paypal.default_cancel'));
        }

        $response = $paypal->getExpressCheckoutDetails($token);

        if ($response->isSuccess()) {
            $doResponse = $paypal->doExpressCheckoutPayment($response);

            // DO transaction, recall Paypal API
            if ($doResponse->isSuccess()) {
                if ($doResponse->get('PAYMENTINFO_0_PAYMENTSTATUS') === 'Completed') {
                    $event = new CheckoutEvent($checkout);
                    $this->container->get('event_dispatcher')->dispatch(PaypalEvents::CHECKOUT_COMPLETED, $event);
                }

                // Create Paypal trace, store transaction information in BDD
                $checkout
                    ->setPayerId($response->get('PAYERID'))
                    ->setTransactionId($doResponse->get('PAYMENTINFO_0_TRANSACTIONID'))
                    ->setTransactionType($doResponse->get('PAYMENTINFO_0_TRANSACTIONTYPE'))
                    ->setStatus($doResponse->get('PAYMENTINFO_0_PAYMENTSTATUS'))
                ;

                $em->flush();

                if ($response = $event->getResponse()) {
                    return $response;
                }

                return new RedirectResponse($this->container->get('router')->generate('betacie_paypal.default_success'));
            }
        }

        return new RedirectResponse($this->container->get('router')->generate('betacie_paypal.default_cancel'));
    }

    /**
     * Cancel response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cancelAction()
    {
        $token = $this->container->get('request')->get('token');

        if ($token) {
            $em = $this->container->get('doctrine.orm.entity_manager');

            // Check if we have a checkout with this token
            if (!$checkout = $this->container->get('betacie.paypal_manager')->findCheckout($token)) {
                return new RedirectResponse($this->container->get('router')->generate('betacie_paypal.default_cancel'));
            }

            $response = $this->container->get('betacie.paypal')->getExpressCheckoutDetails($token);

            if ($response->isSuccess() && in_array($response->get('CHECKOUTSTATUS'), array('PaymentActionNotInitiated', 'PaymentActionFailed'))) {
                $event = new CheckoutEvent($checkout);
                $this->container->get('event_dispatcher')->dispatch(PaypalEvents::CHECKOUT_COMPLETED, $event);

                // Paypal trace, store transaction information in BDD
                $checkout
                    ->setPayerId($response->get('PAYERID'))
                    ->setStatus($response->get('CHECKOUTSTATUS'))
                ;

                $em->flush();

                if ($response = $event->getResponse()) {
                    return $response;
                }
            }

            // Redirect without token
            return new RedirectResponse($this->container->get('router')->generate('betacie_paypal.default_cancel'));
        }

        return $this->container->get('templating')->renderResponse('BetaciePaypalBundle:Default:cancel.html.twig');
    }

}
