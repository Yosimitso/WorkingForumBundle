<?php

namespace Yosimitso\WorkingForumBundle\Tests\Controller;


class ThreadControllerTest extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{

    
    public function testGoToSubforumAndCreateThreadIndex() {
        // LOG IMMEDIATELY
       $client = static::createClient([], ['PHP_AUTH_USER' => 'testuser',
    'PHP_AUTH_PW'   => 'pwd']);
      // GO IN THE FIRST SUBFORUM OF THE FIRST FORUM 
       $crawler = $client->request('GET','/');  
      $link = $crawler->filter('.wf_sub_name > a')->links();
      $crawler = $client->click($link[0]);
      $this->assertEquals(200,$client->getResponse()->getStatusCode());
      
      // GO THE "NEW THREAD" PAGE
      $link = $crawler->filter('a.wf_add')->links();
      
       $crawler = $client->click($link[0]);
      // die(var_dump($crawler));
     $this->assertEquals(200,$client->getResponse()->getStatusCode());
     
     // CREATE A FORM
     $form = $crawler->selectButton('Create the thread')->form();

    $form['thread[label]']       = 'A Test';
    $form['thread[sublabel]']      = 'A wonderful sublabel';
    $form['thread[post][0][content]']    = 'This is a test:smile:
**bold**
_italic_
[link google](http://google.com)
![random image](http://charlymartins.fr/images/jim-carrey.jpg "jimmy")

`<script>alert(\'hello world\')</script>`
> A quote from me';
           
    
    $client->submit($form);
    $crawler = $client->followRedirect();
     $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
}
