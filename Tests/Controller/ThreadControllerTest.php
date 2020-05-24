<?php

namespace Yosimitso\WorkingForumBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Panther\PantherTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

/**
 * Class ThreadControllerTest
 *
 * @package Yosimitso\WorkingForumBundle\Tests\Controller
 */
class ThreadControllerTest extends PantherTestCase
{
    use ReloadDatabaseTrait;
    private $client;

    public function setUp() : void
    {
        self::bootKernel();
//        $this->client = static::createClient(
//            [],
//            [
//                'PHP_AUTH_USER' => $_ENV['TEST_ADMIN_USERNAME'],
//                'PHP_AUTH_PW' => $_ENV['TEST_ADMIN_PASSWORD'],
//            ]
//        );
    }
    public function testGoToSubforumAndCreateThreadIndex()
    {
        $client = static::createPantherClient();// The database will be reset after every boot of the Symfony kernel

        $crawler = $client->request('GET', '/');
        //$this->assertEquals(200, $client->getRequest());
        exit(var_dump($crawler->getText()));
        // GO IN THE FIRST SUBFORUM OF THE FIRST FORUM
//        $link = $crawler->filter('.wf_sub_name > a')->first()->link();
//        $crawler = $this->client->click($link);
        $client->clickLink('My First Forum');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // GO THE "NEW THREAD" PAGE
        $link = $crawler->filter('a.wf_add')->links();

        $crawler = $client->click($link[0]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // CREATE A FORM
        $form = $crawler->filter('input[type=submit]')->form([
            'thread[label]' => 'A Test',
            'thread[sublabel]' => 'A wonderful sublabel',
            'thread[post][0][content]' => 'This is a test:smile:**bold**_italic_[link google](http://google.com)![random image](http://charlymartins.fr/images/jim-carrey.jpg "jimmy")<script>alert(\"hello world\")</script>> A quote from me'
        ]);

        $this->client->submit($form);

        $post = $crawler->filter('.wf_post')[1];
        $this->assertEquals(
            '
         <p>This is a test<img src="/bundles/yosimitsoworkingforum/images/smiley/smile.png"><br><strong>bold</strong><br><em>italic</em><br><a href="http://google.com">link google</a><br>
         <img src="http://charlymartins.fr/images/jim-carrey.jpg" alt="random image" title="jimmy"></p><br><br><p><code>alert(\'hello world\')</code></p><br><br><blockquote><br>
         <p>A quote from me</p><br></blockquote><br>',
            $post
        );
        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}
