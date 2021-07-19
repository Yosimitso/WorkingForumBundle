<?php

namespace Yosimitso\WorkingForumBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

class BundleParametersService
{
    /**
     * @var ContainerInterface 
     */
    private $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

    }

    public function __get($name)
    {
        return $this->container->getParameter('yosimitso_working_forum.'.$name);
    }

    public function get($name)
    {
        return $this->container->getParameter('yosimitso_working_forum.'.$name);
    }
}
