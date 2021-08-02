<?php

namespace Yosimitso\WorkingForumBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Yosimitso\WorkingForumBundle\Entity\Subforum;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


interface AuthorizationGuardInterface
{
    public function __construct(AuthorizationCheckerInterface $authorizatonChecker, TokenStorageInterface $tokenStorage, bool $allowAnonymousRead);
    
    public function hasModeratorAuthorization() : bool;
    
    public function hasAdminAuthorization() : bool;
    
    public function hasSubforumAccessList(array $subforumList) : array;
    
    public function hasSubforumAccess(Subforum $subforum) : bool;
    
    public function hasUserAuthorization() : bool;
    
    public function getErrorMessage() : ?string;

    public function filterForumAccess(array $forums) : void;
}
