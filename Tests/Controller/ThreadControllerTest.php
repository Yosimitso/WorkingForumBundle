<?php

namespace Yosimitso\WorkingForumBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Panther\PantherTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Yosimitso\WorkingForumBundle\Entity\UserTest;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
/**
 * Class ThreadControllerTest
 *
 * @package Yosimitso\WorkingForumBundle\Tests\Controller
 */
class ThreadControllerTest extends WebTestCase
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
//
        $client = static::createClient();
        $container = static::$kernel->getContainer();
        $session = $container->get('session');
        $person = self::$kernel->getContainer()->get('doctrine')->getRepository(UserTest::class)->findAll()[0];

        $token = new UsernamePasswordToken($person, null, 'main', $person->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        $this->client = $client;
    }
    public function testGoToSubforumAndCreateThreadIndex()
    {
       // The database will be reset after every boot of the Symfony kernel
        $router = self::$kernel->getContainer()->get('router');

        $router->setOption('debug', true);
        /** @var $collection \Symfony\Component\Routing\RouteCollection */
        $collection = $router->getRouteCollection();
        $allRoutes = $collection->all();
//        $client = static::createClient();
        $crawler = $this->client->request('GET', '/');
        //$this->assertEquals(200, $client->getRequest());
//        exit(print_r($crawler->html()));
        // GO IN THE FIRST SUBFORUM OF THE FIRST FORUM

        $this->assertEquals(2, $crawler->filter('.wf_sub_name > a')->count());
        $link = $crawler->filter('.wf_sub_name > a')->first()->link();
        $crawler = $this->client->click($link);
//        $client->clickLink('My First Forum');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // GO THE "NEW THREAD" PAGE
        $link = $crawler->filter('a.wf_add')->links();

        $crawler = $this->client->click($link[0]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->client->followRedirects();
        // CREATE A FORM
        $form = $this->client->submitForm('Create the thread', [
            'thread[label]' => 'A Test For A Test',
            'thread[sublabel]' => 'A wonderful sublabel',
            'thread[post][0][content]' => 'This is a test:smile:**bold**_italic_[link google](http://google.com)![random image](http://charlymartins.fr/images/jim-carrey.jpg "jimmy")<script>alert(\"hello world\")</script>> A quote from me'
        ]);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
         exit(print_r($crawler->html()));
        $post = $crawler->filter('.wf_post')->first()->html();

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
