<?php

namespace Yosimitso\WorkingForumBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\User\UserInterface;
use Yosimitso\WorkingForumBundle\Entity\Subforum;

/**
 * Class Authorization
 * Check user's authorization
 * @package Yosimitso\WorkingForumBundle\Security
 */
class Authorization implements AuthorizationInterface
{
    /**
     * @var AuthorizationChecker
     */
    private $securityChecker;
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
     * @param \Symfony\Component\Security\Core\Authorization\AuthorizationChecker $securityChecker
     * @param $tokenStorage
     * @param $allowAnonymousRead
     */
    public function __construct(
        AuthorizationChecker $securityChecker,
        TokenStorageInterface $tokenStorage,
        $allowAnonymousRead
    ) {
        $this->securityChecker = $securityChecker;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->allowAnonymousRead = $allowAnonymousRead;
    }

    /**
     *
     * @return bool
     */
    public function hasModeratorAuthorization() {
        if ($this->securityChecker->isGranted('ROLE_SUPER_ADMIN') || $this->securityChecker->isGranted('ROLE_ADMIN') || $this->securityChecker->isGranted('ROLE_MODERATOR')) {
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
        if ($this->securityChecker->isGranted('ROLE_ADMIN') || $this->securityChecker->isGranted('ROLE_SUPER_ADMIN')) {
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

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

}
