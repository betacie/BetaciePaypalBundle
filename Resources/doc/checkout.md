Express Checkout
================

To use Express Checkout you must create a class or an entity that implement [`TransactionInterface`](https://github.com/betacie/BetaciePaypalBundle/blob/master/Model/TransactionInterface.php).
When you wish to submit an Express Checkout request you must follow this workflow:

    SetExpressCheckout -> GetExpressCheckoutDetails -> DoExpressCheckoutPayment

But the bundle you simplified the task, you only need to call `Paypal::setExpressCheckout` giving him your transaction as parameter.

```php
$response = $this->get('betacie.paypal')->setExpressCheckout($transaction);
if ($response->isSuccess()) {
    return $this->redirect($this->get('betacie.paypal')->getCheckoutUrl($response));
}
```

When you request the Paypal API `betacie.paypal` service return to you a [`Response`](https://github.com/betacie/BetaciePaypalBundle/blob/master/Response/Response.php)
so you can check if request is executed correctly. All you have to do next is to redirect your user on Paypal website. Everything else will be done by the bundle.

After initiating an Express Checkout paypal will return a token that is registered by the bundle so you can link your entities with this token to
follow the payment status.

```php
<?php

namespace Acme\Bundle\PaymentBundle\Listener;

use Betacie\Bundle\PaypalBundle\PaypalEvents;
use Betacie\Bundle\PaypalBundle\Event\SetCheckoutSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PaypalListener implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return array(
            PaypalEvents::SET_CHECKOUT_SUCCESS => 'onSetCheckoutSuccess',
        );
    }

    public function onSetCheckoutSuccess(SetCheckoutSuccessEvent $event)
    {
        $checkout = $event->getCheckout();
        $transaction = $event->getTransaction();

        // Do what you want in your database
    }

}
```

Register your listener:

```yml
services:
    acme_payment.paypal_listener:
        class: Acme\Bundle\PaymentBundle\Listener\PaypalListener
        tags:
            - { name: kernel.event_subscriber }
```

Validate payment
----------------

Once your user has paid on paypal he is redirected on your website and more specifically [here](https://github.com/betacie/BetaciePaypalBundle/blob/master/Controller/CheckoutController.php#L21).
In this controller if checkout is completed an event is fired then you can add your own logic. You can also redirect user where ever you want.

```php
<?php

namespace Acme\Bundle\PaymentBundle\Listener;

use Betacie\Bundle\PaypalBundle\PaypalEvents;
use Betacie\Bundle\PaypalBundle\Event\GetResponseForCheckoutEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PaypalListener implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return array(
            PaypalEvents::DO_CHECKOUT_COMPLETED => 'onDoCheckoutCompleted',
        );
    }

    public function onDoCheckoutCompleted(GetResponseForCheckoutEvent $event)
    {
        $checkout = $event->getCheckout();

        // Insert some stuff in database

        $event->setResponse(new RedirectResponse('/'));
    }

}
```

Cancel payment
--------------

The same process is used if the user cancel his payment on paypal web site. This time you need to subscribe to `Betacie\Bundle\PaypalBundle\PaypalEvents::CHECKOUT_CANCELLED`