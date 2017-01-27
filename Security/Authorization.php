<?php

namespace Yosimitso\WorkingForumBundle\Security;


class Authorization
{
    private $securityChecker;

    public function __construct(Symfony\Component\Security\Core\Authorization\AuthorizationChecker $securityChecker) {
        $this->securityChecker = $securityChecker;
    }
    public function hasModeratorAuthorization() {
        if ($this->securityChecker->isGranted('ROLE_ADMIN') || $this->get('security.authorization_checker')->isGranted('ROLE_MODERATOR')) {
            return true;
        }
        else {
            return false;
        }
    }

    public function hasAdminAuthorization()
    {
        if ($this->securityChecker->isGranted('ROLE_ADMIN')) {
            return true;
        }
        else {
            return false;
        }
    }

    public function hasUserAuthorization()
    {
        $user = $this->getUser();
        $allowAnonymous = $this->container->getParameter('yosimitso_working_forum.allow_anonymous_read');

        if ($user !== null && $user->isBanned()) {
            $this->get('session')->getFlashBag()->add(
                'error',
                $this->get('translator')->trans('message.banned', [], 'YosimitsoWorkingForumBundle')
            )
            ;
            return false;
        }
        if ($user !== null || $allowAnonymous) {
            return true;
        }
    }

}