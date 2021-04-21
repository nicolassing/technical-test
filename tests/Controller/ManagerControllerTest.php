<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ManagerControllerTest extends WebTestCase
{
    /**
     * @group functional
     */
    public function testGet(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/managers/1');

        self::assertResponseIsSuccessful();
        self::assertTrue($client->getResponse()->headers->contains(
            'Content-Type', 'application/json'
        ));

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertJsonStringEqualsJsonFile(__DIR__.'/../json/get_manager_response.json', (string) $client->getResponse()->getContent());
    }

    /**
     * @group functional
     */
    public function testNotFound(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/managers/404');

        self::assertTrue($client->getResponse()->headers->contains(
            'Content-Type', 'application/json'
        ));
        self::assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
