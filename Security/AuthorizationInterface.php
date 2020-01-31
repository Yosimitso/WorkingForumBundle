<?php

namespace Yosimitso\WorkingForumBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Interface AuthorizationInterface
 * @package Yosimitso\WorkingForumBundle\Security
 */
interface AuthorizationInterface
{
    public function __construct(AuthorizationChecker $securityChecker, TokenStorageInterface $tokenStorage, $allowAnonymousRead);
    
    public function hasModeratorAuthorization();
    
    public function hasAdminAuthorization();
    
    public function hasSubforumAccessList($subforumList);
    
    public function hasSubforumAccess($subforum);
    
    public function hasUserAuthorization();
    
    public function getErrorMessage();

}
