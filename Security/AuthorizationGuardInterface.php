<?php

namespace Yosimitso\WorkingForumBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Yosimitso\WorkingForumBundle\Entity\Subforum;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


interface AuthorizationGuardInterface
{
    public function __construct(AuthorizationCheckerInterface $authorizatonChecker, TokenStorageInterface $tokenStorage, $allowAnonymousRead);
    
    public function hasModeratorAuthorization();
    
    public function hasAdminAuthorization();
    
    public function hasSubforumAccessList(array $subforumList);
    
    public function hasSubforumAccess(Subforum $subforum);
    
    public function hasUserAuthorization();
    
    public function getErrorMessage();

}
