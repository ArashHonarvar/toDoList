<?php
declare(strict_types=1);

namespace App\Tests\Controller\Api;


use App\Controller\Api\ProgrammerController;

use App\Tests\ApiTestCase;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\JsonResponse;


class ApiTokenControllerTest extends ApiTestCase
{

    public function testShowToken()
    {

        $data = [
            'username' => 'ArashHonarvar',
            'email' => "arash.ho.13723@gmail.com",
            'password' => '123'
        ];

        $user = $this->createUser($data);

        $apiToken = $this->createToken($user);

        try {
            $response = $this->client->get("/token/" . $apiToken->getAccessToken());
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $this->debugResponse($response); // Body
            }
        }

        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyExists($response, 'accessToken');
        $this->asserter()->assertResponsePropertyExists($response, 'refreshToken');
    }

    public function testRefreshToken()
    {


        $data = [
            'username' => 'ArashHonarvar',
            'email' => "arash.ho.13723@gmail.com",
            'password' => '123'
        ];

        $user = $this->createUser($data);

        $apiToken = $this->createToken($user);

        try {
            $response = $this->client->post("/token/refresh", [
                'headers' => ['AUTH-REFRESH-TOKEN' => $apiToken->getRefreshToken()]
            ]);
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $this->debugResponse($response); // Body
            }
        }

        $this->assertEquals(201, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyExists($response, 'accessToken');
        $this->asserter()->assertResponsePropertyExists($response, 'refreshToken');
    }

}
