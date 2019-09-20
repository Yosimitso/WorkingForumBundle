<?php

namespace Yosimitso\WorkingForumBundle\Service;

use Psr\Container\ContainerInterface;

class BundleParametersService
{
    private $container;
    public function __construct(ContainerInterface $container /*...$args*/)
    {
//        foreach ($args as $arg) {
//           $this->$arg[0] = $arg[1];
//        }
        $this->container = $container;

    }

    public function __get($name)
    {
        return $this->container->getParameter('yosimitso_working_forum.'.$name);
    }
}