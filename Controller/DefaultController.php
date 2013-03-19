<?php

namespace Betacie\Bundle\PaypalBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DefaultController implements ContainerAwareInterface
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Success response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function successAction()
    {
        return $this->container->get('templating')->renderResponse('BetaciePaypalBundle:Default:success.html.twig');
    }

    /**
     * Cancel response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cancelAction()
    {
        return $this->container->get('templating')->renderResponse('BetaciePaypalBundle:Default:cancel.html.twig');
    }
}
