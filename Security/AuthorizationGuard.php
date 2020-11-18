<?php

namespace Yosimitso\WorkingForumBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Yosimitso\WorkingForumBundle\Entity\Forum;
use Yosimitso\WorkingForumBundle\Entity\Subforum;

class AuthorizationGuard implements AuthorizationGuardInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;
    /**
     * @var string
     */
    private $errorMessage;
    /**
     * @var UserInterface|null
     */
    private $user;
    /**
     * @var boolean
     */
    private $allowAnonymousRead;

    /**
     * Authorization constructor.
     * @param AuthorizationCheckerInterface $securityChecker
     * @param $tokenStorage
     * @param $allowAnonymousRead
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage,
        $allowAnonymousRead
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $token = $tokenStorage->getToken();
        $this->user = (is_object($token)) ? $token->getUser() : null;
        $this->allowAnonymousRead = $allowAnonymousRead;
    }

    /**
     *
     * @return bool
     */
    public function hasModeratorAuthorization() {
        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') || $this->authorizationChecker->isGranted('ROLE_ADMIN') || $this->authorizationChecker->isGranted('ROLE_MODERATOR')) {
            return true;
        }
        else {
            $this->setErrorMessage('restricted_action');
            return false;
        }
    }

    /**
     * @param $message
     */
    private function setErrorMessage($message)
    {
        $this->errorMessage = 'message.error.'.$message;
    }

    /**
     * @return bool
     */
    public function hasAdminAuthorization()
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
     * @return array
     */
    public function hasSubforumAccessList(array $subforumList)
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
     * @param Subforum $subforum
     * @return bool
     * @throws \Exception
     */
    public function hasSubforumAccess(Subforum $subforum)
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
            if (in_array($userRole,$subforumRoles))
            {
                return true; // THE USER HAS A ROLE ALLOWED
            }
        }

        $this->setErrorMessage('restricted_access');
        return false;

    }

    /**
     * has user authorization ?
     * @return bool
     */
    public function hasUserAuthorization()
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

    public function filterForumAccess(array $forums)
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
    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

}
