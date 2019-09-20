<?php

namespace Yosimitso\WorkingForumBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Yosimitso\WorkingForumBundle\Util\ThreadUtil;
use Yosimitso\WorkingForumBundle\Entity\Thread as ThreadEntity;
use Knp\Component\Pager\Paginator;

/**
 * Class ThreadUtilTest
 *
 * @package Yosimitso\WorkingForumBundle\Tests\Util
 */
class ThreadUtilTest extends WebTestCase
{
    private $threadUtil;
    private const LOCK_OLDER_THAN = 3;

    public function setUp()
    {
        $paginator = $this->getMockBuilder(Paginator::class)
                    ->disableOriginalConstructor()
                    ->setMethods(['paginate'])
                    ->getMock();
        $paginator->method('paginate')->willReturn(true);
        $this->threadUtil = new ThreadUtil(self::LOCK_OLDER_THAN, $paginator, null, null);
    }
    
    public function testIsAutolock()
    {
     $thread = $this->getMockBuilder(ThreadEntity::class)
                    ->disableOriginalConstructor()
                    ->setMethods(['getLastReplyDate'])
                    ->getMock();
     $thread->method('getLastReplyDate')->willReturnOnConsecutiveCalls(new \DateTime('-1day'), new \DateTime('-7day'));
     $this->assertEquals(false, $this->threadUtil->isAutolock($thread)); // MUST NOT BE LOCKED
     $this->assertEquals(true, $this->threadUtil->isAutolock($thread));// TOO OLD MUST BE AUTOLOCK
    }
}