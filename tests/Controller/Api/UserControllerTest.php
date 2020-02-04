<?php
declare(strict_types=1);

namespace App\Tests\Controller\Api;


use App\Entity\User\User;
use App\Tests\ApiTestCase;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PropertyAccess\PropertyAccess;


class UserControllerTest extends ApiTestCase
{

    public function testRegisterUser()
    {
        $data = [
            'username' => 'Arash',
            'email' => "arash.honarvar.1372@gmail.com",
            'password' => '123'
        ];

        try {
            $response = $this->client->post("/user/register", [
                'body' => json_encode($data)
            ]);
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $this->debugResponse($response); // Body
            }
        }

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($response->getHeader("Content-type")[0], "application/json");
        $this->asserter()->assertResponsePropertiesExist($response,
            [
                'username',
                'email',
                'createdAt'
            ]);
    }

    public function testUpdateUser()
    {
        $data = [
            'username' => 'ArashHonarvar',
            'email' => "arash.ho.13723@gmail.com",
            'password' => '123'
        ];

        $this->createUser($data);

        $data = [
            'email' => "arash.hon.1372@gmail.com",
        ];

        try {
            $response = $this->client->put("/user/update", [
                'body' => json_encode($data),
                'headers' => ['AUTH-USERNAME' => 'ArashHonarvar', 'AUTH-PASSWORD' => '123']
            ]);
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $this->debugResponse($response); // Body
            }
        }

        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response, 'username', "ArashHonarvar");
        $this->asserter()->assertResponsePropertyEquals($response, 'email', 'arash.hon.1372@gmail.com');
    }

    public function testGenerateToken()
    {

        $data = [
            'username' => 'ArashHonarvar',
            'email' => "arash.ho.13723@gmail.com",
            'password' => '123'
        ];

        $this->createUser($data);

        try {
            $response = $this->client->post("/user/token/generate", [
                'body' => json_encode([]),
                'headers' => ['AUTH-USERNAME' => 'ArashHonarvar', 'AUTH-PASSWORD' => '123']
            ]);
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $this->debugResponse($response); // Body
            }
        }

        $this->assertEquals(201, $response->getStatusCode());
        $this->asserter()->assertResponsePropertiesExist($response,
            [
                'accessToken',
                'refreshToken',
                'expiredAt'
            ]);
    }

    public function testShowUser()
    {
        $data = [
            'username' => 'ArashHonarvar',
            'email' => "arash.ho.13723@gmail.com",
            'password' => '123'
        ];

        $user = $this->createUser($data);

        $apiToken = $this->createToken($user);


        try {
            $response = $this->client->get("/user/" . $apiToken->getAccessToken(), [
                'body' => json_encode([]),
            ]);
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $this->debugResponse($response); // Body
            }
        }

        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyDoesNotExist($response, 'id');
        $this->asserter()->assertResponsePropertiesExist($response,
            [
                'email',
                'username',
            ]);
    }

    public function testInvalidJson()
    {

        $invalidJson = <<<EOF
            {
            "username" : "ArashTester",
            "email" : "test@test.com
            "password" : "123"
            }
EOF;


        try {
            $response = $this->client->post("/user/register", [
                'body' => $invalidJson
            ]);
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $this->debugResponse($response); // Body
            }
        }

        $this->assertEquals(400, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyContains(
            $response,
            "type",
            "invalid_body_format"
        );
    }

}
