<?php

namespace Yosimitso\WorkingForumBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

class BundleParametersService
{
    public function __construct(protected readonly ContainerInterface $container) {}

    public function __get($name)
    {
        return $this->container->getParameter('yosimitso_working_forum.'.$name);
    }

    public function get($name)
    {
        return $this->container->getParameter('yosimitso_working_forum.'.$name);
    }
}
