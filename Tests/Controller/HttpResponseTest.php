<?php

namespace Yosimitso\WorkingForumBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Config\Definition\Exception\Exception as Exception;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;

/**
 *
 * Class ThreadControllerTest
 *
 * @package Yosimitso\WorkingForumBundle\Tests\Controller
 */
class HttpControllerTest extends WebTestCase
{
    private $client = null;

    public function setUp()
    {
        $this->client = static::createClient([], [
            'PHP_AUTH_USER' => $_ENV['TEST_ADMIN_USERNAME'],
            'PHP_AUTH_PW' => $_ENV['TEST_ADMIN_PASSWORD'],
        ]);
    }
    /**
     * @test
     */
    function test200Index()
    {
        $urls = [
            '',
            'search',
            'view/subforum-test',
            'newthread/subforum-test',
            'admin',
            'admin/forum/edit/1',
            'admin/forum/add',
            'admin/user',
            'admin/report',
            'admin/report/history'

        ];

        foreach ($urls as $url) {
                $this->client->request('GET', '/'.$url);
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