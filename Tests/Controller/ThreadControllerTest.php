<?php

namespace Yosimitso\WorkingForumBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ThreadControllerTest
 *
 * @package Yosimitso\WorkingForumBundle\Tests\Controller
 */
class ThreadControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function testGoToSubforumAndCreateThreadIndex()
    {
        // LOG IMMEDIATELY
        $client = static::createClient([],
            [
            ]
        );

        // GO IN THE FIRST SUBFORUM OF THE FIRST FORUM
        $crawler = $client->request('GET', '/');
        $this->assertEquals(1,2, $crawler->html());
        $link = $crawler->filter('.wf_sub_name > a')->first()->link();
        $crawler = $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // GO THE "NEW THREAD" PAGE
        $link = $crawler->filter('a.wf_add')->links();

        $crawler = $client->click($link[0]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // CREATE A FORM
        $form = $crawler->filter('input[type=submit]')->form();

        $form['thread[label]'] = 'A Test';
        $form['thread[sublabel]'] = 'A wonderful sublabel';
        $form['thread[post][0][content]'] = 'This is a test:smile:
**bold**
_italic_
[link google](http://google.com)
![random image](http://charlymartins.fr/images/jim-carrey.jpg "jimmy")

`<script>alert(\'hello world\')</script>`
> A quote from me';

        $client->submit($form);

        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
