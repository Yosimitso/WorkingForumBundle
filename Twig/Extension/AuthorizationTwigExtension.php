<?php

namespace Yosimitso\WorkingForumBundle\Twig\Extension;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Yosimitso\WorkingForumBundle\Entity\UserInterface;
use Yosimitso\WorkingForumBundle\Security\AuthorizationGuard;

class AuthorizationTwigExtension extends AbstractExtension
{
    public function __construct(
        protected readonly AuthorizationGuard $authorizationGuard
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'hasModeratorAuthorization',
                [$this, 'hasModeratorAuthorization']
            ),
            new TwigFunction(
                'hasAdminAuthorization',
                [$this, 'hasAdminAuthorization']
            ),
            new TwigFunction(
                'hasUserAuthorization',
                [$this, 'hasUserAuthorization']
            ),
            new TwigFunction(
                'isAnonymous',
                [$this, 'isAnonymous']
            ),
        ];
    }

    public function hasModeratorAuthorization(): bool
    {
        return $this->authorizationGuard->hasModeratorAuthorization();
    }

    public function hasAdminAuthorization(): bool
    {
        return $this->authorizationGuard->hasAdminAuthorization();
    }

    public function hasUserAuthorization(): bool
    {
        return $this->authorizationGuard->hasUserAuthorization();
    }

    public function isAnonymous(): bool
    {
        return $this->authorizationGuard->isAnonymous();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'authorization';
    }
}
