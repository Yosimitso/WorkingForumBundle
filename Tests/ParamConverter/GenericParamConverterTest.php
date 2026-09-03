<?php

namespace Yosimitso\WorkingForumBundle\Tests\ArgumentResolver;



use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Yosimitso\WorkingForumBundle\Entity\File;
use Yosimitso\WorkingForumBundle\Entity\Forum;
use Yosimitso\WorkingForumBundle\Entity\Post;
use Yosimitso\WorkingForumBundle\Entity\Subforum;
use Yosimitso\WorkingForumBundle\Entity\Thread;
use Yosimitso\WorkingForumBundle\ArgumentResolver\GenericArgumentResolver;
use Yosimitso\WorkingForumBundle\Security\AuthorizationGuard;

class GenericArgumentResolverTest extends TestCase
{
    public function getTestedClass($classname, $em = null, $authorization = null)
    {
        if (is_null($em)) {
            $em = $this->createMock(EntityManager::class);
        }

        if (is_null($authorization)) {
            $authorization = $this->createMock(AuthorizationGuard::class);
            $authorization->method('hasSubforumAccess')->willReturn(true);
        }


        return new GenericArgumentResolver(
            $em,
            $authorization,
            $classname
        );
    }

    public function testSupportForum()
    {
        $testedClass = $this->getTestedClass(Forum::class);
        $configuration = new ArgumentResolver([]);
        $configuration->setClass(Forum::class);

        $this->assertTrue($testedClass->supports($configuration));
    }

    public function testSupportSubForum()
    {
        $testedClass = $this->getTestedClass(Subforum::class);
        $configuration = new ArgumentResolver([]);
        $configuration->setClass(Subforum::class);

        $this->assertTrue($testedClass->supports($configuration));
    }

    public function testSupportThread()
    {
        $testedClass = $this->getTestedClass(Thread::class);
        $configuration = new ArgumentResolver([]);
        $configuration->setClass(Thread::class);

        $this->assertTrue($testedClass->supports($configuration));
    }

    public function testSupportPost()
    {
        $testedClass = $this->getTestedClass(Post::class);
        $configuration = new ArgumentResolver([]);
        $configuration->setClass(Post::class);

        $this->assertTrue($testedClass->supports($configuration));
    }

    public function testNotSupportFile()
    {
        $testedClass = $this->getTestedClass(Forum::class);
        $configuration = new ArgumentResolver([]);
        $configuration->setClass(File::class);

        $this->assertFalse($testedClass->supports($configuration));
    }
}
