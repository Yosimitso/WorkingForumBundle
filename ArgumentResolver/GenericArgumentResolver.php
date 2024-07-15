<?php

namespace Yosimitso\WorkingForumBundle\ArgumentResolver;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Yosimitso\WorkingForumBundle\Entity\Forum;
use Yosimitso\WorkingForumBundle\Entity\Post;
use Yosimitso\WorkingForumBundle\Entity\Subforum;
use Yosimitso\WorkingForumBundle\Entity\Thread;
use Yosimitso\WorkingForumBundle\Security\AuthorizationGuardInterface;
use Yosimitso\WorkingForumBundle\Service\ThreadService;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Traversable;

class GenericArgumentResolver implements ValueResolverInterface
{
    public function __construct(
        protected readonly EntityManagerInterface $em,
        protected readonly AuthorizationGuardInterface $authorizationGuard,
        protected readonly string $classname
    ) {}

    public function resolve(Request $request, ArgumentMetadata $argument): Traversable|array
    {
        if (!$this->supports($request, $argument)) {
            return [];
        }
        $value =  $request->attributes->get($argument->getName());
        $param = (is_numeric($value)) ? 'id' : 'slug';
        $entity = $this->em->getRepository($this->classname)->findOneBy([$param => $value]);

        if (is_null($entity)) {
            throw new NotFoundHttpException($argument->getName().' "'.$value.'" not found');
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

        $request->attributes->set($argument->getName(), $entity);

        return [$entity];
    }

    function supports(Request $request, ArgumentMetadata $argument)
    {
        return ($this->classname === $argument->getType());
    }
}
