<?php
declare(strict_types=1);

namespace App\Tests\Controller\Api;


use App\Tests\ApiTestCase;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\JsonResponse;


class UserControllerTest extends ApiTestCase
{

    public function testRegister()
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
//                dump($this->debugResponse($response)); // Body
                dump((string)$response->getBody());
            }
        }

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testGenerateToken()
    {

        try {
            $response = $this->client->post("/user/token/generate", [
                'body' => json_encode([]),
                'headers' => ['AUTH-USERNAME' => 'Arash', 'AUTH-PASSWORD' => '123']
            ]);
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
//                dump($this->debugResponse($response)); // Body
                dump((string)$response->getBody());
            }
        }

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testShow()
    {

        try {
            $response = $this->client->post("/user/show", [
                'body' => json_encode([]),
                'headers' => ['AUTH-USERNAME' => 'Arash', 'AUTH-PASSWORD' => '123']
            ]);
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
//                dump($this->debugResponse($response)); // Body
                dump((string)$response->getBody());
            }
        }

        $this->assertEquals(201, $response->getStatusCode());
    }

}
