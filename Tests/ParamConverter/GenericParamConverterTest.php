<?php

namespace Yosimitso\WorkingForumBundle\Tests\ParamConverter;



use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Yosimitso\WorkingForumBundle\Entity\File;
use Yosimitso\WorkingForumBundle\Entity\Forum;
use Yosimitso\WorkingForumBundle\Entity\Post;
use Yosimitso\WorkingForumBundle\Entity\Subforum;
use Yosimitso\WorkingForumBundle\Entity\Thread;
use Yosimitso\WorkingForumBundle\ParamConverter\GenericParamConverter;
use Yosimitso\WorkingForumBundle\Security\AuthorizationGuard;

class GenericParamConverterTest extends TestCase
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


        return new GenericParamConverter(
            $em,
            $authorization,
            $classname
        );
    }

    public function testSupportForum()
    {
        $testedClass = $this->getTestedClass(Forum::class);
        $configuration = new ParamConverter([]);
        $configuration->setClass(Forum::class);

        $this->assertTrue($testedClass->supports($configuration));
    }

    public function testSupportSubForum()
    {
        $testedClass = $this->getTestedClass(Subforum::class);
        $configuration = new ParamConverter([]);
        $configuration->setClass(Subforum::class);

        $this->assertTrue($testedClass->supports($configuration));
    }

    public function testSupportThread()
    {
        $testedClass = $this->getTestedClass(Thread::class);
        $configuration = new ParamConverter([]);
        $configuration->setClass(Thread::class);

        $this->assertTrue($testedClass->supports($configuration));
    }

    public function testSupportPost()
    {
        $testedClass = $this->getTestedClass(Post::class);
        $configuration = new ParamConverter([]);
        $configuration->setClass(Post::class);

        $this->assertTrue($testedClass->supports($configuration));
    }

    public function testNotSupportFile()
    {
        $testedClass = $this->getTestedClass(Forum::class);
        $configuration = new ParamConverter([]);
        $configuration->setClass(File::class);

        $this->assertFalse($testedClass->supports($configuration));
    }
}