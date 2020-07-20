<?php

namespace Yosimitso\WorkingForumBundle\Tests\Security;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Yosimitso\WorkingForumBundle\Entity\Forum;
use Yosimitso\WorkingForumBundle\Entity\Subforum;
use Yosimitso\WorkingForumBundle\Entity\User;
use PHPUnit\Framework\TestCase;
use Yosimitso\WorkingForumBundle\Security\AuthorizationGuard;

class AuthorizationGuardTest extends TestCase
{
    private function getTestedClass(TokenStorage $tokenStorage, $allowAnonymousToRead)
    {

        $authorizationChecker = $this->getMockBuilder(AuthorizationChecker::class)->disableOriginalConstructor()->getMock();

        return new AuthorizationGuard(
            $authorizationChecker,
            $tokenStorage,
            $allowAnonymousToRead
        );
    }

    private function getMockPublicSubforum()
    {
        $forum = (new Forum())
                ->setName('forumName');
        return (new Subforum())
                ->setName('subforumName')
                ->setForum($forum)
            ;
    }

    private function getMockRestrictedSubforum()
    {
        $forum = (new Forum())
            ->setName('forumName');
        return (new Subforum())
            ->setName('subforumName')
            ->setForum($forum)
            ->setAllowedRoles(['ROLE_MODERATOR', 'ROLE_ADMIN', 'ROLE_SUPERADMIN'])
            ;
    }

    private function getMockClassicUser($banned = false)
    {
        $tokenStorage = $this->createMock(TokenStorage::class);

        $user =  $this->getMockBuilder(User::class)
                    ->onlyMethods(['isBanned'])
                    ->addMethods(['getRoles'])
                    ->getMock();

        $user->method('getRoles')->willReturn(['ROLE_USER']);
        $user->method('isBanned')->willReturn($banned);

        $tokenStorage = $this->createMock(TokenStorage::class);

        $class = new class($user)
        {
            private $user;
            public function __construct($user)
            {
                $this->user = $user;
            }

            function getUser()
            {
                return $this->user;
            }
        };

        $tokenStorage->method('getToken')->willReturn($class);

        return $tokenStorage;
    }

    private function getMockModeratorUser()
    {
        $tokenStorage = $this->createMock(TokenStorage::class);

        $user =  $this->getMockBuilder(User::class)
            ->onlyMethods(['isBanned'])
            ->addMethods(['getRoles'])
            ->getMock();

        $user->method('getRoles')->willReturn(['ROLE_MODERATOR']);
        $user->method('isBanned')->willReturn(false);

        $tokenStorage = $this->createMock(TokenStorage::class);

        $class = new class($user)
        {
            private $user;
            public function __construct($user)
            {
                $this->user = $user;
            }

            function getUser()
            {
                return $this->user;
            }
        };

        $tokenStorage->method('getToken')->willReturn($class);

        return $tokenStorage;
    }

    private function getMockAdminUser()
    {
        $tokenStorage = $this->createMock(TokenStorage::class);

        $user =  $this->getMockBuilder(User::class)
            ->onlyMethods(['isBanned'])
            ->addMethods(['getRoles'])
            ->getMock();

        $user->method('getRoles')->willReturn(['ROLE_ADMIN']);
        $user->method('isBanned')->willReturn(false);

        $tokenStorage = $this->createMock(TokenStorage::class);

        $class = new class($user)
        {
            private $user;
            public function __construct($user)
            {
                $this->user = $user;
            }

            function getUser()
            {
                return $this->user;
            }
        };

        $tokenStorage->method('getToken')->willReturn($class);

        return $tokenStorage;
    }


    private function getMockAnonymousUser()
    {
        $tokenStorage = $this->createMock(TokenStorage::class);

        $class = new class()
        {
            function getUser()
            {
                return null;
            }
        };

        $tokenStorage->method('getToken')->willReturn($class);

        return $tokenStorage;
    }

    public function testAnonymousUserAllowed()
    {
        $user = $this->getMockAnonymousUser();
        $testedClass = $this->getTestedClass($user, true);
        $this->assertTrue($testedClass->hasSubforumAccess($this->getMockPublicSubforum())); // ACCORDING TO SETTINGS, ANONYMOUS USERS SHOULD BE ALLOWED TO READ
    }

    public function testAnonymousUserForbidden()
    {
        $user = $this->getMockAnonymousUser();
        $testedClass = $this->getTestedClass($user, false);
        $this->assertFalse($testedClass->hasSubforumAccess($this->getMockPublicSubforum())); // ACCORDING TO SETTINGS, ANONYMOUS USERS SHOULDN'T BE ALLOWED TO READ

        $user = $this->getMockClassicUser();
        $testedClass = $this->getTestedClass($user, false);
        $this->assertTrue($testedClass->hasSubforumAccess($this->getMockPublicSubforum())); // A REGISTERED USER SHOULD BE ALLOWED TO READ
    }

    public function testBannedUser()
    {
        $user = $this->getMockClassicUser(true); // BANNED
        $testedClass = $this->getTestedClass($user, false);
        $this->assertFalse($testedClass->hasSubforumAccess($this->getMockPublicSubforum()));
    }

    public function testRestrictedSubforum()
    {
        $user = $this->getMockAnonymousUser(); // ANONYMOUS
        $testedClass = $this->getTestedClass($user, false);
        $this->assertFalse($testedClass->hasSubforumAccess($this->getMockRestrictedSubforum()));

        $user = $this->getMockClassicUser(false); // CLASSIC USER
        $testedClass = $this->getTestedClass($user, false);
        $this->assertFalse($testedClass->hasSubforumAccess($this->getMockRestrictedSubforum()));

        $user = $this->getMockClassicUser(true); // BANNED USER
        $testedClass = $this->getTestedClass($user, false);
        $this->assertFalse($testedClass->hasSubforumAccess($this->getMockRestrictedSubforum()));

        $admin = $this->getMockAdminUser(); // MODERATOR
        $testedClass = $this->getTestedClass($admin, false);
        $this->assertTrue($testedClass->hasSubforumAccess($this->getMockRestrictedSubforum()));
    }
}