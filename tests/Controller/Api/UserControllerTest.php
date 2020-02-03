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

    public function testRegister()
    {
        $data = [
            'username' => 'Arash2',
            'email' => "arash.honarvar.13722@gmail.com",
            'password' => '123'
        ];

        try {
            $response = $this->client->post("/user/register", [
                'body' => json_encode($data)
            ]);
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                dump($this->debugResponse($response)); // Body
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

    public function testUpdate()
    {
        $data = [
            'username' => 'Arash3',
            'email' => "arash.honarvar.13723@gmail.com",
            'password' => '123'
        ];

        $this->createUser($data);

        $data = [
            'username' => 'Arash4',
            'email' => "arash.honarvar.13724@gmail.com",
            'password' => '123'
        ];

        try {
            $response = $this->client->put("/user/update", [
                'body' => json_encode($data),
                'headers' => ['AUTH-USERNAME' => 'Arash3', 'AUTH-PASSWORD' => '123']
            ]);
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                dump($this->debugResponse($response)); // Body
            }
        }

        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response, 'username', "Arash3");
        $this->asserter()->assertResponsePropertyEquals($response, 'email', 'arash.honarvar.13724@gmail.com');
    }

    public function testGenerateToken()
    {

        try {
            $response = $this->client->post("/user/token/generate", [
                'body' => json_encode([]),
                'headers' => ['AUTH-USERNAME' => 'Arash2', 'AUTH-PASSWORD' => '123']
            ]);
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                dump($this->debugResponse($response)); // Body
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

    public function testShow()
    {
        try {
            $response = $this->client->get("/user/j5wozkm4a680oo0wkoks0okwo8okccs", [
                'body' => json_encode([]),
            ]);
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                dump($this->debugResponse($response)); // Body
            }
        }

        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyDoesNotExist($response, 'id');
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
                dump($this->debugResponse($response)); // Body
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
