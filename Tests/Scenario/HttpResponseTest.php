<?php

namespace Yosimitso\WorkingForumBundle\Tests\Scenario;

use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Config\Definition\Exception\Exception as Exception;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
use Yosimitso\WorkingForumBundle\Tests\Entity\UserTest;


class HttpResponseTest extends WebTestCase
{
    use ReloadDatabaseTrait;
    private $client;

    public function setUp() : void
    {
        $client = static::createClient();
        $container = static::$kernel->getContainer();
        $person = self::$kernel->getContainer()->get('doctrine')->getRepository(UserTest::class)->findAll()[2];
        $client->loginUser($person);
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
                    $file = fopen(__DIR__.'/error.html', 'w');
                    fwrite($file, $crawler->html());

                }
                $this->assertEquals(200, $this->client->getResponse()->getStatusCode(),$url.' returns '.$this->client->getResponse()->getStatusCode());
        }
    }
}
?>
