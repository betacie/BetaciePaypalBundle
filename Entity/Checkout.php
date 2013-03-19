<?php

namespace Betacie\Bundle\PaypalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Betacie\Bundle\PaymentBundle\Entity\Paypal
 *
 * @ORM\Table(name="paypal_checkout")
 * @ORM\Entity
 */
class Checkout
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $token
     *
     * @ORM\Column(name="token", type="string", length=255)
     */
    private $token;

    /**
     * @var string $payerId
     *
     * @ORM\Column(name="payer_id", type="string", length=255, nullable=true)
     */
    private $payerId;

    /**
     * @var string $transactionId
     *
     * @ORM\Column(name="transaction_id", type="string", length=255, nullable=true)
     */
    private $transactionId;

    /**
     * @var string $transactionType
     *
     * @ORM\Column(name="transaction_type", type="string", length=255, nullable=true)
     */
    private $transactionType;

    /**
     * @var string $status
     *
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set token
     *
     * @param  string $token
     * @return Paypal
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set payerId
     *
     * @param  string $payerId
     * @return Paypal
     */
    public function setPayerId($payerId)
    {
        $this->payerId = $payerId;

        return $this;
    }

    /**
     * Get payerId
     *
     * @return string
     */
    public function getPayerId()
    {
        return $this->payerId;
    }

    /**
     * Set transactionId
     *
     * @param  string $transactionId
     * @return Paypal
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    /**
     * Get transactionId
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * Set transactionType
     *
     * @param  string $transactionType
     * @return Paypal
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;

        return $this;
    }

    /**
     * Get transactionType
     *
     * @return string
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * set status
     *
     * @param  string $status
     * @return Paypal
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

}
