<?php

namespace Betacie\Bundle\PaypalBundle\Request;

use Betacie\Bundle\PaypalBundle\Model\TransactionInterface;

class ExpressCheckoutRequest extends Request
{

    /**
     * Add parameters to the request from a transaction
     *
     * @param  Betacie\Bundle\PaypalBundle\Model\TransactionInterface      $transaction
     * @return \Betacie\Bundle\PaypalBundle\Request\ExpressCheckoutRequest
     */
    public function addTransaction(TransactionInterface $transaction)
    {
        $this
            ->add('PAYMENTREQUEST_0_AMT', $transaction->getAmount())
            ->add('PAYMENTREQUEST_0_ITEMAMT', $transaction->getAmount())
            ->add('PAYMENTREQUEST_0_CURRENCYCODE', 'EUR')
        ;

        foreach ($transaction->getItems() as $key => $value) {
            $this
                ->add('L_PAYMENTREQUEST_0_NAME' . $key, $value->getTitle())
                ->add('L_PAYMENTREQUEST_0_DESC' . $key, $value->getDescription())
                ->add('L_PAYMENTREQUEST_0_AMT' . $key, $value->getUnitPrice())
                ->add('L_PAYMENTREQUEST_0_QTY' . $key, $value->getQuantity())
            ;
        }

        return $this;
    }

}
