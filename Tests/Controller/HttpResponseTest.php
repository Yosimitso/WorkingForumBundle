<?php

namespace Yosimitso\WorkingForumBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Config\Definition\Exception\Exception as Exception;

/**
 * Class ThreadControllerTest
 *
 * @package Yosimitso\WorkingForumBundle\Tests\Controller
 */
class HttpControllerTest extends WebTestCase
{
    function test200Index()
    {
        $client = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW' => 'jeanpaul2'
            ]
        );

        $urls = [
            '',
            'search',
            'view/subforum-test',
            'newthread/subforum-test',
            'admin',
            'admin/edit/1',
            'admin/add',
            'admin/user',
            'admin/report',
            'admin/report/history'

        ];

        foreach ($urls as $url) {
                $client->request('GET', '/'.$url);
                $this->assertEquals(200, $client->getResponse()->getStatusCode(),$url.' returns '.$client->getResponse()->getStatusCode());
        }
    }


}
?>