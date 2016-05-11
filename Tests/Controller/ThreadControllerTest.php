<?php

namespace Yosimitso\WorkingForumBundle\Tests\Controller;


use Yosimitso\WorkingForumBundle\Entity\Thread;

class ThreadControllerTest extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{
    public function testURLIndex()
    {
        $client = static::createClient();
        
        $urlList = ['admin','newthread/subforum-test'];
        
        foreach ($urlList as $url)
        {
        $client->request('GET', 'demoworkingforum/web/app.php/'.$url);
         $this->assertTrue(200 === $client->getResponse()->getStatusCode());
        }
      
    }
}
