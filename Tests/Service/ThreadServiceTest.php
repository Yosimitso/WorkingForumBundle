<?php

namespace Yosimitso\WorkingForumBundle\Tests\Service;

//use Symfony\Bundle\FrameworkBundle\Test\TestCase;
use PHPUnit\Framework\TestCase;
use Yosimitso\WorkingForumBundle\Entity\Thread;
use Yosimitso\WorkingForumBundle\Service\ThreadService;
use Yosimitso\WorkingForumBundle\Tests\Mock\EntityManagerMock;

/**
 * Class ThreadControllerTest
 *
 * @package Yosimitso\WorkingForumBundle\Tests\Controller
 */
class ThreadServiceTest extends TestCase
{

    public function getTestedClass($em)
    {
        $testedClass = new ThreadService(
            $em
        );

        return $testedClass;
    }


    public function testPin()
    {

        $em = $this->getMockBuilder(EntityManagerMock::class)
            ->setMethods(['getRepository'])
            ->getMock()

        ;

        $entity = new class extends TestCase  {
            public function findOneBySlug($a) {
                return new Thread;
            }
        };

        $em->method('getRepository')->willReturn($entity);

        $testedClass = $this->getTestedClass($em);
        $testedClass->pin('a');
        $this->assertTrue($em->getFlushedEntities()[0]->getPin());
    }

}
