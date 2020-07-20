<?php

namespace Yosimitso\WorkingForumBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ThreadControllerTest
 *
 * @package Yosimitso\WorkingForumBundle\Tests\Controller
 */
class AdminController extends WebTestCase
{
    private $client;
    
    public function setUp() : void
    {
        $this->client = static::createClient([], [
            'PHP_AUTH_USER' => $_ENV['TEST_ADMIN_USERNAME'],
            'PHP_AUTH_PW' => $_ENV['TEST_ADMIN_PASSWORD'],
        ]);
    }
    /**
     * @test
     */
    public function testAdminForumIndex()
    {
        // GO TO ADMIN PANEL
        $crawler = $this->client->request('GET', '/admin');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $link = $crawler->filter('.wf_forum_block_admin ul > li a')->first()->link();

        $this->client->click($link);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminaddForumIndex()
    {
        $this->client->request('GET', '/admin/add');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}
