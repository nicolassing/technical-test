<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ShopControllerTest extends WebTestCase
{
    /**
     * @group functional
     */
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/shops');

        self::assertResponseIsSuccessful();
        self::assertTrue($client->getResponse()->headers->contains(
            'Content-Type', 'application/json'
        ));

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertJsonStringEqualsJsonFile(__DIR__.'/../json/list_shops_response.json', (string) $client->getResponse()->getContent());
    }

    /**
     * @group functional
     */
    public function testSearchWithTypo(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/shops?q=pinces');

        self::assertResponseIsSuccessful();
        self::assertTrue($client->getResponse()->headers->contains(
            'Content-Type', 'application/json'
        ));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertJsonStringEqualsJsonFile(__DIR__.'/../json/fulltext_search_response.json', (string) $client->getResponse()->getContent());
    }

    /**
     * @group functional
     */
    public function testSearchWithLocation(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/shops?lat=48.884748&lon=2.23964&distance=7000');

        self::assertResponseIsSuccessful();
        self::assertTrue($client->getResponse()->headers->contains(
            'Content-Type', 'application/json'
        ));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertJsonStringEqualsJsonFile(__DIR__.'/../json/geolocalised_search_response.json', (string) $client->getResponse()->getContent());
    }

    /**
     * @group functional
     */
    public function testCreate(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/shops',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"name": "Sezane", "latitude": 48.8630357, "longitude": 2.3706465, "address1": "42 rue du test", "postal_code": "75001", "city": "Paris", "manager": 1}',
        );

        self::assertResponseIsSuccessful();
        self::assertTrue($client->getResponse()->headers->contains(
            'Content-Type', 'application/json'
        ));
        self::assertEquals(201, $client->getResponse()->getStatusCode());
        self::assertJsonStringEqualsJsonFile(__DIR__.'/../json/create_shop_response.json', (string) $client->getResponse()->getContent());
    }
}
