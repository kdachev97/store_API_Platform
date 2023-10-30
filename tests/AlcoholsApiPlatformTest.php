<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Alcohol;
use App\Entity\Image;
use App\Entity\Producer;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Component\HttpFoundation\Response;

class AlcoholsApiPlatformTest extends ApiTestCase
{
    use ReloadDatabaseTrait;

    private function login(): string
    {
        $client = self::createClient();
        $response = $client->request(
            'POST',
            '/login_check',
            [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => [
                    'email' => 'krum@codixis.com',
                    'password' => 'aBcd@5678yilnjvgtiuh',
                ],
            ]
        );

        return $response->toArray()['token'];
    }

    public function testGetAlcoholsUnauthorizedSuccess()
    {
        $client = static::createClient();
        $client->request('GET', '/api/alcohols?page=1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseContent = $client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);
        $totalAlcohols = $responseData['hydra:totalItems'];
        $this->assertEquals(50, $totalAlcohols);
    }

    public function testGetAlcoholsAuthorizedSuccess()
    {
        $token = $this->login();
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/alcohols?page=1',
            [
                'auth_bearer' => $token
            ]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseContent = $client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);
        $totalAlcohols = $responseData['hydra:totalItems'];
        $this->assertEquals(50, $totalAlcohols);
    }


    public function testGetAlcoholsFilters()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/alcohols?page=1&type=whiskey&name=Jameson',
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $responseContent = $client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);
        $totalAlcohols = $responseData['hydra:totalItems'];
        $this->assertEquals(1, $totalAlcohols);
    }

    public function testGetOneAlcoholUnauthorizedSuccess()
    {
        $client = static::createClient();
        $alcoholIri = $this->findIriBy(Alcohol::class, ['name' => 'Jameson']);
        $producerIri = $this->findIriBy(Producer::class, ['name' => 'Bacardi']);
        $imageIri = $this->findIriBy(Image::class, ['name' => 'Jameson']);


        $client->request('GET', $alcoholIri);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJsonContains(
            [
                "name" => "Jameson",
                "type" => "whiskey",
                "description" => "Tennessee whiskey",
                "producer" => $producerIri,
                "abv" => 37.5,
                "image" => $imageIri
            ],
        );
    }

    public function testGetOneAlcoholAuthorizedSuccess()
    {
        $token = $this->login();
        $client = static::createClient();
        $alcoholIri = $this->findIriBy(Alcohol::class, ['name' => 'Jameson']);
        $producerIri = $this->findIriBy(Producer::class, ['name' => 'Bacardi']);
        $imageIri = $this->findIriBy(Image::class, ['name' => 'Jameson']);


        $client->request(
            'GET',
            $alcoholIri,
            [
                'auth_bearer' => $token
            ]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJsonContains(
            [
                "name" => "Jameson",
                "type" => "whiskey",
                "description" => "Tennessee whiskey",
                "producer" => $producerIri,
                "abv" => 37.5,
                "image" => $imageIri
            ],
        );
    }

    public function testGetOneAlcoholWrongIDFail()
    {
        $client = static::createClient();
        $client->request('GET', '/alcohols/fa5e2591-0463-40c4-a32a-62a89df22549');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCreateAlcohol()
    {
        $token = $this->login();
        $client = static::createClient();
        $producerIri = $this->findIriBy(Producer::class, ['name' => 'Bacardi']);

        $client->request(
            'POST',
            '/api/images',
            [
                'auth_bearer' => $token,
                'json' => [
                    "name" => "Jameson 5",
                    "url" => "sameUrls.com"
                ]
            ],
        );

        $imageIri = $this->findIriBy(Image::class, ['name' => 'Jameson 5']);

        $client->request(
            'POST',
            '/api/alcohols',
            [
                'auth_bearer' => $token,
                'json' => [
                    "name" => "Jameson 5",
                    "type" => "whiskey",
                    "description" => "Tennessee whiskey",
                    "producer" => $producerIri,
                    "abv" => 37.5,
                    "image" => $imageIri
                ],
            ]
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertJsonContains(
            [
                "name" => "Jameson 5",
                "type" => "whiskey",
                "description" => "Tennessee whiskey",
                "producer" => $producerIri,
                "abv" => 37.5,
                "image" => $imageIri
            ]
        );
    }
    public function testCreateAlcoholUnauthorized()
    {
        $client = static::createClient();
        $producerIri = $this->findIriBy(Producer::class, ['name' => 'Bacardi']);

        $client->request(
            'POST',
            '/api/images',
            [
                'json' => [
                    "name" => "Jameson 5",
                    "url" => "sameUrls.com"
                ]
            ],
        );

        $imageIri = $this->findIriBy(Image::class, ['name' => 'Jameson 5']);

        $client->request(
            'POST',
            '/api/alcohols',
            [
                'json' => [
                    "name" => "Jameson 5",
                    "type" => "whiskey",
                    "description" => "Tennessee whiskey",
                    "producer" => $producerIri,
                    "abv" => 37.5,
                    "image" => $imageIri
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertJsonContains([
            'message' => 'JWT Token not found',
        ]);
    }

    public function testUpdateAlcohol()
    {
        $token = $this->login();
        $client = static::createClient();
        $alcoholIri = $this->findIriBy(Alcohol::class, ['name' => 'Jameson']);

        $client->request(
            'PUT',
            $alcoholIri,
            [
                'auth_bearer' => $token,
                'json' => [
                    "name" => "Test update"
                ]
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(
            [
                "name" => "Test update"
            ]
        );
    }

    public function testUpdateAlcoholUnauthorized()
    {
        $client = static::createClient();
        $alcoholIri = $this->findIriBy(Alcohol::class, ['name' => 'Jameson']);

        $client->request(
            'PUT',
            $alcoholIri,
            [
                'json' => [
                    "name" => "Test update"
                ]
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertJsonContains([
            'message' => 'JWT Token not found',
        ]);
    }

    public function testDeleteAlcohol()
    {
        $token = $this->login();
        $client = static::createClient();
        $alcoholIri = $this->findIriBy(Alcohol::class, ['name' => 'Jameson']);

        $client->request(
            'DELETE',
            $alcoholIri,
            [
                'auth_bearer' => $token
            ]
        );

        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

    public function testDeleteAlcoholUnauthorized()
    {
        $client = static::createClient();
        $alcoholIri = $this->findIriBy(Alcohol::class, ['name' => 'Jameson']);

        $client->request(
            'DELETE',
            $alcoholIri,
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertJsonContains([
            'message' => 'JWT Token not found',
        ]);
    }
}
