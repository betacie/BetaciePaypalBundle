<?php

namespace Betacie\Bundle\PaypalBundle\Request;

class Request implements RequestInterface
{

    private $parameters = array();

    public function __construct(array $parameters = array())
    {
        $this->parameters = $parameters;
    }

    /**
     * Add a parameter to the request
     *
     * @param  mixed                                                       $key
     * @param  mixed                                                       $value
     * @return \Betacie\Bundle\PaypalBundle\Request\ExpressCheckoutRequest
     */
    public function add($key, $value)
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    /**
     * URL query string
     *
     * @return string
     */
    public function toHttpQuery()
    {
        return http_build_query($this->parameters);
    }

}
