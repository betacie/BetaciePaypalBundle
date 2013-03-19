<?php

namespace Betacie\Bundle\PaypalBundle;

class PaypalEvents
{
    const SET_CHECKOUT_SUCCESS = 'betacie.set_checkout.success';
    const CHECKOUT_COMPLETED = 'betacie.checkout.completed';
    const CHECKOUT_CANCELLED= 'betacie.checkout.cancelled';
}
