<?php

namespace Yosimitso\WorkingForumBundle\ParamConverter;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Yosimitso\WorkingForumBundle\Entity\Forum;
use Yosimitso\WorkingForumBundle\Entity\Post;
use Yosimitso\WorkingForumBundle\Entity\Subforum;
use Yosimitso\WorkingForumBundle\Entity\Thread;
use Yosimitso\WorkingForumBundle\Security\AuthorizationGuardInterface;
use Yosimitso\WorkingForumBundle\Service\ThreadService;

class GenericParamConverter implements  ParamConverterInterface
{
    protected EntityManagerInterface $em;
    protected string $classname;
    protected AuthorizationGuardInterface $authorizationGuard;

    public function __construct(EntityManagerInterface $em, AuthorizationGuardInterface $authorizationGuard, string $classname)
    {
        $this->em = $em;
        $this->authorizationGuard = $authorizationGuard;
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

        $subforumAuthorization = null;
        if ($this->classname === Subforum::class) {
            $subforumAuthorization = $entity;
        } elseif ($this->classname === Thread::class) {
            $subforumAuthorization = $entity->getSubforum();
        } elseif ($this->classname === Post::class) {
            $subforumAuthorization = $entity->getThread()->getSubforum();
        }

        if (!is_null($subforumAuthorization) && !$this->authorizationGuard->hasSubforumAccess($subforumAuthorization)) {
            throw new UnauthorizedHttpException('Forbidden');
        }

        $request->attributes->set($configuration->getName(), $entity);

        return true;
    }

    function supports(ParamConverter $configuration)
    {
        return ($this->classname === $configuration->getClass());
    }
}
