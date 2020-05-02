<?php

namespace Yosimitso\WorkingForumBundle\ParamConverter;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yosimitso\WorkingForumBundle\Entity\Forum;

class GenericParamConverter implements  ParamConverterInterface
{
    protected $em;
    protected const HANDLED_PARAM_NAME = ['forum', 'subforum', 'thread', 'post'];
    protected $classname;

    public function __construct(EntityManagerInterface $em, $classname)
    {
        $this->em = $em;
        $this->classname = $classname;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        $value =  $request->attributes->get($configuration->getName());
        $param = (is_numeric($value)) ? 'id' : 'slug';

        $entity = $this->em->getRepository($this->classname)->findOneBy([$param => $value]);

        if (is_null($entity)) {
            throw new NotFoundHttpException($configuration->getName().' "'.$value.'" not found');
        }

        $request->attributes->set($configuration->getName(), $entity);

        return true;
    }

    function supports(ParamConverter $configuration)
    {
        return ($this->classname === $configuration->getClass());
    }
}