<?php

namespace Betacie\Bundle\PaypalBundle\Model;

interface ItemInterface
{

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity();

    /**
     * Get unitPrice
     *
     * @return float
     */
    public function getUnitPrice();

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Get amount
     *
     * @return mixed
     */
    public function getAmount();
}
