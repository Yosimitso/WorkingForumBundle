<?php

namespace Yosimitso\WorkingForumBundle\Security;


class Authorization
{
    private $securityChecker;
    private $errorMessage;
    private $tokenStorage;
    private $allowAnonymousRead;

    public function __construct(\Symfony\Component\Security\Core\Authorization\AuthorizationChecker $securityChecker, $tokenStorage, $allowAnonymousRead) {
        $this->securityChecker = $securityChecker;
        $this->tokenStorage = $tokenStorage;
        $this->allowAnonymousRead = $allowAnonymousRead;
    }
    public function hasModeratorAuthorization() {
        if ($this->securityChecker->isGranted('ROLE_ADMIN') || $this->get('security.authorization_checker')->isGranted('ROLE_MODERATOR')) {
            return true;
        }
        else {
            $this->setErrorMessage('restricted_action');
            return false;
        }
    }

    public function hasAdminAuthorization()
    {
        if ($this->securityChecker->isGranted('ROLE_ADMIN')) {
            return true;
        }
        else {
            $this->setErrorMessage('restricted_action');
            return false;
        }
    }

    public function hasUserAuthorization()
    {
        $user = $this->tokenStorage->getToken()->getUser();
        if (is_object($user) && $user->isBanned()) {
            $this->get('session')->getFlashBag()->add(
                'error',
                $this->get('translator')->trans('message.banned', [], 'YosimitsoWorkingForumBundle')
            )
            ;
            $this->setErrorMessage('banned');
            return false;
        }

        if (is_object($user) || $this->allowAnonymousRead) {
            return true;
        }

        $this->setErrorMessage('must_be_logged');
        return false;


    }

    public function hasSubforumAccess($subforum)
    {
        if (!$this->hasUserAuthorization())
        {
            return false;
        }
        $user = $this->tokenStorage->getToken()->getUser();
        if (!is_object($user))
        {
            throw new \Exception('User entity invalid');
            return false;
        }
        $subforumRoles = $subforum->getAllowedRoles();

        if (!$subforum->hasAllowedRoles())
        {
            return true;
        }
        $userRoles = $user->getRoles();
        if (!count($userRoles)) // CASE OF USER HAS NO ROLE, FALLBACK
        {
            $userRoles = ['ROLE_USER'];
        }
        

        foreach ($userRoles as $userRole)
        {
            if (in_array($userRole,$subforumRoles))
            {
                return true;
            }
        }

        $this->setErrorMessage('restricted_access');
        return false;

    }

    public function hasSubforumAccessList($subforumList)
    {
        $subforumAllowed = array();
        foreach ($subforumList as $subforum)
            {
                if ($this->hasSubforumAccess($subforum))
                {
                    $subforumAllowed[] = $subforum->getId();
                }
            }
        return $subforumAllowed;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    private function setErrorMessage($message)
    {
        $this->errorMessage = 'message.error.'.$message;
    }

}