<?php

namespace Yosimitso\WorkingForumBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Yosimitso\WorkingForumBundle\Entity\Thread;

class ThreadControllerTest extends WebTestCase
{
    public function createThreadIndex()
    {
      $thread = new Thread;
      $this->assertEqual('lol',$thread->setSlug('lol'));
      
    }
}
