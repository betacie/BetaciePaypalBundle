<?php

namespace Betacie\Bundle\PaypalBundle\Request;

interface RequestInterface
{
    public function toHttpQuery();

    public function add($key, $value);
}
