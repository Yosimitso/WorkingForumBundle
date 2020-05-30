<?php

namespace Yosimitso\WorkingForumBundle\Tests\Scenario;

use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Config\Definition\Exception\Exception as Exception;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
use Yosimitso\WorkingForumBundle\Entity\UserTest;

/**
 *
 * Class HttpControllerTest
 *
 * @package Yosimitso\WorkingForumBundle\Tests\Controller
 */
class HttpResponseTest extends WebTestCase
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
        $person = self::$kernel->getContainer()->get('doctrine')->getRepository(UserTest::class)->findAll()[2];

        $token = new UsernamePasswordToken($person, null, 'main', $person->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        $this->client = $client;
    }
    /**
     * @test
     */
    function test200Index()
    {
        $urls = [
            '',
            'search',
            'my-forum/my-first-forum/view',
            'my-forum/my-first-forum/new',
            'admin',
            'admin/forum/edit/1',
            'admin/forum/add',
            'admin/users',
            'admin/report',
            'admin/report/history'

        ];

        foreach ($urls as $url) {
                $crawler = $this->client->request('GET', '/'.$url);
                if ($this->client->getResponse()->getStatusCode() === 500) {
//                    print_r($crawler->html());
                }
                $this->assertEquals(200, $this->client->getResponse()->getStatusCode(),$url.' returns '.$this->client->getResponse()->getStatusCode());
        }
    }

    private function logIn()
    {
        $session = $this->client->getContainer()->get('session');

        // the firewall context defaults to the firewall name
        $firewallContext = 'main';

        $token = new UsernamePasswordToken('admin', null, $firewallContext, array('ROLE_ADMIN'));
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);

    }


}
?>
