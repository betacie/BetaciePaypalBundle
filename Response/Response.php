<?php

namespace Betacie\Bundle\PaypalBundle\Response;

class Response
{
    private $parameters;

    public function __construct(array $parameters = array())
    {
        $this->parameters = $parameters;
    }

    /**
     * Get token from paypal response
     *
     * @return string|null
     */
    public function getToken()
    {
        return $this->get('TOKEN');
    }

    /**
     * Get parameter from response
     *
     * @param  string $key
     * @return mixed
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->parameters)) {
            return $this->parameters[$key];
        }
    }

    /**
     * Check if last request was success
     *
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->get('ACK') === 'Success';
    }
}
