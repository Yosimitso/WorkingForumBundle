<?php

namespace Yosimitso\WorkingForumBundle\Tests\Service;

//use Symfony\Bundle\FrameworkBundle\Test\TestCase;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Yosimitso\WorkingForumBundle\Entity\Post;
use Yosimitso\WorkingForumBundle\Entity\PostReport;
use Yosimitso\WorkingForumBundle\Entity\Subforum;
use Yosimitso\WorkingForumBundle\Entity\Thread;
use Yosimitso\WorkingForumBundle\Entity\User;
use Yosimitso\WorkingForumBundle\Service\ThreadService;
use Yosimitso\WorkingForumBundle\Tests\Mock\EntityManagerMock;
use Knp\Component\Pager\Paginator;

/**
 * Class ThreadControllerTest
 *
 * @package Yosimitso\WorkingForumBundle\Tests\Controller
 */
class ThreadServiceTest extends TestCase
{

    public function getTestedClass($em, $user = null)
    {
        if (is_null($user)) {
            $user = $this->createMock(User::class);
        }
        $testedClass = new ThreadService(
            0,
            $this->createMock(Paginator::class),
            10,
            $this->createMock(RequestStack::class),
            $em,
            $user
        );

        return $testedClass;
    }


    public function testPin()
    {
        $em = $this->getMockBuilder(EntityManagerMock::class)
            ->setMethods(['getRepository'])
            ->getMock()
        ;

//        $entity = new class extends TestCase  {
//            public function findOneBySlug($a) {
//                return new Thread;
//            }
//        };
//
//        $em->method('getRepository')->willReturn($entity);

        $testedClass = $this->getTestedClass($em);

        $thread = new Thread;
        $this->assertTrue($testedClass->pin($thread));
        $this->assertTrue($em->getFlushedEntities()[0]->getPin());
    }

    public function testResolved()
    {
        $em = $this->getMockBuilder(EntityManagerMock::class)
            ->setMethods(['getRepository'])
            ->getMock()
        ;
        $testedClass = $this->getTestedClass($em);

        $thread = new Thread;

        $this->assertTrue($testedClass->resolve($thread));
        $this->assertTrue($em->getFlushedEntities()[0]->getResolved());
    }

    public function testLocked()
    {
        $em = $this->getMockBuilder(EntityManagerMock::class)
            ->setMethods(['getRepository'])
            ->getMock()
        ;
        $testedClass = $this->getTestedClass($em);

        $thread = new Thread;

        $this->assertTrue($testedClass->lock($thread));
        $this->assertTrue($em->getFlushedEntities()[0]->getLocked());
    }

    public function testReport()
    {
        $em = $this->getMockBuilder(EntityManagerMock::class)
            ->setMethods(['getRepository'])
            ->getMock()
        ;

        $testedClass = $this->getTestedClass($em);

        $post = new Post;
        $this->assertTrue($testedClass->report($post));
        $this->assertTrue($em->getFlushedEntities()[0] instanceof PostReport);
    }


    public function testMoveThread()
    {
        $em = $this->getMockBuilder(EntityManagerMock::class)
            ->setMethods(['getRepository'])
            ->getMock()
        ;

        $testedClass = $this->getTestedClass($em);

        $thread = new Thread;
        $thread->setNbReplies(5);

        $currentSubforum = new Subforum;
        $currentSubforum->setName('former');
        $currentSubforum->setNbThread(20);
        $currentSubforum->setNbPost(50);


        $targetSubforum = new Subforum;
        $targetSubforum->setName('new');
        $targetSubforum->setNbThread(20);
        $targetSubforum->setNbPost(50);

        $this->assertTrue($testedClass->moveThread($thread, $currentSubforum, $targetSubforum));
        $this->assertTrue($em->getFlushedEntities()[0] instanceof Thread);

        $this->assertEquals('new', $em->getFlushedEntities()[0]->getSubforum()->getName()); // THREAD MOVE TO THE RIGHT SUBFORUM
        $this->assertEquals(19, $em->getFlushedEntities()[1]->getNbThread()); // STATISTICS ARE UPDATED
        $this->assertEquals(21, $em->getFlushedEntities()[2]->getNbThread()); // STATISTICS ARE UPDATED

        $this->assertEquals(45, $em->getFlushedEntities()[1]->getNbPost()); // STATISTICS ARE UPDATED
        $this->assertEquals(55, $em->getFlushedEntities()[2]->getNbPost()); // STATISTICS ARE UPDATED

    }

}
