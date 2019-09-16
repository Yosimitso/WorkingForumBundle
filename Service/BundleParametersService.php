<?php

namespace Yosimitso\WorkingForumBundle\Service;

class BundleParametersService
{
    public function __construct(...$args)
    {
        foreach ($args as $arg) {
           $this->$arg[0] = $arg[1];
        }
    }

    public function __get($name)
    {
        return $this->$name;
    }
}