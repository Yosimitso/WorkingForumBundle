<?php

namespace Yosimitso\WorkingForumBundle\Tests\Scenario;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Panther\PantherTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Yosimitso\WorkingForumBundle\Entity\UserTest;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;

class ThreadTest extends WebTestCase
{
    use ReloadDatabaseTrait;
    private $client;

    public function setUp() : void
    {
        self::bootKernel();
    }

    private function getModeratorUserClient()
    {
        $client = static::createClient();
        $container = static::$kernel->getContainer();
        $session = $container->get('session');
        $person = self::$kernel->getContainer()->get('doctrine')->getRepository(UserTest::class)->findAll()[1];

        $token = new UsernamePasswordToken($person, null, 'main', $person->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $client;
    }

    private function getAdminUserClient()
    {
        $client = static::createClient();
        $container = static::$kernel->getContainer();
        $session = $container->get('session');
        $person = self::$kernel->getContainer()->get('doctrine')->getRepository(UserTest::class)->findAll()[2];

        $token = new UsernamePasswordToken($person, null, 'main', $person->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $client;
    }

    private function getClassicUserClient()
    {
        $client = static::createClient();
        $container = static::$kernel->getContainer();
        $session = $container->get('session');
        $person = self::$kernel->getContainer()->get('doctrine')->getRepository(UserTest::class)->findAll()[0];

        $token = new UsernamePasswordToken($person, null, 'main', $person->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $client;
    }

    private function getAnonymousUserClient()
    {
        $client = static::createClient();

        return $client;
    }

    public function testClassicUserGoToSubforumAndShouldBeAbleToCreateAThread()
    {
        $client = $this->getClassicUserClient();
        $crawler = $client->request('GET', '/');

        $this->assertEquals(1, $crawler->filter('.wf_sub_name > a')->count());
        $link = $crawler->filter('.wf_sub_name > a')->first()->link();
        $crawler = $client->click($link);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // GO THE "NEW THREAD" PAGE
        $this->assertEquals(1, $crawler->filter('a.wf_add')->count());
        $link = $crawler->filter('a.wf_add')->links();

        $crawler = $client->click($link[0]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->followRedirects();

        // CREATE A FORM
        $crawler = $client->submitForm('Create the thread', [
            'thread[label]' => 'A Test For A Test',
            'thread[sublabel]' => 'A wonderful sublabel',
            'thread[post][0][content]' => "This is a test:smile:**bold**\n"
            ."_italic_\n"
            ."[link google](http://google.com)![random image](http://charlymartins.fr/images/jim-carrey.jpg \"jimmy\")\n"
            ."<script>alert('hello world')</script>\n"
            ."> A quote from me"
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
//        exit(print_r($crawler->html()));
        
        $post = $crawler->filter('.wf_post_content')->first()->html();

        $this->assertEquals(trim(
            "\n".'<p>This is a test<img src="http://localhost/bundles/yosimitsoworkingforum/images/smiley/smile.png"><strong>bold</strong><br><em>italic</em><br><a href="http://google.com">link google</a><img src="http://charlymartins.fr/images/jim-carrey.jpg" alt="random image" title="jimmy"><br>alert(\'hello world\')</p><br><br><blockquote><br>  <p>A quote from me</p><br></blockquote><br>'."\n"),
            trim($post)
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        
    }

    public function testAnonymousGoToSubforumAndShouldNotBeAbleToCreateAThread()
    {
        $client = $this->getAnonymousUserClient();
        $crawler = $client->request('GET', '/');

        $this->assertEquals(1, $crawler->filter('.wf_sub_name > a')->count());
        $link = $crawler->filter('.wf_sub_name > a')->first()->link();
        $crawler = $client->click($link);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // NEW THREAD BUTTON SHOULD NOT APPEAR
        $this->assertEquals(0, $crawler->filter('a.wf_add')->count());

        // NEW THREAD PAGE SHOULDN'T BE ACCESSIBLE
        $client->request('GET', '/my-forum/my-first-forum/new');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testSetAsResolved()
    {
        $client = $this->getClassicUserClient();
        $client->followRedirects();
        
        $crawler = $client->request('GET', '/my-forum/my-first-forum/a-thread-from-fixtures/view');
        $link = $crawler->filter('#wf_set-resolved')->first()->link();
        $crawler = $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals('The thread is now solved, thanks for your feedback', $crawler->filter('.alert-success ul > li')->html());
    }

    public function testPost()
    {
        $client = $this->getClassicUserClient();

        $crawler = $client->request('GET', '/my-forum/my-first-forum/a-thread-from-fixtures/view');

        $client->followRedirects();
        $crawler = $client->submitForm('Post !', [
            'post[content]' => 'test post a message'
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('Post saved', $crawler->filter('.alert-success ul > li')->html());

    }

    public function testModeratorShouldBeAbleToLockThread()
    {
        $client = $this->getModeratorUserClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/my-forum/my-first-forum/a-thread-from-fixtures/view');

        $link = $crawler->filter('#wf_set-locked')->first()->link();
        $crawler = $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals('The thread has been successfully locked', $crawler->filter('.alert-success ul > li')->html());

    }

    public function testAdminShouldBeAbleToLockThread()
    {
        $client = $this->getAdminUserClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/my-forum/my-first-forum/a-thread-from-fixtures/view');

        $link = $crawler->filter('#wf_set-locked')->first()->link();
        $crawler = $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals('The thread has been successfully locked', $crawler->filter('.alert-success ul > li')->html());
    }

    public function testClassicUserShouldNotBeAbleToLockThread()
    {
        $client = $this->getClassicUserClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/my-forum/my-first-forum/a-thread-from-fixtures/view'); // SHOULDN'T SEE THE BUTTON
        $this->assertEquals(0, $crawler->filter('#wf_set-locked')->count());
        
        $crawler = $client->request('GET', '/my-forum/my-first-forum/a-thread-from-fixtures/lock');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testAnonymousShouldNotBeAbleToLockThread()
    {
        $client = $this->getAnonymousUserClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/my-forum/my-first-forum/a-thread-from-fixtures/view'); // SHOULDN'T SEE THE BUTTON
        $this->assertEquals(0, $crawler->filter('#wf_set-locked')->count());

        $crawler = $client->request('GET', '/my-forum/my-first-forum/a-thread-from-fixtures/lock');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }
}
