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
        try {
            $response = $this->client->get("/token/tepqy7z95c0w0c8skg84ggco80okw0/");
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
        try {
            $response = $this->client->post("/token/refresh", [
                'headers' => ['AUTH-REFRESH-TOKEN' => 'dmom6glaboggw0ksg8ok008skswkokg']
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
