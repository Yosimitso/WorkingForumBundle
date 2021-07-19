<?php

namespace Yosimitso\WorkingForumBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Yosimitso\WorkingForumBundle\Entity\Forum;
use Yosimitso\WorkingForumBundle\Entity\Subforum;

class AuthorizationGuard implements AuthorizationGuardInterface
{

    private AuthorizationCheckerInterface $authorizationChecker;

    private ?string $errorMessage;
    private ?UserInterface $user;
    private bool $allowAnonymousRead;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage,
        bool $allowAnonymousRead
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $token = $tokenStorage->getToken();

        $this->user = (is_object($token) && is_object($token->getUser())) ? $token->getUser() : null;
        $this->allowAnonymousRead = $allowAnonymousRead;
    }

    public function hasModeratorAuthorization() : bool {
        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') || $this->authorizationChecker->isGranted('ROLE_ADMIN') || $this->authorizationChecker->isGranted('ROLE_MODERATOR')) {
            return true;
        }
        else {
            $this->setErrorMessage('restricted_action');
            return false;
        }
    }

    public function hasAdminAuthorization() : bool
    {
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN') || $this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }
        else {
            $this->setErrorMessage('restricted_action');
            return false;
        }
    }

    /**
     * @param array<Subforum> $subforumList
     */
    public function hasSubforumAccessList(array $subforumList) : array
    {
        $subforumAllowed = array();
        foreach ($subforumList as $subforum)
        {
            if (!$subforum instanceof Subforum) {
                throw new \Exception('is not a subforum');
            }
            if ($this->hasSubforumAccess($subforum))
            {
                $subforumAllowed[] = $subforum->getId();
            }
        }
        return $subforumAllowed;
    }

    /**
     * Check if user has permissions to view/write into a subforum
     * @throws \Exception
     */
    public function hasSubforumAccess(Subforum $subforum) : bool
    {
        if (!$this->hasUserAuthorization() || is_null($subforum))
        {
            return false;
        }

        $subforumRoles = $subforum->getAllowedRoles();

        if (!$subforum->hasAllowedRoles())
        {
            return true;
        }

        $userRoles = (is_object($this->user)) ? $this->user->getRoles() : [];
        if (!count($userRoles) || trim($userRoles[0]) === '') // CASE OF USER HAS NO ROLE, FALLBACK
        {
            $userRoles = ['ROLE_USER'];
        }


        foreach ($userRoles as $userRole)
        {
            if (in_array($userRole, $subforumRoles ? $subforumRoles : []))
            {
                return true; // THE USER HAS A ROLE ALLOWED
            }
        }

        $this->setErrorMessage('restricted_access');
        return false;

    }

    /**
     * has user authorization ?
     */
    public function hasUserAuthorization() : bool
    {
        if (is_object($this->user) && $this->user->isBanned()) {
            $this->setErrorMessage('banned');
            return false;
        }

        if (is_object($this->user) || $this->allowAnonymousRead) {
            return true;
        }

        $this->setErrorMessage('must_be_logged');
        return false;


    }

    public function filterForumAccess(array $forums) : void
    {
        foreach ($forums as $forum)
        {
            if (!$forum instanceof Forum) {
                throw new \Exception('is not a forum');
            }

            foreach ($forum->getSubforum() as $index => $subforum) {
                if (!$this->hasSubforumAccess($subforum)) {
                    $forum->removeSubForum($index);
                }
            }

        }
    }

    public function getErrorMessage() : ?string
    {
        return $this->errorMessage;
    }

    private function setErrorMessage(string $message) : void
    {
        $this->errorMessage = 'message.error.'.$message;
    }

}
