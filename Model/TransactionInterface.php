<?php

namespace Betacie\Bundle\PaypalBundle\Model;

interface TransactionInterface
{
    public function addItem(ItemInterface $item);

    public function getAmount();

    public function getItems();
}
